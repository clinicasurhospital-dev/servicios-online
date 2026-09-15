<?php

namespace Tests\Feature;

use App\Domain\Appointments\Services\AppointmentBookingService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Domain\Payments\Enums\PaymentMethod;
use App\Models\Doctor;
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
            'consultation_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Neurologia',
            'base_price' => 200000,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        // Patient A
        $pUserA = User::factory()->create();
        $patientA = Patient::create([
            'user_id' => $pUserA->id,
            'first_name' => 'Paciente',
            'last_name' => 'A',
            'identification_type' => IdentificationType::CC,
            'identification_number' => '1111111',
            'phone' => '3001111111',
            'gender' => Gender::MALE,
        ]);

        // Patient B
        $pUserB = User::factory()->create();
        $patientB = Patient::create([
            'user_id' => $pUserB->id,
            'first_name' => 'Paciente',
            'last_name' => 'B',
            'identification_type' => IdentificationType::CC,
            'identification_number' => '2222222',
            'phone' => '3002222222',
            'gender' => Gender::FEMALE,
        ]);

        $startAt = Carbon::parse('next monday 11:00:00');
        $endAt = $startAt->copy()->addMinutes(30);

        // First attempt (Patient A) succeeds
        $appointmentA = $this->bookingService->book(
            patient: $patientA,
            doctor: $doctor,
            specialty: $specialty,
            startAt: $startAt,
            endAt: $endAt,
            paymentMethod: PaymentMethod::ONLINE,
            bookedByUserId: $pUserA->id
        );

        $this->assertNotNull($appointmentA);
        $this->assertEquals($startAt->toDateTimeString(), $appointmentA->start_at->toDateTimeString());

        // Second simultaneous attempt (Patient B) for the exact same slot must throw Exception
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El horario seleccionado ya no se encuentra disponible.');

        $this->bookingService->book(
            patient: $patientB,
            doctor: $doctor,
            specialty: $specialty,
            startAt: $startAt,
            endAt: $endAt,
            paymentMethod: PaymentMethod::ONLINE,
            bookedByUserId: $pUserB->id
        );
    }
}
