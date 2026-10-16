<?php

namespace Tests\Feature\Clinical;

use App\Domain\Clinical\Enums\AllergySeverity;
use App\Domain\Clinical\Enums\AllergyType;
use App\Domain\Clinical\Enums\DocumentType;
use App\Domain\Clinical\Services\ClinicalDocumentService;
use App\Domain\Clinical\Services\ClinicalRecordService;
use App\Domain\Clinical\Services\ClinicalTemplateService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Models\ClinicalDocument;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClinicalAuthorizationAndPrivacyTest extends TestCase
{
    protected ClinicalRecordService $recordService;
    protected ClinicalDocumentService $documentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recordService = app(ClinicalRecordService::class);
        $this->documentService = app(ClinicalDocumentService::class);
        Storage::fake('medical_records');
    }

    /**
     * CASO 4: Recepción intenta descargar documento clínico confidencial o ver notas sensibles -> Bloqueado.
     */
    public function test_reception_cannot_download_sensitive_clinical_document(): void
    {
        $patientUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'first_name' => 'Ana',
            'last_name' => 'Castro',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0988887777',
            'phone' => '0991114455',
            'gender' => Gender::FEMALE,
        ]);

        $record = $this->recordService->getOrCreateRecordForPatient($patient);

        $docUser = User::factory()->create();
        $docUser->assignRole('DOCTOR');

        $file = UploadedFile::fake()->create('informe_psicologia_confidencial.pdf', 500, 'application/pdf');
        $document = $this->documentService->uploadDocument(
            $record,
            $file,
            DocumentType::LAB_REPORT->value,
            null,
            'Informe confidencial de salud mental',
            true, // is_sensitive = true
            $docUser->id
        );

        $receptionUser = User::factory()->create();
        $receptionUser->assignRole('RECEPTION');

        // Reception attempt to download sensitive document
        $response = $this->actingAs($receptionUser)->get(route('admin.clinical.documents.download', ['document' => $document->id]));
        $response->assertStatus(403);
    }

    /**
     * CASO 6: Doctor registra alergia severa/crítica -> Debe aparecer inmediatamente como alerta clínica.
     */
    public function test_doctor_registers_severe_allergy_triggers_prominent_clinical_alert(): void
    {
        $patientUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $patientUser->id,
            'first_name' => 'Gabriel',
            'last_name' => 'Vera',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0966665555',
            'phone' => '0992223344',
            'gender' => Gender::MALE,
        ]);

        $record = $this->recordService->getOrCreateRecordForPatient($patient);

        $this->recordService->addAllergy($record, [
            'substance' => 'Ácido Acetilsalicílico (Aspirina)',
            'allergy_type' => AllergyType::MEDICATION->value,
            'severity' => AllergySeverity::SEVERE->value,
            'reaction' => 'Broncoespasmo severo',
            'is_critical' => true,
        ]);

        $alerts = $patient->fresh()->clinical_alerts;
        $this->assertNotEmpty($alerts);
        $this->assertStringContainsString('Aspirina', $alerts->first()['title']);
    }
}
