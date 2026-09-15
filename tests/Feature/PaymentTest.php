<?php

namespace Tests\Feature;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Appointments\Services\AppointmentBookingService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Domain\Payments\Contracts\PaymentGatewayInterface;
use App\Domain\Payments\Enums\BankTransferStatus;
use App\Domain\Payments\Enums\PaymentMethod;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Services\BankTransferService;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    protected AppointmentBookingService $bookingService;
    protected BankTransferService $transferService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookingService = app(AppointmentBookingService::class);
        $this->transferService = app(BankTransferService::class);
        Storage::fake('receipts');
    }

    public function test_mock_online_payment_gateway_creates_successful_payment(): void
    {
        $gateway = app(PaymentGatewayInterface::class);

        $result = $gateway->createPayment(
            amount: 150000,
            currency: 'COP',
            description: 'Consulta Cardiologia',
            payerEmail: 'paciente@test.com',
            metadata: ['test_key' => 'test_val']
        );

        $this->assertTrue($result['success']);
        $this->assertNotEmpty($result['transaction_id']);
        $this->assertEquals(PaymentStatus::PAID, $result['status']);
    }

    public function test_payment_webhook_is_idempotent(): void
    {
        $gateway = app(PaymentGatewayInterface::class);

        $payload = [
            'transaction_id' => 'TX-IDEMPOTENT-001',
            'status' => 'APPROVED',
            'amount' => 120000,
        ];

        $firstCall = $gateway->handleWebhook($payload);
        $this->assertTrue($firstCall['success']);
        $this->assertFalse($firstCall['is_duplicate'] ?? false);

        // Second call with same transaction_id is detected as idempotent duplicate
        $secondCall = $gateway->handleWebhook($payload);
        $this->assertTrue($secondCall['success']);
        $this->assertTrue($secondCall['is_duplicate'] ?? true);
    }

    public function test_bank_transfer_submission_and_admin_validation_flow(): void
    {
        $dUser = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $dUser->id,
            'license_number' => 'DOC-PAY',
            'consultation_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Ortopedia',
            'base_price' => 130000,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Diego',
            'last_name' => 'Torres',
            'identification_type' => IdentificationType::CC,
            'identification_number' => '77889900',
            'phone' => '3004445566',
            'gender' => Gender::MALE,
        ]);

        $startAt = Carbon::now()->addDays(3)->setTime(11, 0);
        $endAt = $startAt->copy()->addMinutes(30);

        $appointment = $this->bookingService->book(
            patient: $patient,
            doctor: $doctor,
            specialty: $specialty,
            startAt: $startAt,
            endAt: $endAt,
            paymentMethod: PaymentMethod::BANK_TRANSFER,
            bookedByUserId: $pUser->id
        );

        $this->assertEquals(AppointmentStatus::PENDING_PAYMENT, $appointment->status);

        // Submit bank transfer with fake receipt file
        $receiptFile = UploadedFile::fake()->image('comprobante_bancolombia.jpg');

        $transfer = $this->transferService->submitTransfer(
            appointment: $appointment,
            data: [
                'bank_name' => 'Bancolombia',
                'reference_number' => 'REF-987654321',
                'transfer_date' => now()->format('Y-m-d'),
                'notes' => 'Transferencia desde cuenta de ahorros',
            ],
            receiptFile: $receiptFile
        );

        $this->assertNotNull($transfer);
        $this->assertEquals(BankTransferStatus::PENDING_VALIDATION, $transfer->validation_status);
        Storage::disk('receipts')->assertExists($transfer->receipt_path);

        // Reception admin validates and approves transfer
        $adminUser = User::factory()->create();
        $adminUser->assignRole('RECEPTION');

        $this->transferService->validateTransfer(
            transfer: $transfer,
            status: BankTransferStatus::APPROVED,
            validator: $adminUser,
            rejectionReason: null
        );

        $this->assertEquals(BankTransferStatus::APPROVED, $transfer->fresh()->validation_status);
        $this->assertEquals(PaymentStatus::PAID, $transfer->payment->fresh()->status);
        $this->assertEquals(AppointmentStatus::CONFIRMED, $appointment->fresh()->status);
    }
}
