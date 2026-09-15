<?php

namespace Tests\Feature;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Appointments\Services\AppointmentBookingService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Domain\Payments\Enums\PaymentMethod;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    protected AppointmentBookingService $bookingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookingService = app(AppointmentBookingService::class);
    }

    public function test_patient_can_book_appointment_with_online_payment(): void
    {
        $dUser = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $dUser->id,
            'license_number' => 'DOC-123',
            'consultation_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Dermatologia',
            'base_price' => 120000,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Laura',
            'last_name' => 'Restrepo',
            'identification_type' => IdentificationType::CC,
            'identification_number' => '1030507090',
            'phone' => '3105554433',
            'gender' => Gender::FEMALE,
        ]);

        $startAt = Carbon::parse('next monday 10:00:00');
        $endAt = $startAt->copy()->addMinutes(30);

        $appointment = $this->bookingService->book(
            patient: $patient,
            doctor: $doctor,
            specialty: $specialty,
            startAt: $startAt,
            endAt: $endAt,
            reason: 'Revision de lunares',
            paymentMethod: PaymentMethod::ONLINE,
            bookedByUserId: $pUser->id
        );

        $this->assertInstanceOf(Appointment::class, $appointment);
        $this->assertEquals(AppointmentStatus::CONFIRMED, $appointment->status);
        $this->assertEquals(120000, $appointment->total_amount);

        // Verify status history
        $this->assertDatabaseHas('appointment_status_histories', [
            'appointment_id' => $appointment->id,
            'new_status' => 'CONFIRMED',
        ]);

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'AppointmentCreated',
            'auditable_id' => (string) $appointment->id,
        ]);
    }

    public function test_booking_with_bank_transfer_sets_status_to_pending_payment(): void
    {
        $dUser = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $dUser->id,
            'license_number' => 'DOC-123',
            'consultation_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Medicina General',
            'base_price' => 80000,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Andres',
            'last_name' => 'Castro',
            'identification_type' => IdentificationType::CC,
            'identification_number' => '80123456',
            'phone' => '3151234567',
            'gender' => Gender::MALE,
        ]);

        $startAt = Carbon::parse('next tuesday 14:00:00');
        $endAt = $startAt->copy()->addMinutes(30);

        $appointment = $this->bookingService->book(
            patient: $patient,
            doctor: $doctor,
            specialty: $specialty,
            startAt: $startAt,
            endAt: $endAt,
            reason: 'Chequeo general',
            paymentMethod: PaymentMethod::BANK_TRANSFER,
            bookedByUserId: $pUser->id
        );

        $this->assertEquals(AppointmentStatus::PENDING_PAYMENT, $appointment->status);
        $this->assertDatabaseHas('payments', [
            'appointment_id' => $appointment->id,
            'method' => 'BANK_TRANSFER',
            'status' => 'PENDING',
        ]);
    }
}
