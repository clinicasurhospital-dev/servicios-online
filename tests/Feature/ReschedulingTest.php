<?php

namespace Tests\Feature;

use App\Domain\Appointments\DTOs\CreateAppointmentDTO;
use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Appointments\Exceptions\InvalidReschedulePolicyException;
use App\Domain\Appointments\Services\AppointmentBookingService;
use App\Domain\Appointments\Services\AppointmentRescheduleService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class ReschedulingTest extends TestCase
{
    protected AppointmentBookingService $bookingService;
    protected AppointmentRescheduleService $rescheduleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookingService = app(AppointmentBookingService::class);
        $this->rescheduleService = app(AppointmentRescheduleService::class);
    }

    public function test_can_reschedule_appointment_when_meeting_policy(): void
    {
        $dUser = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $dUser->id,
            'license_number' => 'DOC-RESCHEDULE',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Pediatria',
            'slug' => 'pediatria',
            'base_price' => 100.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        // Schedules for Monday and Tuesday
        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '16:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);
        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 2,
            'start_time' => '08:00',
            'end_time' => '16:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Felipe',
            'last_name' => 'Mora',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0955667788',
            'phone' => '0990998877',
            'gender' => Gender::MALE,
        ]);

        // Initial appointment on next Monday 09:00 (> 24h away)
        $initialStartAt = Carbon::now()->next(Carbon::MONDAY)->setTime(9, 0);
        $initialEndAt = $initialStartAt->copy()->addMinutes(30);

        $dto = new CreateAppointmentDTO(
            patientId: $patient->id,
            doctorId: $doctor->id,
            specialtyId: $specialty->id,
            procedureId: null,
            startAt: $initialStartAt,
            endAt: $initialEndAt,
            price: 100.00,
            patientNotes: null,
            createdByUserId: $pUser->id,
            paymentMethod: 'ONLINE'
        );

        $appointment = $this->bookingService->book($dto);

        // Reschedule to next Tuesday 10:00
        $newStartAt = Carbon::now()->next(Carbon::TUESDAY)->setTime(10, 0);
        $newEndAt = $newStartAt->copy()->addMinutes(30);

        $rescheduled = $this->rescheduleService->reschedule(
            appointment: $appointment,
            newStartAt: $newStartAt,
            newEndAt: $newEndAt,
            userId: $pUser->id,
            reason: 'Viaje imprevisto de trabajo'
        );

        $this->assertEquals($newStartAt->toDateTimeString(), $rescheduled->start_at->toDateTimeString());
        $this->assertEquals(AppointmentStatus::RESCHEDULED, $rescheduled->status);
        $this->assertDatabaseHas('appointment_status_histories', [
            'appointment_id' => $appointment->id,
            'new_status' => 'RESCHEDULED',
        ]);
    }

    public function test_cannot_reschedule_if_violating_minimum_hours_policy(): void
    {
        $dUser = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $dUser->id,
            'license_number' => 'DOC-RESCHEDULE-2',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Pediatria General',
            'slug' => 'pediatria-general',
            'base_price' => 100.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        // Schedule for today and tomorrow
        $todayDayOfWeek = Carbon::now()->dayOfWeek;
        $tomorrowDayOfWeek = Carbon::now()->addDay()->dayOfWeek;

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => $todayDayOfWeek,
            'start_time' => '00:00',
            'end_time' => '23:59',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => $tomorrowDayOfWeek,
            'start_time' => '00:00',
            'end_time' => '23:59',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Felipe',
            'last_name' => 'Mora',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0955667788',
            'phone' => '0990998877',
            'gender' => Gender::MALE,
        ]);

        // Cita en 2 horas (viola política de 24h mínimas)
        $initialStartAt = Carbon::now()->addHours(2);
        $initialEndAt = $initialStartAt->copy()->addMinutes(30);

        $dto = new CreateAppointmentDTO(
            patientId: $patient->id,
            doctorId: $doctor->id,
            specialtyId: $specialty->id,
            procedureId: null,
            startAt: $initialStartAt,
            endAt: $initialEndAt,
            price: 100.00,
            patientNotes: null,
            createdByUserId: $pUser->id,
            paymentMethod: 'ONLINE'
        );

        $appointment = $this->bookingService->book($dto);

        $newStartAt = Carbon::now()->addDays(5)->setTime(10, 0);
        $newEndAt = $newStartAt->copy()->addMinutes(30);

        $this->expectException(InvalidReschedulePolicyException::class);

        $this->rescheduleService->reschedule(
            appointment: $appointment,
            newStartAt: $newStartAt,
            newEndAt: $newEndAt,
            userId: $pUser->id,
            reason: 'Tarde para avisar'
        );
    }
}
