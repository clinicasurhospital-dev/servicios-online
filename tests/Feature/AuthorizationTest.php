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
            'identification_type' => 'CC',
            'identification_number' => '123456',
            'phone' => '300000000',
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
}
