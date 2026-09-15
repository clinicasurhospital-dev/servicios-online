<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_user_can_authenticate_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect();
    }

    public function test_user_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_patient_can_register(): void
    {
        $response = $this->post('/registro', [
            'first_name' => 'Maria',
            'last_name' => 'Gomez',
            'identification_type' => 'CC',
            'identification_number' => '1020304050',
            'email' => 'maria.gomez@test.com',
            'phone' => '3009876543',
            'birth_date' => '1995-05-15',
            'gender' => 'FEMALE',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('patient.dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'maria.gomez@test.com']);
        $this->assertDatabaseHas('patients', ['identification_number' => '1020304050']);

        $user = User::where('email', 'maria.gomez@test.com')->first();
        $this->assertTrue($user->hasRole('PATIENT'));
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
