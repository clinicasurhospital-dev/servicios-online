<?php

namespace App\Domain\Clinical\Services;

use App\Domain\Clinical\Enums\AllergySeverity;
use App\Domain\Clinical\Enums\AllergyType;
use App\Domain\Clinical\Enums\BackgroundCategory;
use App\Domain\Clinical\Enums\ConditionStatus;
use App\Domain\Clinical\Enums\MedicationStatus;
use App\Models\Appointment;
use App\Models\ClinicalRecord;
use App\Models\Patient;
use App\Models\PatientAllergy;
use App\Models\PatientBackground;
use App\Models\PatientCondition;
use App\Models\PatientMedication;
use Illuminate\Support\Facades\DB;

class ClinicalRecordService
{
    public function __construct(
        protected ClinicalAuditService $auditService
    ) {}

    /**
     * Get existing ClinicalRecord or create one for the given patient.
     */
    public function getOrCreateRecordForPatient(Patient $patient): ClinicalRecord
    {
        return DB::transaction(function () use ($patient) {
            $record = ClinicalRecord::firstOrCreate(
                ['patient_id' => $patient->id],
                [
                    'record_number' => ClinicalRecord::generateRecordNumber($patient->id),
                    'blood_type' => $patient->blood_type,
                    'organ_donor' => false,
                    'notes' => $patient->medical_notes,
                ]
            );

            // If it was just created, log the audit event
            if ($record->wasRecentlyCreated) {
                $this->auditService->log('ClinicalRecordCreated', $record, [
                    'patient_id' => $patient->id,
                    'record_number' => $record->record_number,
                ]);
            }

            return $record;
        });
    }

    /**
     * Add an allergy to the patient's record.
     */
    public function addAllergy(ClinicalRecord $record, array $data): PatientAllergy
    {
        return DB::transaction(function () use ($record, $data) {
            $severity = is_string($data['severity']) ? AllergySeverity::from($data['severity']) : $data['severity'];
            $isCritical = $data['is_critical'] ?? ($severity === AllergySeverity::SEVERE || $severity === AllergySeverity::LIFE_THREATENING);

            $allergy = $record->allergies()->create([
                'substance' => $data['substance'],
                'allergy_type' => $data['allergy_type'] ?? AllergyType::OTHER->value,
                'severity' => $severity->value,
                'reaction' => $data['reaction'] ?? null,
                'is_critical' => $isCritical,
                'diagnosed_at' => $data['diagnosed_at'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $this->auditService->log('PatientAllergyAdded', $allergy, [
                'substance' => $allergy->substance,
                'severity' => $allergy->severity->value,
                'is_critical' => $allergy->is_critical,
            ]);

            return $allergy;
        });
    }

    /**
     * Add a chronic condition.
     */
    public function addCondition(ClinicalRecord $record, array $data): PatientCondition
    {
        return DB::transaction(function () use ($record, $data) {
            $condition = $record->conditions()->create([
                'condition_name' => $data['condition_name'],
                'icd10_code' => $data['icd10_code'] ?? null,
                'status' => $data['status'] ?? ConditionStatus::ACTIVE->value,
                'diagnosed_date' => $data['diagnosed_date'] ?? null,
                'is_clinical_alert' => $data['is_clinical_alert'] ?? false,
                'notes' => $data['notes'] ?? null,
            ]);

            $this->auditService->log('PatientConditionAdded', $condition, [
                'condition_name' => $condition->condition_name,
                'status' => $condition->status->value,
            ]);

            return $condition;
        });
    }

    /**
     * Add a habitual medication.
     */
    public function addMedication(ClinicalRecord $record, array $data): PatientMedication
    {
        return DB::transaction(function () use ($record, $data) {
            $medication = $record->medications()->create([
                'drug_name' => $data['drug_name'],
                'concentration' => $data['concentration'] ?? null,
                'dose' => $data['dose'] ?? null,
                'frequency' => $data['frequency'] ?? null,
                'route' => $data['route'] ?? 'Oral',
                'status' => $data['status'] ?? MedicationStatus::ACTIVE->value,
                'start_date' => $data['start_date'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $this->auditService->log('PatientMedicationAdded', $medication, [
                'drug_name' => $medication->drug_name,
                'dose' => $medication->dose,
            ]);

            return $medication;
        });
    }

    /**
     * Add a background entry (Personal, Family, Surgical, Habit, etc.).
     */
    public function addBackground(ClinicalRecord $record, array $data): PatientBackground
    {
        return DB::transaction(function () use ($record, $data) {
            $background = $record->backgrounds()->create([
                'category' => $data['category'] ?? BackgroundCategory::PERSONAL->value,
                'title' => $data['title'],
                'relationship' => $data['relationship'] ?? null,
                'event_year' => $data['event_year'] ?? null,
                'institution' => $data['institution'] ?? null,
                'details' => $data['details'] ?? null,
                'is_relevant' => $data['is_relevant'] ?? false,
            ]);

            $this->auditService->log('PatientBackgroundAdded', $background, [
                'category' => $background->category->value,
                'title' => $background->title,
            ]);

            return $background;
        });
    }

    /**
     * Get comprehensive Longitudinal Patient Summary for Clinical Dashboard.
     */
    public function getPatientSummary(Patient $patient): array
    {
        $record = $this->getOrCreateRecordForPatient($patient);

        $alerts = $patient->clinical_alerts;
        $activeConditions = $record->activeConditions;
        $activeMedications = $record->activeMedications;
        $backgrounds = $record->backgrounds()->orderBy('category')->get();
        $recentEncounters = $record->encounters()->with(['doctor.user', 'specialty', 'primaryDiagnosis'])->take(5)->get();
        $nextAppointment = Appointment::where('patient_id', $patient->id)
            ->where('start_at', '>=', now())
            ->whereIn('status', ['PENDING', 'CONFIRMED', 'PENDING_PAYMENT'])
            ->orderBy('start_at', 'asc')
            ->first();

        return [
            'record' => $record,
            'patient' => $patient,
            'alerts' => $alerts,
            'active_conditions' => $activeConditions,
            'active_medications' => $activeMedications,
            'backgrounds' => $backgrounds,
            'recent_encounters' => $recentEncounters,
            'next_appointment' => $nextAppointment,
        ];
    }
}
