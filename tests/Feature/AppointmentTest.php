<?php

namespace Tests\Feature;

use App\Domain\Appointments\DTOs\CreateAppointmentDTO;
use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Appointments\Services\AppointmentBookingService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
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
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Dermatologia',
            'slug' => 'dermatologia',
            'base_price' => 120.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Laura',
            'last_name' => 'Restrepo',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0930507090',
            'phone' => '0995554433',
            'gender' => Gender::FEMALE,
        ]);

        $startAt = Carbon::now()->next(Carbon::MONDAY)->setTime(10, 0);
        $endAt = $startAt->copy()->addMinutes(30);

        // Active doctor schedule on Monday (Day 1)
        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '14:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $dto = new CreateAppointmentDTO(
            patientId: $patient->id,
            doctorId: $doctor->id,
            specialtyId: $specialty->id,
            procedureId: null,
            startAt: $startAt,
            endAt: $endAt,
            price: 120.00,
            patientNotes: 'Revision de lunares',
            createdByUserId: $pUser->id,
            paymentMethod: 'ONLINE'
        );

        $appointment = $this->bookingService->book($dto);

        $this->assertInstanceOf(Appointment::class, $appointment);
        $this->assertEquals(AppointmentStatus::PENDING_PAYMENT, $appointment->status);
        $this->assertEquals(120.00, $appointment->price);

        // Verify status history
        $this->assertDatabaseHas('appointment_status_histories', [
            'appointment_id' => $appointment->id,
            'new_status' => 'PENDING_PAYMENT',
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
            'license_number' => 'DOC-123-2',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Medicina General',
            'slug' => 'medicina-general',
            'base_price' => 80.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Andres',
            'last_name' => 'Castro',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0980123456',
            'phone' => '0995123456',
            'gender' => Gender::MALE,
        ]);

        $startAt = Carbon::now()->next(Carbon::TUESDAY)->setTime(14, 0);
        $endAt = $startAt->copy()->addMinutes(30);

        // Active doctor schedule on Tuesday (Day 2)
        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 2,
            'start_time' => '13:00',
            'end_time' => '18:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $dto = new CreateAppointmentDTO(
            patientId: $patient->id,
            doctorId: $doctor->id,
            specialtyId: $specialty->id,
            procedureId: null,
            startAt: $startAt,
            endAt: $endAt,
            price: 80.00,
            patientNotes: 'Chequeo general',
            createdByUserId: $pUser->id,
            paymentMethod: 'BANK_TRANSFER'
        );

        $appointment = $this->bookingService->book($dto);

        $this->assertEquals(AppointmentStatus::PENDING_PAYMENT, $appointment->status);
        $this->assertDatabaseHas('payments', [
            'appointment_id' => $appointment->id,
            'payment_method' => 'BANK_TRANSFER',
            'status' => 'PENDING',
        ]);
    }
}
