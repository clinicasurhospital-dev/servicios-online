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
use App\Models\BankTransfer;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

    public function test_bank_transfer_admin_rejection_flow(): void
    {
        $dUser = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $dUser->id,
            'license_number' => 'DOC-REJ',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);
        $specialty = Specialty::create([
            'name' => 'Pediatria',
            'slug' => 'pediatria-rej',
            'base_price' => 100.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);
        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Maria',
            'last_name' => 'Gomez',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0911223344',
            'phone' => '0995556677',
            'gender' => Gender::FEMALE,
        ]);

        $appointment = Appointment::create([
            'code' => 'SUR-TEST-REJ-01',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
            'start_at' => now()->addDays(3),
            'end_at' => now()->addDays(3)->addMinutes(30),
            'status' => AppointmentStatus::PENDING_PAYMENT,
            'price' => 100.00,
        ]);

        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'payment_method' => 'BANK_TRANSFER',
            'amount' => 100.00,
            'currency' => 'USD',
            'status' => PaymentStatus::PENDING,
        ]);

        $transfer = BankTransfer::create([
            'payment_id' => $payment->id,
            'bank_name' => 'Banco Guayaquil',
            'reference_number' => 'REF-REJECT-123',
            'receipt_path' => 'receipts/fake.jpg',
            'receipt_original_name' => 'fake.jpg',
            'amount' => 100.00,
            'transfer_date' => now()->format('Y-m-d'),
            'validation_status' => BankTransferStatus::PENDING_VALIDATION,
        ]);

        $adminUser = User::factory()->create();
        $adminUser->assignRole('RECEPTION');

        $this->transferService->validateTransfer(
            transfer: $transfer,
            validator: $adminUser,
            isApproved: false,
            rejectionReason: 'Comprobante ilegible o no encontrado'
        );

        $this->assertEquals(BankTransferStatus::REJECTED, $transfer->fresh()->validation_status);
        $this->assertEquals(PaymentStatus::REJECTED, $payment->fresh()->status);
        $this->assertEquals('Comprobante ilegible o no encontrado', $transfer->fresh()->rejection_reason);
    }

    public function test_bank_transfer_rejection_succeeds_even_when_appointment_is_orphaned(): void
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('ADMIN');

        // Create an orphaned payment where appointment does not exist
        DB::commit();
        Schema::disableForeignKeyConstraints();
        $payment = Payment::create([
            'appointment_id' => 999999,
            'payment_method' => 'BANK_TRANSFER',
            'amount' => 80.00,
            'currency' => 'USD',
            'status' => PaymentStatus::PENDING,
        ]);
        $transfer = BankTransfer::create([
            'payment_id' => $payment->id,
            'bank_name' => 'Banco Pichincha',
            'reference_number' => 'REF-ORPHAN-999',
            'receipt_path' => 'receipts/orphan.jpg',
            'receipt_original_name' => 'orphan.jpg',
            'amount' => 80.00,
            'transfer_date' => now()->format('Y-m-d'),
            'validation_status' => BankTransferStatus::PENDING_VALIDATION,
        ]);
        Schema::enableForeignKeyConstraints();
        DB::beginTransaction();

        // Rejection should complete without throwing foreign key or null appointment errors
        $this->transferService->validateTransfer(
            transfer: $transfer,
            validator: $adminUser,
            isApproved: false,
            rejectionReason: 'Cita médica no existe en el sistema'
        );

        $this->assertEquals(BankTransferStatus::REJECTED, $transfer->fresh()->validation_status);
        $this->assertEquals(PaymentStatus::REJECTED, $payment->fresh()->status);
    }

    public function test_admin_controller_reject_orphaned_transfer_http_flow(): void
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('ADMIN');

        DB::commit();
        Schema::disableForeignKeyConstraints();
        $payment = Payment::create([
            'appointment_id' => 888888,
            'payment_method' => 'BANK_TRANSFER',
            'amount' => 75.00,
            'currency' => 'USD',
            'status' => PaymentStatus::PENDING,
        ]);

        $transfer = BankTransfer::create([
            'payment_id' => $payment->id,
            'bank_name' => 'Banco Pacifico',
            'reference_number' => 'REF-ORPHAN-HTTP',
            'receipt_path' => 'receipts/test.jpg',
            'receipt_original_name' => 'test.jpg',
            'amount' => 75.00,
            'transfer_date' => now()->format('Y-m-d'),
            'validation_status' => BankTransferStatus::PENDING_VALIDATION,
        ]);
        Schema::enableForeignKeyConstraints();
        DB::beginTransaction();

        $response = $this->actingAs($adminUser)->post("/admin/transferencias/{$transfer->id}/validar", [
            'status' => 'REJECTED',
            'notes' => 'Comprobante no coincide con cita eliminada',
        ]);

        $response->assertSessionHas('success', 'Transferencia procesada correctamente.');
        $this->assertEquals(BankTransferStatus::REJECTED, $transfer->fresh()->validation_status);
        $this->assertEquals(PaymentStatus::REJECTED, $payment->fresh()->status);
    }

    public function test_admin_controller_approve_orphaned_transfer_returns_friendly_error(): void
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('ADMIN');

        DB::commit();
        Schema::disableForeignKeyConstraints();
        $payment = Payment::create([
            'appointment_id' => 777777,
            'payment_method' => 'BANK_TRANSFER',
            'amount' => 95.00,
            'currency' => 'USD',
            'status' => PaymentStatus::PENDING,
        ]);

        $transfer = BankTransfer::create([
            'payment_id' => $payment->id,
            'bank_name' => 'Banco Internacional',
            'reference_number' => 'REF-NO-APPT',
            'receipt_path' => 'receipts/intl.jpg',
            'receipt_original_name' => 'intl.jpg',
            'amount' => 95.00,
            'transfer_date' => now()->format('Y-m-d'),
            'validation_status' => BankTransferStatus::PENDING_VALIDATION,
        ]);
        Schema::enableForeignKeyConstraints();
        DB::beginTransaction();

        $response = $this->actingAs($adminUser)->post("/admin/transferencias/{$transfer->id}/validar", [
            'status' => 'APPROVED',
            'notes' => 'Intentando aprobar',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(BankTransferStatus::PENDING_VALIDATION, $transfer->fresh()->validation_status);
    }

    public function test_admin_sends_datalink_email_to_patient(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $adminUser = User::factory()->create();
        $adminUser->assignRole('ADMIN');

        $doctorUser = User::factory()->create();
        $doctorUser->assignRole('DOCTOR');
        $doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'license_number' => 'DOC-DL1',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Cardiologia DataLink',
            'slug' => 'cardiologia-datalink',
            'base_price' => 70.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $patientUser = User::factory()->create(['email' => 'paciente.datalink@test.com']);
        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'first_name' => 'Carlos',
            'last_name' => 'DataLink',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0912345678',
            'birth_date' => '1990-05-15',
            'gender' => Gender::MALE,
            'phone' => '0987654321',
        ]);

        $appointment = Appointment::create([
            'code' => 'SUR-TEST-DL-01',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
            'start_at' => now()->addDays(2)->setTime(10, 0),
            'end_at' => now()->addDays(2)->setTime(10, 30),
            'status' => AppointmentStatus::PENDING,
            'price' => 70.00,
        ]);

        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'payment_method' => 'PAYMENT_LINK',
            'amount' => 70.00,
            'currency' => 'USD',
            'status' => PaymentStatus::PENDING,
        ]);

        $dataLinkUrl = 'https://datalink.datafast.com.ec/pay/test123456';

        $response = $this->actingAs($adminUser)->post("/admin/transferencias/datalink/{$payment->id}/enviar-correo", [
            'datalink_url' => $dataLinkUrl,
            'notes' => 'Por favor realiza tu pago antes de las 18:00',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals($dataLinkUrl, $payment->fresh()->datalink_url);
        $this->assertNotNull($payment->fresh()->datalink_sent_at);

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\PatientDataLinkReadyMail::class, function ($mail) use ($patientUser, $dataLinkUrl) {
            return $mail->hasTo($patientUser->email) && $mail->dataLinkUrl === $dataLinkUrl;
        });
    }

    public function test_admin_marks_datalink_payment_as_paid_and_confirms_appointment(): void
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('ADMIN');

        $doctorUser = User::factory()->create();
        $doctorUser->assignRole('DOCTOR');
        $doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'license_number' => 'DOC-DL2',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Dermatologia DataLink',
            'slug' => 'dermatologia-datalink',
            'base_price' => 60.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $patientUser = User::factory()->create(['email' => 'paciente2@test.com']);
        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'first_name' => 'Maria',
            'last_name' => 'DataLink',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0923456789',
            'birth_date' => '1992-08-20',
            'gender' => Gender::FEMALE,
            'phone' => '0998765432',
        ]);

        $appointment = Appointment::create([
            'code' => 'SUR-TEST-DL-02',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
            'start_at' => now()->addDays(3)->setTime(11, 0),
            'end_at' => now()->addDays(3)->setTime(11, 30),
            'status' => AppointmentStatus::PENDING,
            'price' => 60.00,
        ]);

        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'payment_method' => 'PAYMENT_LINK',
            'amount' => 60.00,
            'currency' => 'USD',
            'status' => PaymentStatus::PENDING,
            'datalink_url' => 'https://datalink.datafast.com.ec/pay/paid123',
        ]);

        $response = $this->actingAs($adminUser)->post("/admin/transferencias/datalink/{$payment->id}/marcar-pagado");

        $response->assertSessionHas('success');
        $this->assertEquals(PaymentStatus::PAID, $payment->fresh()->status);
        $this->assertEquals(AppointmentStatus::CONFIRMED, $appointment->fresh()->status);
    }
}
