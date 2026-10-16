<?php

namespace Tests\Feature\Clinical;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Clinical\Enums\DiagnosisType;
use App\Domain\Clinical\Enums\EncounterStatus;
use App\Domain\Clinical\Enums\FieldType;
use App\Domain\Clinical\Services\ClinicalEncounterService;
use App\Domain\Clinical\Services\ClinicalRecordService;
use App\Domain\Clinical\Services\ClinicalTemplateService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Tests\TestCase;

class ClinicalEncounterTest extends TestCase
{
    protected ClinicalTemplateService $templateService;
    protected ClinicalEncounterService $encounterService;
    protected ClinicalRecordService $recordService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->templateService = app(ClinicalTemplateService::class);
        $this->encounterService = app(ClinicalEncounterService::class);
        $this->recordService = app(ClinicalRecordService::class);
    }

    /**
     * CASO 2: Finalizar atención. Intentar editar directamente. Debe rechazarse.
     */
    public function test_finalized_encounter_cannot_be_directly_modified(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');

        $specialty = Specialty::create([
            'name' => 'Medicina General Test',
            'slug' => 'medicina-general-test',
            'base_price' => 30.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $template = $this->templateService->createTemplate([
            'specialty_id' => $specialty->id,
            'name' => 'Plantilla Test Cierre',
            'is_active' => true,
        ], $admin->id);

        $v1 = $template->latestVersion;
        $sec = $this->templateService->addSection($v1, ['name' => 'Examen', 'order' => 1]);
        $field = $this->templateService->addField($sec, [
            'name' => 'Hallazgos',
            'code' => 'hallazgos',
            'field_type' => FieldType::TEXTAREA->value,
            'order' => 1,
            'is_required' => true,
        ]);
        $this->templateService->publishVersion($v1, $admin->id);

        $docUser = User::factory()->create();
        $docUser->assignRole('DOCTOR');
        $doctor = Doctor::create([
            'user_id' => $docUser->id,
            'license_number' => 'DOC-001',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $patientUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'first_name' => 'Lucía',
            'last_name' => 'Ramírez',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0933332222',
            'phone' => '0990001122',
            'gender' => Gender::FEMALE,
        ]);

        $appointment = Appointment::create([
            'code' => 'AP-LOCK-001',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
            'start_at' => now(),
            'end_at' => now()->addMinutes(30),
            'status' => AppointmentStatus::CONFIRMED,
            'price' => 30.00,
        ]);

        $encounter = $this->encounterService->startEncounterFromAppointment($appointment, $doctor);

        // Finalizar atención
        $this->encounterService->finalizeEncounter(
            $encounter,
            [$field->id => 'Paciente en buen estado general, normotensa.'],
            [['code' => 'Z00.0', 'description' => 'Examen médico general', 'type' => DiagnosisType::PRIMARY->value]],
            ['bp' => '120/80'],
            $docUser->id
        );

        $this->assertEquals(EncounterStatus::FINALIZED, $encounter->fresh()->status);
        $this->assertEquals(AppointmentStatus::COMPLETED, $appointment->fresh()->status);

        // Intentar guardar borrador en una atención finalizada -> Debe lanzar DomainException
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('ya ha sido finalizada y no admite modificación directa');

        $this->encounterService->saveDraftValues($encounter, [
            $field->id => 'Intento de cambio no autorizado.',
        ]);
    }

    /**
     * CASO 3: Crear adenda. La información original permanece intacta.
     */
    public function test_addendum_preserves_original_encounter_data(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');

        $specialty = Specialty::create([
            'name' => 'Especialidad Adenda',
            'slug' => 'especialidad-adenda',
            'base_price' => 40.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $template = $this->templateService->createTemplate([
            'specialty_id' => $specialty->id,
            'name' => 'Plantilla Adenda',
            'is_active' => true,
        ], $admin->id);

        $v1 = $template->latestVersion;
        $sec = $this->templateService->addSection($v1, ['name' => 'Diagnóstico y Plan', 'order' => 1]);
        $field = $this->templateService->addField($sec, [
            'name' => 'Tratamiento',
            'code' => 'tratamiento_orig',
            'field_type' => FieldType::TEXTAREA->value,
            'order' => 1,
            'is_required' => true,
        ]);
        $this->templateService->publishVersion($v1, $admin->id);

        $docUser = User::factory()->create();
        $docUser->assignRole('DOCTOR');
        $doctor = Doctor::create([
            'user_id' => $docUser->id,
            'license_number' => 'DOC-ADENDA',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $patientUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'first_name' => 'Pedro',
            'last_name' => 'Suárez',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0944445555',
            'phone' => '0995556677',
            'gender' => Gender::MALE,
        ]);

        $appointment = Appointment::create([
            'code' => 'AP-ADENDA-001',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
            'start_at' => now(),
            'end_at' => now()->addMinutes(30),
            'status' => AppointmentStatus::CONFIRMED,
            'price' => 40.00,
        ]);

        $encounter = $this->encounterService->startEncounterFromAppointment($appointment, $doctor);

        // Finalizar con tratamiento original
        $originalTreatment = 'Paracetamol 500mg cada 8 horas por 3 días.';
        $this->encounterService->finalizeEncounter(
            $encounter,
            [$field->id => $originalTreatment],
            [['code' => 'J00', 'description' => 'Rinofaringitis aguda', 'type' => DiagnosisType::PRIMARY->value]],
            [],
            $docUser->id
        );

        // Emitir Adenda posterior
        $addendumContent = 'Se ajusta posología tras reporte febril: Paracetamol 1g cada 8 horas.';
        $addendum = $this->encounterService->createAddendum(
            $encounter,
            'Ajuste de dosis por persistencia de fiebre',
            $addendumContent,
            $docUser->id
        );

        $this->assertNotNull($addendum);
        $this->assertEquals(EncounterStatus::AMENDED, $encounter->fresh()->status);

        // Verificar que el valor original permanece EXACTAMENTE igual
        $originalSavedValue = $encounter->fresh(['values'])->values->first()->value_text;
        $this->assertEquals($originalTreatment, $originalSavedValue);

        // Verificar que la adenda existe y referencia al autor
        $this->assertCount(1, $encounter->fresh()->addendums);
        $this->assertEquals($addendumContent, $encounter->fresh()->addendums->first()->content);
        $this->assertEquals($docUser->id, $encounter->fresh()->addendums->first()->author_id);
    }
}
