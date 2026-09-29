<?php

namespace Tests\Feature;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PatientAppointmentDetailAndActionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Patient $patient;
    protected Doctor $doctor;
    protected Specialty $specialty;
    protected Appointment $appointment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $this->user = User::create([
            'name' => 'Ana Sofía Mendoza',
            'email' => 'paciente@surhospital.com',
            'password' => Hash::make('password123'),
            'is_active' => true,
        ]);
        $this->user->assignRole('PATIENT');

        $this->patient = Patient::create([
            'user_id' => $this->user->id,
            'first_name' => 'Ana Sofía',
            'last_name' => 'Mendoza',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0958473621',
            'phone' => '+593 99 123 4567',
            'gender' => Gender::FEMALE,
        ]);

        $dUser = User::create([
            'name' => 'Dr. Carlos Soledispa',
            'email' => 'doc@surhospital.com',
            'password' => Hash::make('123'),
            'is_active' => true,
        ]);
        $dUser->assignRole('DOCTOR');

        $this->doctor = Doctor::create([
            'user_id' => $dUser->id,
            'license_number' => 'MSP-10293',
            'room_office' => 'Consultorio 204',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $this->specialty = Specialty::create([
            'name' => 'Cardiología',
            'slug' => 'cardiologia',
            'base_price' => 45.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $this->doctor->specialties()->attach($this->specialty->id);

        // Doctor Schedule for all days
        for ($day = 0; $day <= 6; $day++) {
            DoctorSchedule::create([
                'doctor_id' => $this->doctor->id,
                'day_of_week' => $day,
                'start_time' => '08:00',
                'end_time' => '17:00',
                'is_active' => true,
            ]);
        }

        $this->appointment = Appointment::create([
            'code' => 'SUR-PAC-001',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'specialty_id' => $this->specialty->id,
            'start_at' => now()->addDays(2)->setTime(10, 0),
            'end_at' => now()->addDays(2)->setTime(10, 30),
            'status' => AppointmentStatus::CONFIRMED,
            'price' => 45.00,
            'patient_notes' => 'Chequeo rutinario',
        ]);
    }

    public function test_patient_can_view_appointment_details(): void
    {
        $response = $this->actingAs($this->user)->get(route('patient.appointment.show', $this->appointment));
        $response->assertStatus(200);
        $response->assertSee('SUR-PAC-001');
        $response->assertSee('Dr. Carlos Soledispa');
        $response->assertSee('Cardiología');
        $response->assertSee('Consultorio 204');
    }

    public function test_patient_can_view_appointments_list(): void
    {
        $response = $this->actingAs($this->user)->get(route('patient.appointments'));
        $response->assertStatus(200);
        $response->assertSee('Mis Citas Médicas');
        $response->assertSee('SUR-PAC-001');
    }

    public function test_patient_can_fetch_available_slots_for_rescheduling(): void
    {
        $targetDate = now()->addDays(3)->format('Y-m-d');
        $response = $this->actingAs($this->user)->getJson(route('patient.appointment.slots', [
            'appointment' => $this->appointment,
            'date' => $targetDate,
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'date',
            'slots' => [
                '*' => ['time', 'label', 'start_at', 'end_at']
            ]
        ]);
    }

    public function test_patient_can_reschedule_appointment(): void
    {
        $targetDate = now()->addDays(3)->format('Y-m-d');
        $response = $this->actingAs($this->user)->post(route('patient.appointment.reschedule', $this->appointment), [
            'new_date' => $targetDate,
            'new_time' => '11:00',
            'reason' => 'Cambio por motivos laborales',
        ]);

        $response->assertRedirect();
        $this->appointment->refresh();
        $this->assertEquals(AppointmentStatus::RESCHEDULED, $this->appointment->status);
        $this->assertEquals('11:00', $this->appointment->start_at->format('H:i'));
    }

    public function test_patient_can_cancel_appointment(): void
    {
        $response = $this->actingAs($this->user)->post(route('patient.appointment.cancel', $this->appointment), [
            'reason' => 'No podré asistir a la cita',
        ]);

        $response->assertRedirect();
        $this->appointment->refresh();
        $this->assertEquals(AppointmentStatus::CANCELLED, $this->appointment->status);
        $this->assertEquals('No podré asistir a la cita', $this->appointment->cancelled_reason);
    }
}
