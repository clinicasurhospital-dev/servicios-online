<?php

namespace Tests\Feature;

use App\Domain\Appointments\DTOs\CreateAppointmentDTO;
use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Appointments\Services\AppointmentBookingService;
use App\Domain\Appointments\Services\AppointmentCancellationService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class CancellationTest extends TestCase
{
    protected AppointmentBookingService $bookingService;
    protected AppointmentCancellationService $cancellationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookingService = app(AppointmentBookingService::class);
        $this->cancellationService = app(AppointmentCancellationService::class);
    }

    public function test_can_cancel_scheduled_appointment(): void
    {
        $dUser = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $dUser->id,
            'license_number' => 'DOC-CANCEL',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Oftalmologia',
            'slug' => 'oftalmologia',
            'base_price' => 110.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $startAt = Carbon::now()->next(Carbon::MONDAY)->setTime(15, 0);
        $endAt = $startAt->copy()->addMinutes(30);

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1,
            'start_time' => '12:00',
            'end_time' => '18:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Sara',
            'last_name' => 'Diaz',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0944332211',
            'phone' => '0997778899',
            'gender' => Gender::FEMALE,
        ]);

        $dto = new CreateAppointmentDTO(
            patientId: $patient->id,
            doctorId: $doctor->id,
            specialtyId: $specialty->id,
            procedureId: null,
            startAt: $startAt,
            endAt: $endAt,
            price: 110.00,
            patientNotes: null,
            createdByUserId: $pUser->id,
            paymentMethod: 'ONLINE'
        );

        $appointment = $this->bookingService->book($dto);

        $cancelled = $this->cancellationService->cancel(
            appointment: $appointment,
            reason: 'Sintomas aliviados previamente',
            cancelledByUserId: $pUser->id
        );

        $this->assertEquals(AppointmentStatus::CANCELLED, $cancelled->status);
        $this->assertEquals('Sintomas aliviados previamente', $cancelled->cancelled_reason);
        $this->assertNotNull($cancelled->cancelled_at);

        $this->assertDatabaseHas('appointment_status_histories', [
            'appointment_id' => $appointment->id,
            'new_status' => 'CANCELLED',
        ]);
    }
}
