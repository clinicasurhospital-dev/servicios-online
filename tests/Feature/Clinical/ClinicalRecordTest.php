<?php

namespace Tests\Feature\Clinical;

use App\Domain\Clinical\Enums\AllergySeverity;
use App\Domain\Clinical\Enums\AllergyType;
use App\Domain\Clinical\Enums\ConditionStatus;
use App\Domain\Clinical\Services\ClinicalRecordService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Models\Patient;
use App\Models\User;
use Tests\TestCase;

class ClinicalRecordTest extends TestCase
{
    protected ClinicalRecordService $recordService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recordService = app(ClinicalRecordService::class);
    }

    public function test_clinical_record_is_automatically_initialized_for_patient(): void
    {
        $user = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $user->id,
            'first_name' => 'María',
            'last_name' => 'López',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0987654321',
            'phone' => '0987654321',
            'gender' => Gender::FEMALE,
            'blood_type' => 'O+',
        ]);

        $record = $this->recordService->getOrCreateRecordForPatient($patient);

        $this->assertNotNull($record);
        $this->assertEquals($patient->id, $record->patient_id);
        $this->assertStringStartsWith('HC-', $record->record_number);
        $this->assertEquals('O+', $record->blood_type);
    }

    public function test_critical_allergy_triggers_clinical_alert(): void
    {
        $user = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $user->id,
            'first_name' => 'Carlos',
            'last_name' => 'Mendoza',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0912345678',
            'phone' => '0999888777',
            'gender' => Gender::MALE,
        ]);

        $record = $this->recordService->getOrCreateRecordForPatient($patient);

        // Add non-critical mild allergy
        $this->recordService->addAllergy($record, [
            'substance' => 'Polen',
            'allergy_type' => AllergyType::ENVIRONMENTAL->value,
            'severity' => AllergySeverity::MILD->value,
            'reaction' => 'Rinitis estacional',
            'is_critical' => false,
        ]);

        $this->assertCount(0, $patient->fresh()->clinical_alerts);

        // Add critical life-threatening allergy
        $this->recordService->addAllergy($record, [
            'substance' => 'Penicilina',
            'allergy_type' => AllergyType::MEDICATION->value,
            'severity' => AllergySeverity::LIFE_THREATENING->value,
            'reaction' => 'Shock anafiláctico',
            'is_critical' => true,
        ]);

        $alerts = $patient->fresh()->clinical_alerts;
        $this->assertCount(1, $alerts);
        $this->assertStringContainsString('Penicilina', $alerts->first()['title']);
    }

    public function test_active_condition_with_alert_flag_appears_in_clinical_alerts(): void
    {
        $user = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $user->id,
            'first_name' => 'Elena',
            'last_name' => 'Suárez',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0955554444',
            'phone' => '0991112233',
            'gender' => Gender::FEMALE,
        ]);

        $record = $this->recordService->getOrCreateRecordForPatient($patient);

        $this->recordService->addCondition($record, [
            'condition_name' => 'Diabetes Mellitus Tipo 1',
            'icd10_code' => 'E10',
            'status' => ConditionStatus::ACTIVE->value,
            'is_clinical_alert' => true,
            'notes' => 'Insulinodependiente',
        ]);

        $alerts = $patient->fresh()->clinical_alerts;
        $this->assertCount(1, $alerts);
        $this->assertStringContainsString('Diabetes Mellitus Tipo 1', $alerts->first()['title']);
    }
}
