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

class DoctorAccessTest extends TestCase
{
    public function test_doctor_sees_only_own_appointments_in_agenda(): void
    {
        $this->withoutExceptionHandling();

        $specialty = Specialty::create([
            'name' => 'Psicologia',
            'slug' => 'psicologia',
            'base_price' => 90.00,
            'default_duration_minutes' => 45,
            'is_active' => true,
        ]);

        // Doctor 1
        $userDoc1 = User::factory()->create(['name' => 'Dr. Carlos']);
        $userDoc1->assignRole('DOCTOR');
        $doctor1 = Doctor::create([
            'user_id' => $userDoc1->id,
            'license_number' => 'DOC-001',
            'default_slot_duration_minutes' => 45,
            'is_active' => true,
        ]);

        // Doctor 2
        $userDoc2 = User::factory()->create(['name' => 'Dra. Andrea']);
        $userDoc2->assignRole('DOCTOR');
        $doctor2 = Doctor::create([
            'user_id' => $userDoc2->id,
            'license_number' => 'DOC-002',
            'default_slot_duration_minutes' => 45,
            'is_active' => true,
        ]);

        // Patient
        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Paciente',
            'last_name' => 'General',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0955544433',
            'phone' => '0990001111',
            'gender' => Gender::MALE,
        ]);

        $testDate = Carbon::today();

        // Appointment for Doctor 1
        $app1 = Appointment::create([
            'code' => 'AP-DOC1-001',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor1->id,
            'specialty_id' => $specialty->id,
            'start_at' => $testDate->copy()->setTime(9, 0),
            'end_at' => $testDate->copy()->setTime(9, 45),
            'status' => AppointmentStatus::CONFIRMED,
            'price' => 90.00,
        ]);

        // Appointment for Doctor 2
        $app2 = Appointment::create([
            'code' => 'AP-DOC2-002',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor2->id,
            'specialty_id' => $specialty->id,
            'start_at' => $testDate->copy()->setTime(11, 0),
            'end_at' => $testDate->copy()->setTime(11, 45),
            'status' => AppointmentStatus::CONFIRMED,
            'price' => 90.00,
        ]);

        // Doctor 1 accesses admin agenda
        $response = $this->actingAs($userDoc1)->get(route('admin.agenda', ['date' => $testDate->format('Y-m-d')]));
        $response->assertStatus(200);
        $response->assertSee('AP-DOC1-001');
        $response->assertDontSee('AP-DOC2-002');
    }
}
