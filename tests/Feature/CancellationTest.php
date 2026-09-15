<?php

namespace Tests\Feature;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Appointments\Services\AppointmentBookingService;
use App\Domain\Appointments\Services\AppointmentCancellationService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Domain\Payments\Enums\PaymentMethod;
use App\Models\Doctor;
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
            'consultation_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Oftalmologia',
            'base_price' => 110000,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Sara',
            'last_name' => 'Diaz',
            'identification_type' => IdentificationType::CC,
            'identification_number' => '44332211',
            'phone' => '3007778899',
            'gender' => Gender::FEMALE,
        ]);

        $startAt = Carbon::now()->addDays(2)->setTime(15, 0);
        $endAt = $startAt->copy()->addMinutes(30);

        $appointment = $this->bookingService->book(
            patient: $patient,
            doctor: $doctor,
            specialty: $specialty,
            startAt: $startAt,
            endAt: $endAt,
            paymentMethod: PaymentMethod::ONLINE,
            bookedByUserId: $pUser->id
        );

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
