<?php

namespace Tests\Feature;

use App\Domain\Appointments\DTOs\CreateAppointmentDTO;
use App\Domain\Appointments\Exceptions\SlotAlreadyBookedException;
use App\Domain\Appointments\Services\AppointmentBookingService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class ConcurrentBookingTest extends TestCase
{
    protected AppointmentBookingService $bookingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookingService = app(AppointmentBookingService::class);
    }

    public function test_prevents_double_booking_for_the_same_doctor_and_time_slot(): void
    {
        $dUser = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $dUser->id,
            'license_number' => 'DOC-CONCURRENCY',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Neurologia',
            'slug' => 'neurologia',
            'base_price' => 200.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        // Next Monday schedule
        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '14:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        // Patient A
        $pUserA = User::factory()->create();
        $patientA = Patient::create([
            'user_id' => $pUserA->id,
            'first_name' => 'Paciente',
            'last_name' => 'A',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0911111111',
            'phone' => '0991111111',
            'gender' => Gender::MALE,
        ]);

        // Patient B
        $pUserB = User::factory()->create();
        $patientB = Patient::create([
            'user_id' => $pUserB->id,
            'first_name' => 'Paciente',
            'last_name' => 'B',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0922222222',
            'phone' => '0992222222',
            'gender' => Gender::FEMALE,
        ]);

        $startAt = Carbon::now()->next(Carbon::MONDAY)->setTime(11, 0);
        $endAt = $startAt->copy()->addMinutes(30);

        $dtoA = new CreateAppointmentDTO(
            patientId: $patientA->id,
            doctorId: $doctor->id,
            specialtyId: $specialty->id,
            procedureId: null,
            startAt: $startAt,
            endAt: $endAt,
            price: 200.00,
            patientNotes: null,
            createdByUserId: $pUserA->id,
            paymentMethod: 'ONLINE'
        );

        // First attempt (Patient A) succeeds
        $appointmentA = $this->bookingService->book($dtoA);

        $this->assertNotNull($appointmentA);
        $this->assertEquals($startAt->toDateTimeString(), $appointmentA->start_at->toDateTimeString());

        // Second simultaneous attempt (Patient B) for the exact same slot must throw SlotAlreadyBookedException
        $this->expectException(SlotAlreadyBookedException::class);

        $dtoB = new CreateAppointmentDTO(
            patientId: $patientB->id,
            doctorId: $doctor->id,
            specialtyId: $specialty->id,
            procedureId: null,
            startAt: $startAt,
            endAt: $endAt,
            price: 200.00,
            patientNotes: null,
            createdByUserId: $pUserB->id,
            paymentMethod: 'ONLINE'
        );

        $this->bookingService->book($dtoB);
    }
}
