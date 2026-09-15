<?php

namespace Tests\Feature;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class PatientAccessTest extends TestCase
{
    public function test_patient_cannot_view_or_cancel_another_patients_appointment(): void
    {
        // Doctor & Specialty
        $dUser = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $dUser->id,
            'license_number' => 'DOC-SEC',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Urologia',
            'slug' => 'urologia',
            'base_price' => 140.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        // Patient 1
        $user1 = User::factory()->create();
        $user1->assignRole('PATIENT');
        $patient1 = Patient::create([
            'user_id' => $user1->id,
            'first_name' => 'Paciente',
            'last_name' => 'Uno',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0910010011',
            'phone' => '0991001001',
            'gender' => Gender::MALE,
        ]);

        // Patient 2
        $user2 = User::factory()->create();
        $user2->assignRole('PATIENT');
        $patient2 = Patient::create([
            'user_id' => $user2->id,
            'first_name' => 'Paciente',
            'last_name' => 'Dos',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0920020022',
            'phone' => '0992002002',
            'gender' => Gender::FEMALE,
        ]);

        // Appointment belonging to Patient 1
        $app1 = Appointment::create([
            'code' => 'AP-SEC-001',
            'patient_id' => $patient1->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
            'start_at' => Carbon::now()->addDays(2)->setTime(10, 0),
            'end_at' => Carbon::now()->addDays(2)->setTime(10, 30),
            'status' => AppointmentStatus::CONFIRMED,
            'price' => 140.00,
        ]);

        // Patient 2 attempts to access Patient 1's appointment
        $response = $this->actingAs($user2)->get(route('patient.appointment.show', $app1));
        $response->assertStatus(403);

        // Patient 2 attempts to cancel Patient 1's appointment
        $cancelResponse = $this->actingAs($user2)
            ->post(route('patient.appointment.cancel', $app1), [
                'reason' => 'Intento malicioso de cancelación',
            ]);
        $cancelResponse->assertStatus(403);

        // Verify appointment was NOT cancelled
        $this->assertEquals(AppointmentStatus::CONFIRMED, $app1->fresh()->status);
    }
}
