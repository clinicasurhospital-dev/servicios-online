<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    public function test_guest_is_redirected_to_login_when_accessing_admin_or_patient_portal(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/paciente')->assertRedirect('/login');
    }

    public function test_patient_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('PATIENT');
        Patient::create([
            'user_id' => $user->id,
            'first_name' => 'Test',
            'last_name' => 'Patient',
            'identification_type' => 'CEDULA',
            'identification_number' => '0912345678',
            'phone' => '0990000000',
            'birth_date' => '1990-01-01',
            'gender' => 'OTHER',
        ]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('ADMIN');

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_super_admin_bypasses_all_permission_checks(): void
    {
        $user = User::factory()->create();
        $user->assignRole('SUPER_ADMIN');

        $this->actingAs($user)->get('/admin')->assertStatus(200);
        $this->actingAs($user)->get('/admin/citas')->assertStatus(200);
        $this->actingAs($user)->get('/admin/auditoria')->assertStatus(200);
    }

    public function test_public_booking_disabled_restricts_guest_and_patient_but_allows_admin_and_reception(): void
    {
        config(['branding.enable_patient_booking' => false]);

        // Guest is redirected
        $this->get('/agendar')->assertRedirect('/')->assertSessionHas('info');

        // Patient is redirected
        $patientUser = User::factory()->create();
        $patientUser->assignRole('PATIENT');
        $this->actingAs($patientUser)->get('/agendar')->assertRedirect('/')->assertSessionHas('info');

        // Admin can access
        $adminUser = User::factory()->create();
        $adminUser->assignRole('ADMIN');
        $this->actingAs($adminUser)->get('/agendar')->assertStatus(200);

        // Receptionist can access
        $receptionUser = User::factory()->create();
        $receptionUser->assignRole('RECEPTION');
        $this->actingAs($receptionUser)->get('/agendar')->assertStatus(200);
    }

    public function test_public_booking_enabled_allows_everyone_to_access_and_see_button(): void
    {
        config(['branding.enable_patient_booking' => true]);

        // Guest can access
        $this->get('/agendar')->assertStatus(200);

        // Guest sees the button on home
        $this->get('/')->assertSee('Agendar Cita en Línea');
    }
}
