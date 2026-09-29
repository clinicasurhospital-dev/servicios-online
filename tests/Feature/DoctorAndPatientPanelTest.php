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
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DoctorAndPatientPanelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_registered_patient_can_authenticate_with_password123(): void
    {
        $user = User::create([
            'name' => 'Ana Sofía Mendoza',
            'email' => 'paciente@surhospital.com',
            'phone' => '+593 99 123 4567',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $user->assignRole('PATIENT');

        $response = $this->post(route('login'), [
            'email' => 'paciente@surhospital.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('patient.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_patient_can_view_patient_portal_dashboard(): void
    {
        $user = User::create([
            'name' => 'Ana Sofía Mendoza',
            'email' => 'paciente@surhospital.com',
            'phone' => '+593 99 123 4567',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $user->assignRole('PATIENT');

        $patient = Patient::create([
            'user_id' => $user->id,
            'first_name' => 'Ana Sofía',
            'last_name' => 'Mendoza',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0958473621',
            'phone' => '+593 99 123 4567',
            'gender' => Gender::FEMALE,
        ]);

        $response = $this->actingAs($user)->get(route('patient.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Portal del Paciente');
        $response->assertSee('Ana Sofía');
    }

    public function test_doctor_sees_panel_medico_instead_of_panel_administrativo(): void
    {
        $dUser = User::create([
            'name' => 'Dr. Carlos Soledispa',
            'email' => 'carlos.soledispa@surhospital.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $dUser->assignRole('DOCTOR');

        $doctor = Doctor::create([
            'user_id' => $dUser->id,
            'license_number' => 'MED-12345',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $response = $this->actingAs($dUser)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Panel Médico');
        $response->assertDontSee('Panel Administrativo');
    }
}
