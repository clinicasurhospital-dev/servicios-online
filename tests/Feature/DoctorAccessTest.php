<?php

namespace Tests\Feature;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Livewire\BookingWizard;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class DoctorAccessTest extends TestCase
{
    private Specialty $specialty;
    private User $userDoc1;
    private Doctor $doctor1;
    private User $userDoc2;
    private Doctor $doctor2;
    private User $patientUser;
    private Patient $patient;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('medical_records');

        $this->specialty = Specialty::create([
            'name' => 'Psicología',
            'slug' => 'psicologia',
            'base_price' => 90.00,
            'default_duration_minutes' => 45,
            'is_active' => true,
        ]);

        // Doctor 1
        $this->userDoc1 = User::factory()->create(['name' => 'Dr. Carlos Mendoza', 'email' => 'carlos@example.com']);
        $this->userDoc1->assignRole('DOCTOR');
        $this->doctor1 = Doctor::create([
            'user_id' => $this->userDoc1->id,
            'license_number' => 'DOC-001',
            'default_slot_duration_minutes' => 45,
            'default_consultation_price' => 80.00,
            'is_active' => true,
        ]);

        // Doctor 2
        $this->userDoc2 = User::factory()->create(['name' => 'Dra. Andrea Suarez', 'email' => 'andrea@example.com']);
        $this->userDoc2->assignRole('DOCTOR');
        $this->doctor2 = Doctor::create([
            'user_id' => $this->userDoc2->id,
            'license_number' => 'DOC-002',
            'default_slot_duration_minutes' => 45,
            'default_consultation_price' => 95.00,
            'is_active' => true,
        ]);

