<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use App\Notifications\NewPatientWelcomeNotification;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserAndSecurityModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_all_five_required_roles_exist(): void
    {
        $this->assertTrue(Role::where('name', 'ADMIN')->exists());
        $this->assertTrue(Role::where('name', 'RECEPTION')->exists());
        $this->assertTrue(Role::where('name', 'DOCTOR')->exists());
        $this->assertTrue(Role::where('name', 'SPECIALIST')->exists());
        $this->assertTrue(Role::where('name', 'PATIENT')->exists());
    }

    public function test_user_model_role_helper_methods(): void
    {
        $doctor = User::factory()->create();
        $doctor->assignRole('DOCTOR');
        $this->assertTrue($doctor->isDoctor());
        $this->assertFalse($doctor->isSpecialist());

        $specialist = User::factory()->create();
        $specialist->assignRole('SPECIALIST');
        $this->assertTrue($specialist->isDoctor());
        $this->assertTrue($specialist->isSpecialist());

        $reception = User::factory()->create();
        $reception->assignRole('RECEPTION');
        $this->assertTrue($reception->isReception());
        $this->assertFalse($reception->isAdmin());

        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');
        $this->assertTrue($admin->isAdmin());

        $patient = User::factory()->create();
        $patient->assignRole('PATIENT');
        $this->assertTrue($patient->isPatient());
    }

    public function test_admin_can_access_users_index(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');

        $response = $this->actingAs($admin)->get(route('admin.users'));
        $response->assertStatus(200);
        $response->assertSee('Módulo General de Usuarios');
    }

    public function test_admin_can_access_security_index(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');

        $response = $this->actingAs($admin)->get(route('admin.security'));
        $response->assertStatus(200);
        $response->assertSee('Módulo de Seguridad & Perfiles');
    }

    public function test_registering_patient_via_patient_controller_generates_generic_password_and_sends_email(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');

        $response = $this->actingAs($admin)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('admin.patients.store'), [
                'first_name' => 'María',
                'last_name' => 'González',
                'identification_type' => 'CEDULA',
                'identification_number' => '0928374651',
                'email' => 'maria.gonzalez@test.com',
                'phone' => '0998877665',
                'birth_date' => '1995-05-12',
                'gender' => 'FEMALE',
            ]);

        $response->assertSessionHas('success');

        $user = User::where('email', 'maria.gonzalez@test.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('SurHospital2026!', $user->password));
        $this->assertTrue($user->hasRole('PATIENT'));

        $patient = Patient::where('user_id', $user->id)->first();
        $this->assertNotNull($patient);
        $this->assertEquals('0928374651', $patient->identification_number);

        Notification::assertSentTo($user, NewPatientWelcomeNotification::class, function ($notification) {
            return $notification->genericPassword === 'SurHospital2026!';
        });
    }

    public function test_registering_specialist_via_doctor_controller_creates_user_with_generic_password(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');

        $specialty = Specialty::create([
            'name' => 'Cardiología Pediátrica',
            'slug' => 'cardiologia-pediatrica',
            'base_price' => 50.00,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('admin.doctors.store'), [
                'name' => 'Dr. Fernando Castro',
                'email' => 'dr.castro@surhospital.com',
                'phone' => '0991122334',
                'role' => 'SPECIALIST',
                'license_number' => 'MSP-CARDIO-8821',
                'room_office' => 'Consultorio 301',
                'default_consultation_price' => 50.00,
                'default_slot_duration_minutes' => 30,
                'specialty_ids' => [$specialty->id],
            ]);

        $response->assertSessionHas('success');

        $user = User::where('email', 'dr.castro@surhospital.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('SurHospital2026!', $user->password));
        $this->assertTrue($user->hasRole('SPECIALIST'));
        $this->assertTrue($user->isDoctor());
        $this->assertTrue($user->isSpecialist());

        $doctor = Doctor::where('user_id', $user->id)->first();
        $this->assertNotNull($doctor);
        $this->assertEquals('MSP-CARDIO-8821', $doctor->license_number);
        $this->assertTrue($doctor->specialties->contains($specialty->id));
        $this->assertEquals('Cardiología Pediátrica', $doctor->primary_specialty_name);
    }

    public function test_admin_can_create_user_with_role_in_users_controller(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');

        // Create a Receptionist
        $response = $this->actingAs($admin)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('admin.users.store'), [
                'name' => 'Lucía Recepción',
                'email' => 'lucia@surhospital.com',
                'phone' => '0993344556',
                'role' => 'RECEPTION',
                'password' => 'SurHospital2026!',
            ]);

        $response->assertSessionHas('success');

        $reception = User::where('email', 'lucia@surhospital.com')->first();
        $this->assertNotNull($reception);
        $this->assertTrue($reception->hasRole('RECEPTION'));
        $this->assertTrue($reception->isReception());
    }

    public function test_security_controller_can_update_role_permissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');

        $doctorRole = Role::findByName('DOCTOR', 'web');

        $response = $this->actingAs($admin)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->put(route('admin.security.role_permissions', $doctorRole), [
                'permissions' => ['view_dashboard', 'view_doctor_own_agenda'],
            ]);

        $response->assertSessionHas('success');

        $this->assertTrue($doctorRole->hasPermissionTo('view_dashboard'));
        $this->assertTrue($doctorRole->hasPermissionTo('view_doctor_own_agenda'));
        $this->assertFalse($doctorRole->hasPermissionTo('manage_users'));
    }

    public function test_security_controller_can_assign_user_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');

        $targetUser = User::factory()->create();
        $targetUser->assignRole('PATIENT');

        $response = $this->actingAs($admin)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route('admin.security.assign_role', $targetUser), [
                'role' => 'RECEPTION',
            ]);

        $response->assertSessionHas('success');
        $this->assertTrue($targetUser->fresh()->hasRole('RECEPTION'));
    }

    public function test_specialty_controller_can_store_and_update_with_long_description(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole("ADMIN");

        $longDescription = trim(str_repeat("Especialidad médica integral para diagnóstico clínico y tratamiento avanzado. ", 15));

        $response = $this->actingAs($admin)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->post(route("admin.specialties.store"), [
                "name" => "Dermatología Avanzada",
                "description" => $longDescription,
                "base_price" => 45.00,
                "default_duration_minutes" => 30,
                "has_procedures" => false,
            ]);

        $response->assertSessionHas("success");
        $specialty = Specialty::where("name", "Dermatología Avanzada")->first();
        $this->assertNotNull($specialty);
        $this->assertEquals($longDescription, $specialty->description);

        $responseUpdate = $this->actingAs($admin)
            ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class)
            ->put(route("admin.specialties.update", $specialty), [
                "name" => "Dermatología Quirúrgica y Clínica",
                "description" => $longDescription . " Actualizado.",
                "base_price" => 55.00,
                "default_duration_minutes" => 30,
                "has_procedures" => false,
            ]);

        $responseUpdate->assertSessionHas("success");
        $this->assertEquals("Dermatología Quirúrgica y Clínica", $specialty->fresh()->name);
    }
}
