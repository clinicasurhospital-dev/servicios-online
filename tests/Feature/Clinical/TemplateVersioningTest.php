<?php

namespace Tests\Feature\Clinical;

use App\Domain\Clinical\Enums\FieldType;
use App\Domain\Clinical\Enums\TemplateStatus;
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

class TemplateVersioningTest extends TestCase
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
     * CASO 1:
     * 1. Crear plantilla Cardiología v1.
     * 2. Publicar.
     * 3. Crear consulta.
     * 4. Modificar plantilla (crea v2 con campos adicionales).
     * 5. La consulta anterior sigue usando v1.
     */
    public function test_template_versioning_preserves_historical_encounter_integrity(): void
    {
        $adminUser = User::factory()->create();
        $adminUser->assignRole('ADMIN');

        $specialty = Specialty::create([
            'name' => 'Cardiología de Prueba',
            'slug' => 'cardiologia-prueba',
            'base_price' => 50.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        // 1. Crear plantilla Cardiología v1 (Draft)
        $template = $this->templateService->createTemplate([
            'specialty_id' => $specialty->id,
            'name' => 'Cardiología v1 Plantilla',
            'is_active' => true,
        ], $adminUser->id);

        $v1 = $template->latestVersion;
        $sec1 = $this->templateService->addSection($v1, ['name' => 'Motivo de Consulta', 'order' => 1]);
        $fMotivo = $this->templateService->addField($sec1, [
            'name' => 'Motivo Principal',
            'code' => 'motivo_v1',
            'field_type' => FieldType::TEXTAREA->value,
            'order' => 1,
        ]);

        // 2. Publicar v1
        $this->templateService->publishVersion($v1, $adminUser->id);
        $this->assertEquals(TemplateStatus::PUBLISHED, $v1->fresh()->status);

        // 3. Crear consulta asistencial con v1
        $doctorUser = User::factory()->create();
        $doctorUser->assignRole('DOCTOR');
        $doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'license_number' => 'DOC-CARDIO-1',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $patientUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'first_name' => 'Roberto',
            'last_name' => 'Gómez',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0911223344',
            'phone' => '0988776655',
            'gender' => Gender::MALE,
        ]);

        $appointment = Appointment::create([
            'code' => 'AP-V1-001',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
            'start_at' => now(),
            'end_at' => now()->addMinutes(30),
            'status' => 'CONFIRMED',
            'price' => 50.00,
        ]);

        $encounterV1 = $this->encounterService->startEncounterFromAppointment($appointment, $doctor);
        $this->assertEquals($v1->id, $encounterV1->template_version_id);

        // Guardar respuesta en consulta v1
        $this->encounterService->saveDraftValues($encounterV1, [
            $fMotivo->id => 'Dolor precordial opresivo con irradiación.',
        ]);

        // 4. Modificar plantilla -> crea v2
        $v2 = $this->templateService->createNewVersionDraft($template, 'Se agrega sección de Riesgo Cardiovascular');
        $this->assertEquals(2, $v2->version_number);
        $this->assertEquals(TemplateStatus::DRAFT, $v2->status);

        // Agregar sección y campo exclusivo de v2
        $secRiesgo = $this->templateService->addSection($v2, ['name' => 'Riesgo Cardiovascular', 'order' => 2]);
        $fRiesgo = $this->templateService->addField($secRiesgo, [
            'name' => 'Score Framingham',
            'code' => 'score_framingham_v2',
            'field_type' => FieldType::DECIMAL->value,
            'order' => 1,
        ]);

        // Publicar v2
        $this->templateService->publishVersion($v2, $adminUser->id);

        // 5. Verificar que la consulta anterior sigue usando v1 y sus campos originales
        $encounterV1Reloaded = $encounterV1->fresh(['templateVersion.sections.fields', 'values']);
        $this->assertEquals($v1->id, $encounterV1Reloaded->template_version_id);
        $this->assertEquals(1, $encounterV1Reloaded->templateVersion->version_number);
        $this->assertEquals(1, $encounterV1Reloaded->templateVersion->sections->count());
        $this->assertEquals('Dolor precordial opresivo con irradiación.', $encounterV1Reloaded->values->first()->value_text);

        // Y verificar que una nueva cita iniciada ahora utiliza la nueva versión v2
        $appointment2 = Appointment::create([
            'code' => 'AP-V2-002',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
            'start_at' => now()->addDay(),
            'end_at' => now()->addDay()->addMinutes(30),
            'status' => 'CONFIRMED',
            'price' => 50.00,
        ]);

        $encounterV2 = $this->encounterService->startEncounterFromAppointment($appointment2, $doctor);
        $this->assertEquals($v2->id, $encounterV2->template_version_id);
        $this->assertEquals(2, $encounterV2->templateVersion->version_number);
        $this->assertEquals(2, $encounterV2->templateVersion->sections->count());
    }
}
