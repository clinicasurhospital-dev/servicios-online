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

        $this->specialty = Specialty::firstOrCreate(
            ['slug' => 'cardiologia-test-detail'],
            [
                'name' => 'Cardiología Test',
                'base_price' => 45.00,
                'default_duration_minutes' => 30,
                'is_active' => true,
            ]
        );

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

    public function test_patient_can_pay_with_card_and_confirms_automatically(): void
    {
        $pendingAppointment = Appointment::create([
            'code' => 'SUR-PAC-PAY-CARD',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'specialty_id' => $this->specialty->id,
            'start_at' => now()->addDays(2)->setTime(14, 0),
            'end_at' => now()->addDays(2)->setTime(14, 30),
            'status' => AppointmentStatus::PENDING_PAYMENT,
            'price' => 45.00,
        ]);

        $response = $this->actingAs($this->user)->post(route('patient.appointment.pay_card', $pendingAppointment), [
            'card_holder' => 'ANA SOFIA MENDOZA',
            'card_number' => '4532 8900 1234 5678',
            'card_expiry' => '12/28',
            'card_cvv' => '842',
        ]);

        $response->assertRedirect();
        $pendingAppointment->refresh();
        $this->assertEquals(AppointmentStatus::CONFIRMED, $pendingAppointment->status);
        $this->assertDatabaseHas('payments', [
            'appointment_id' => $pendingAppointment->id,
            'payment_method' => 'CREDIT_CARD',
            'status' => 'PAID',
        ]);
    }

    public function test_patient_can_pay_with_payment_link_and_confirms_automatically(): void
    {
        $pendingAppointment = Appointment::create([
            'code' => 'SUR-PAC-PAY-LINK',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'specialty_id' => $this->specialty->id,
            'start_at' => now()->addDays(2)->setTime(15, 0),
            'end_at' => now()->addDays(2)->setTime(15, 30),
            'status' => AppointmentStatus::PENDING_PAYMENT,
            'price' => 45.00,
        ]);

        $response = $this->actingAs($this->user)->post(route('patient.appointment.pay_link', $pendingAppointment), [
            'gateway_provider' => 'Wompi / Bancolombia',
            'payment_reference' => 'WOMPI-998877',
        ]);

        $response->assertRedirect();
        $pendingAppointment->refresh();
        $this->assertEquals(AppointmentStatus::CONFIRMED, $pendingAppointment->status);
        $this->assertDatabaseHas('payments', [
            'appointment_id' => $pendingAppointment->id,
            'payment_method' => 'PAYMENT_LINK',
            'status' => 'PAID',
        ]);
    }

    public function test_patient_submitting_bank_transfer_stays_in_pending_validation(): void
    {
        \Illuminate\Support\Facades\Storage::fake('receipts');

        $pendingAppointment = Appointment::create([
            'code' => 'SUR-PAC-PAY-TRANS',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'specialty_id' => $this->specialty->id,
            'start_at' => now()->addDays(2)->setTime(16, 0),
            'end_at' => now()->addDays(2)->setTime(16, 30),
            'status' => AppointmentStatus::PENDING_PAYMENT,
            'price' => 45.00,
        ]);

        $receipt = \Illuminate\Http\UploadedFile::fake()->create('comprobante.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('patient.transfer.submit', $pendingAppointment), [
            'bank_name' => 'Banco Pichincha',
            'reference_number' => 'REF-98765432',
            'transfer_date' => now()->format('Y-m-d'),
            'receipt' => $receipt,
            'notes' => 'Pago realizado desde banca web',
        ]);

        $response->assertRedirect();
        $pendingAppointment->refresh();
        $this->assertEquals(AppointmentStatus::PENDING_PAYMENT, $pendingAppointment->status);
        $this->assertDatabaseHas('bank_transfers', [
            'reference_number' => 'REF-98765432',
            'validation_status' => 'PENDING_VALIDATION',
        ]);

        // When viewing detail, patient sees transfer validation notice
        $detailResponse = $this->actingAs($this->user)->get(route('patient.appointment.show', $pendingAppointment));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Transferencia Bancaria en Validación');
        $detailResponse->assertSee('REF-98765432');
    }
}
