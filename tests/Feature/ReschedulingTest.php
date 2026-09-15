<?php

namespace Tests\Feature;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Appointments\Services\AppointmentBookingService;
use App\Domain\Appointments\Services\AppointmentRescheduleService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Domain\Payments\Enums\PaymentMethod;
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
            'consultation_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Pediatria',
            'base_price' => 100000,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Felipe',
            'last_name' => 'Mora',
            'identification_type' => IdentificationType::CC,
            'identification_number' => '55667788',
            'phone' => '3209998877',
            'gender' => Gender::MALE,
        ]);

        // Cita programada para dentro de 4 días (cumple con holgura la política de >24h)
        $initialStartAt = Carbon::now()->addDays(4)->setTime(9, 0);
        $initialEndAt = $initialStartAt->copy()->addMinutes(30);

        $appointment = $this->bookingService->book(
            patient: $patient,
            doctor: $doctor,
            specialty: $specialty,
            startAt: $initialStartAt,
            endAt: $initialEndAt,
            paymentMethod: PaymentMethod::ONLINE,
            bookedByUserId: $pUser->id
        );

        $newStartAt = Carbon::now()->addDays(5)->setTime(10, 0);
        $newEndAt = $newStartAt->copy()->addMinutes(30);

        $rescheduled = $this->rescheduleService->reschedule(
            appointment: $appointment,
            newStartAt: $newStartAt,
            newEndAt: $newEndAt,
            reason: 'Viaje imprevisto de trabajo',
            rescheduledByUserId: $pUser->id
        );

        $this->assertEquals($newStartAt->toDateTimeString(), $rescheduled->start_at->toDateTimeString());
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
            'consultation_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Pediatria',
            'base_price' => 100000,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Felipe',
            'last_name' => 'Mora',
            'identification_type' => IdentificationType::CC,
            'identification_number' => '55667788',
            'phone' => '3209998877',
            'gender' => Gender::MALE,
        ]);

        // Cita en 2 horas (viola política de 24h mínimas)
        $initialStartAt = Carbon::now()->addHours(2);
        $initialEndAt = $initialStartAt->copy()->addMinutes(30);

        $appointment = $this->bookingService->book(
            patient: $patient,
            doctor: $doctor,
            specialty: $specialty,
            startAt: $initialStartAt,
            endAt: $initialEndAt,
            paymentMethod: PaymentMethod::ONLINE,
            bookedByUserId: $pUser->id
        );

        $newStartAt = Carbon::now()->addDays(5)->setTime(10, 0);
        $newEndAt = $newStartAt->copy()->addMinutes(30);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No es posible reprogramar con menos de 24 horas de antelación.');

        $this->rescheduleService->reschedule(
            appointment: $appointment,
            newStartAt: $newStartAt,
            newEndAt: $newEndAt,
            reason: 'Tarde para avisar',
            rescheduledByUserId: $pUser->id
        );
    }
}
