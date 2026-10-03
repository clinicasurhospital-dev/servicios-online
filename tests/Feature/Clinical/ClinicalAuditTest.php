<?php

namespace Tests\Feature\Clinical;

use App\Domain\Clinical\Enums\FieldType;
use App\Domain\Clinical\Services\ClinicalAuditService;
use App\Domain\Clinical\Services\ClinicalEncounterService;
use App\Domain\Clinical\Services\ClinicalRecordService;
use App\Domain\Clinical\Services\ClinicalTemplateService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Models\Appointment;
use App\Models\ClinicalAuditLog;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Tests\TestCase;

class ClinicalAuditTest extends TestCase
{
    protected ClinicalAuditService $auditService;
    protected ClinicalTemplateService $templateService;
    protected ClinicalEncounterService $encounterService;
    protected ClinicalRecordService $recordService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auditService = app(ClinicalAuditService::class);
        $this->templateService = app(ClinicalTemplateService::class);
        $this->encounterService = app(ClinicalEncounterService::class);
        $this->recordService = app(ClinicalRecordService::class);
    }

    public function test_clinical_events_are_recorded_in_audit_logs(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');
        $this->actingAs($admin);

        $specialty = Specialty::create([
            'name' => 'Auditoría Test',
            'slug' => 'auditoria-test',
            'base_price' => 25.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $template = $this->templateService->createTemplate([
            'specialty_id' => $specialty->id,
            'name' => 'Plantilla Auditoría',
            'is_active' => true,
        ], $admin->id);

        $this->assertDatabaseHas('clinical_audit_logs', [
            'event' => 'TemplateCreated',
            'auditable_type' => get_class($template),
            'auditable_id' => $template->id,
        ]);

        $v1 = $template->latestVersion;
        $this->templateService->publishVersion($v1, $admin->id);

        $this->assertDatabaseHas('clinical_audit_logs', [
            'event' => 'TemplatePublished',
            'auditable_type' => get_class($v1),
            'auditable_id' => $v1->id,
        ]);
    }
}
