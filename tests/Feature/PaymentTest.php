<?php

namespace Tests\Feature;

use App\Domain\Appointments\DTOs\CreateAppointmentDTO;
use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Appointments\Services\AppointmentBookingService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Domain\Payments\Contracts\PaymentGatewayInterface;
use App\Domain\Payments\DTOs\PaymentCreationDTO;
use App\Domain\Payments\Enums\BankTransferStatus;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Services\BankTransferService;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        $dto = new PaymentCreationDTO(
            appointmentId: 1,
            amount: 150.00,
            currency: 'USD',
            patientEmail: 'paciente@test.com',
            patientName: 'Juan Perez',
            description: 'Consulta Cardiologia'
        );

        $result = $gateway->createPayment($dto);

        $this->assertTrue($result->isSuccessful);
        $this->assertNotEmpty($result->gatewayTransactionId);
        $this->assertNotEmpty($result->redirectUrl);
    }

    public function test_payment_webhook_is_idempotent(): void
    {
        $gateway = app(PaymentGatewayInterface::class);

        $user = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $user->id,
            'first_name' => 'Idem',
            'last_name' => 'Potent',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0998811223',
            'phone' => '0991234567',
            'gender' => Gender::MALE,
        ]);
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'license_number' => 'DOC-IDEM',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);
        $specialty = Specialty::create([
            'name' => 'Cardiologia',
            'slug' => 'cardiologia-idem',
            'base_price' => 120.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $appointment = Appointment::create([
            'code' => 'SUR-TEST-IDEM-01',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
            'start_at' => now()->addDays(2),
            'end_at' => now()->addDays(2)->addMinutes(30),
            'status' => AppointmentStatus::PENDING_PAYMENT,
            'price' => 120.00,
        ]);

        Payment::create([
            'appointment_id' => $appointment->id,
            'payment_method' => 'ONLINE',
            'amount' => 120.00,
            'currency' => 'USD',
            'status' => PaymentStatus::PENDING,
            'gateway_transaction_id' => 'TX-IDEMPOTENT-001',
        ]);

        $request = Request::create('/api/webhooks/payment', 'POST', [
            'transaction_id' => 'TX-IDEMPOTENT-001',
            'status' => 'PAID',
            'amount' => 120.00,
        ]);

        $firstCall = $gateway->handleWebhook($request);
        $this->assertTrue($firstCall->isValid);
        $this->assertEquals(PaymentStatus::PAID, $firstCall->paymentStatus);

        // Second call with same transaction_id is handled idempotently
        $secondCall = $gateway->handleWebhook($request);
        $this->assertTrue($secondCall->isValid);
        $this->assertEquals(PaymentStatus::PAID, $secondCall->paymentStatus);
    }

    public function test_bank_transfer_submission_and_admin_validation_flow(): void
    {
        $dUser = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $dUser->id,
            'license_number' => 'DOC-PAY',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Ortopedia',
            'slug' => 'ortopedia',
            'base_price' => 130.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '16:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Diego',
            'last_name' => 'Torres',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0977889900',
            'phone' => '0994445566',
            'gender' => Gender::MALE,
        ]);

        $startAt = Carbon::now()->next(Carbon::MONDAY)->setTime(11, 0);
        $endAt = $startAt->copy()->addMinutes(30);

        $dto = new CreateAppointmentDTO(
            patientId: $patient->id,
            doctorId: $doctor->id,
            specialtyId: $specialty->id,
            procedureId: null,
            startAt: $startAt,
            endAt: $endAt,
            price: 130.00,
            patientNotes: null,
            createdByUserId: $pUser->id,
            paymentMethod: 'BANK_TRANSFER'
        );

        $appointment = $this->bookingService->book($dto);

        $this->assertEquals(AppointmentStatus::PENDING_PAYMENT, $appointment->status);

        // Submit bank transfer with fake receipt file
        $receiptFile = UploadedFile::fake()->image('comprobante_bolivariano.jpg');

        $transfer = $this->transferService->submitTransfer(
            appointment: $appointment,
            data: [
                'bank_name' => 'Banco Bolivariano',
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
            validator: $adminUser,
            isApproved: true,
            rejectionReason: null
        );

        $this->assertEquals(BankTransferStatus::APPROVED, $transfer->fresh()->validation_status);
        $this->assertEquals(PaymentStatus::PAID, $transfer->payment->fresh()->status);
        $this->assertEquals(AppointmentStatus::CONFIRMED, $appointment->fresh()->status);
    }
}
