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

        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post('/login', [
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

        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);

        $this->assertGuest();
    }

    public function test_patient_can_register(): void
    {
        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post('/registro', [
                'name' => 'Maria Gomez',
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

        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->actingAs($user)
            ->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/recuperar-contrasena');
        $response->assertStatus(200);
        $response->assertSee('Recuperar Contraseña');
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        \Illuminate\Support\Facades\Notification::fake();

        $user = User::factory()->create([
            'email' => 'paciente.recuperar@surhospital.com',
        ]);

        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post('/recuperar-contrasena', [
                'email' => 'paciente.recuperar@surhospital.com',
            ]);

        $response->assertSessionHas('status');
        \Illuminate\Support\Facades\Notification::assertSentTo(
            $user,
            \App\Notifications\ResetPasswordNotification::class
        );
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/restablecer-contrasena/sample-token?email=paciente.recuperar@surhospital.com');
        $response->assertStatus(200);
        $response->assertSee('Nueva Contraseña');
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'reset.user@surhospital.com',
            'password' => bcrypt('old-password-123'),
        ]);

        $token = \Illuminate\Support\Facades\Password::createToken($user);

        $response = $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post('/restablecer-contrasena', [
                'token' => $token,
                'email' => 'reset.user@surhospital.com',
                'password' => 'new-secure-password-456',
                'password_confirmation' => 'new-secure-password-456',
            ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('new-secure-password-456', $user->password));
    }
}