        // Patient
        $this->patientUser = User::factory()->create(['name' => 'Juan Paciente', 'email' => 'paciente@example.com']);
        $this->patientUser->assignRole('PATIENT');
        $this->patient = Patient::create([
            'user_id' => $this->patientUser->id,
            'first_name' => 'Juan',
            'last_name' => 'Paciente',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0955544433',
            'phone' => '0990001111',
            'gender' => Gender::MALE,
        ]);
    }

    public function test_doctor_sees_only_own_appointments_in_agenda_and_no_costs(): void
    {
        $testDate = Carbon::today();

        Appointment::create([
            'code' => 'AP-DOC1-001',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor1->id,
            'specialty_id' => $this->specialty->id,
            'start_at' => $testDate->copy()->setTime(9, 0),
            'end_at' => $testDate->copy()->setTime(9, 45),
            'status' => AppointmentStatus::CONFIRMED,
            'price' => 80.00,
        ]);

        Appointment::create([
            'code' => 'AP-DOC2-002',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor2->id,
            'specialty_id' => $this->specialty->id,
            'start_at' => $testDate->copy()->setTime(11, 0),
            'end_at' => $testDate->copy()->setTime(11, 45),
            'status' => AppointmentStatus::CONFIRMED,
            'price' => 95.00,
        ]);

        $response = $this->actingAs($this->userDoc1)->get(route('admin.agenda', ['date' => $testDate->format('Y-m-d')]));
        $response->assertStatus(200);
        $response->assertSee('AP-DOC1-001');
        $response->assertDontSee('AP-DOC2-002');
        // Price should not be displayed to doctor
        $response->assertDontSee('$80.00');
        $response->assertDontSee('$95.00');
    }

    public function test_doctor_cannot_configure_other_doctors_schedules(): void
    {
        $payload = [
            'schedules' => [
                [
                    'day_of_week' => 1,
                    'start_time' => '08:00',
                    'end_time' => '16:00',
                    'is_active' => true,
                ],
            ],
        ];

        // Doctor 1 tries to update Doctor 2 schedule
        $response = $this->actingAs($this->userDoc1)->post(
            route('admin.doctors.schedules', $this->doctor2),
            $payload
        );

        $response->assertStatus(403);

        // Doctor 1 can update own schedule
        $responseOwn = $this->actingAs($this->userDoc1)->post(
            route('admin.doctors.schedules', $this->doctor1),
            $payload
        );

        $responseOwn->assertStatus(302);
        $this->assertDatabaseHas('doctor_schedules', [
            'doctor_id' => $this->doctor1->id,
            'day_of_week' => 1,
            'is_active' => true,
        ]);
    }

    public function test_doctor_cannot_update_or_delete_other_doctor(): void
    {
        // Doctor 1 tries to update Doctor 2
        $response = $this->actingAs($this->userDoc1)->put(
            route('admin.doctors.update', $this->doctor2),
            [
                'name' => 'Modificado por otro',
                'email' => 'modificado@example.com',
                'license_number' => 'DOC-999',
                'default_slot_duration_minutes' => 30,
            ]
        );
        $response->assertStatus(403);

        // Doctor 1 tries to delete Doctor 2
        $responseDelete = $this->actingAs($this->userDoc1)->delete(
            route('admin.doctors.destroy', $this->doctor2)
        );
        $responseDelete->assertStatus(403);

        // Doctor 1 tries to toggle active Doctor 2
        $responseToggle = $this->actingAs($this->userDoc1)->post(
            route('admin.doctors.toggle', $this->doctor2)
        );
        $responseToggle->assertStatus(403);
    }

    public function test_doctor_cannot_see_costs_in_doctor_list_and_sees_only_themselves(): void
    {
        $response = $this->actingAs($this->userDoc1)->get(route('admin.doctors'));
        $response->assertStatus(200);
        $response->assertSee('Dr. Carlos Mendoza');
        $response->assertDontSee('Dra. Andrea Suarez');
        // Prices hidden
        $response->assertDontSee('$80.00');
        $response->assertDontSee('$95.00');
        $response->assertDontSee('Precio Consulta');
        $response->assertDontSee('+ Registrar Médico');
    }

    public function test_doctor_cannot_access_financial_or_specialty_admin_routes(): void
    {
        $this->actingAs($this->userDoc1)->get(route('admin.specialties'))->assertStatus(403);
        $this->actingAs($this->userDoc1)->get(route('admin.transfers'))->assertStatus(403);
        $this->actingAs($this->userDoc1)->get(route('admin.audit'))->assertStatus(403);
    }

    public function test_doctor_cannot_create_appointments_via_livewire(): void
    {
        $this->actingAs($this->userDoc1);

        Livewire::test(BookingWizard::class)->assertForbidden();
    }

    public function test_doctor_can_upload_clinical_history_and_procedure_to_own_appointment(): void
    {
        $testDate = Carbon::today();
        $appointment = Appointment::create([
            'code' => 'AP-CLINICAL-001',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor1->id,
            'specialty_id' => $this->specialty->id,
            'start_at' => $testDate->copy()->setTime(10, 0),
            'end_at' => $testDate->copy()->setTime(10, 45),
            'status' => AppointmentStatus::CONFIRMED,
            'price' => 80.00,
        ]);

        $fakeHistory = UploadedFile::fake()->create('historia_paciente.pdf', 500, 'application/pdf');
        $fakeProcedure = UploadedFile::fake()->create('informe_procedimiento.pdf', 300, 'application/pdf');

        $response = $this->actingAs($this->userDoc1)->post(
            route('admin.appointments.upload_history', $appointment),
            [
                'clinical_history' => $fakeHistory,
                'procedure_document' => $fakeProcedure,
                'clinical_notes' => 'Paciente presenta mejoría notable en la sesión.',
                'status' => 'COMPLETED',
            ]
        );

        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $appointment->refresh();

        $this->assertNotNull($appointment->clinical_history_path);
        $this->assertEquals('historia_paciente.pdf', $appointment->clinical_history_filename);
        $this->assertNotNull($appointment->procedure_document_path);
        $this->assertEquals('informe_procedimiento.pdf', $appointment->procedure_document_filename);
        $this->assertEquals('Paciente presenta mejoría notable en la sesión.', $appointment->clinical_notes);
        $this->assertEquals(AppointmentStatus::COMPLETED, $appointment->status);

        // Check if path exists using normalized path or allFiles
        $allFiles = Storage::disk('medical_records')->allFiles();
        $this->assertNotEmpty($allFiles);
        $this->assertTrue(Storage::disk('medical_records')->exists(str_replace('\\', '/', $appointment->clinical_history_path)));
        $this->assertTrue(Storage::disk('medical_records')->exists(str_replace('\\', '/', $appointment->procedure_document_path)));
    }

    public function test_doctor_cannot_upload_clinical_records_to_other_doctor_appointment(): void
    {
        $testDate = Carbon::today();
        $otherAppointment = Appointment::create([
            'code' => 'AP-OTHER-001',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor2->id,
            'specialty_id' => $this->specialty->id,
            'start_at' => $testDate->copy()->setTime(14, 0),
            'end_at' => $testDate->copy()->setTime(14, 45),
            'status' => AppointmentStatus::CONFIRMED,
            'price' => 95.00,
        ]);

        $fakeHistory = UploadedFile::fake()->create('historia.pdf', 200, 'application/pdf');

        $response = $this->actingAs($this->userDoc1)->post(
            route('admin.appointments.upload_history', $otherAppointment),
            [
                'clinical_history' => $fakeHistory,
            ]
        );

        $response->assertStatus(403);
    }

    public function test_doctor_and_patient_can_download_clinical_history(): void
    {
        $testDate = Carbon::today();
        $storedPath = 'historia_clinica/test_record.pdf';
        Storage::disk('medical_records')->put($storedPath, 'CONTENIDO_MEDICO_SECRETO');

        $appointment = Appointment::create([
            'code' => 'AP-DOWNLOAD-001',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor1->id,
            'specialty_id' => $this->specialty->id,
            'start_at' => $testDate->copy()->setTime(15, 0),
            'end_at' => $testDate->copy()->setTime(15, 45),
            'status' => AppointmentStatus::COMPLETED,
            'clinical_history_path' => $storedPath,
            'clinical_history_filename' => 'historia_final.pdf',
            'price' => 80.00,
        ]);

        // Doctor 1 downloads
        $responseDoc = $this->actingAs($this->userDoc1)->get(
            route('admin.appointments.download_history', ['appointment' => $appointment, 'type' => 'historia'])
        );
        $responseDoc->assertStatus(200);

        // Doctor 2 cannot download
        $responseOtherDoc = $this->actingAs($this->userDoc2)->get(
            route('admin.appointments.download_history', ['appointment' => $appointment, 'type' => 'historia'])
        );
        $responseOtherDoc->assertStatus(403);

        // Patient owner can download from patient portal
        $responsePatient = $this->actingAs($this->patientUser)->get(
            route('patient.appointment.clinical_record', ['appointment' => $appointment, 'type' => 'historia'])
        );
        $responsePatient->assertStatus(200);
    }

    public function test_doctor_can_delete_clinical_document(): void
    {
        $testDate = Carbon::today();
        $storedPath = 'historia_clinica/test_delete.pdf';
        Storage::disk('medical_records')->put($storedPath, 'CONTENIDO_ELIMINAR');

        $appointment = Appointment::create([
            'code' => 'AP-DEL-001',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor1->id,
            'specialty_id' => $this->specialty->id,
            'start_at' => $testDate->copy()->setTime(16, 0),
            'end_at' => $testDate->copy()->setTime(16, 45),
            'status' => AppointmentStatus::COMPLETED,
            'clinical_history_path' => $storedPath,
            'clinical_history_filename' => 'historia_eliminar.pdf',
            'price' => 80.00,
        ]);

        $response = $this->actingAs($this->userDoc1)->delete(
            route('admin.appointments.delete_history', ['appointment' => $appointment, 'type' => 'historia'])
        );

        $response->assertStatus(302);
        $appointment->refresh();
        $this->assertNull($appointment->clinical_history_path);
        Storage::disk('medical_records')->assertMissing($storedPath);
    }
}
