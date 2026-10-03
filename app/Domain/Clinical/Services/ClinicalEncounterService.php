<?php

namespace App\Domain\Clinical\Services;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Clinical\Enums\DiagnosisType;
use App\Domain\Clinical\Enums\EncounterStatus;
use App\Domain\Clinical\Enums\FieldType;
use App\Models\Appointment;
use App\Models\ClinicalAddendum;
use App\Models\ClinicalDiagnosis;
use App\Models\ClinicalEncounter;
use App\Models\ClinicalEncounterValue;
use App\Models\ClinicalRecord;
use App\Models\ClinicalTemplateField;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Specialty;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClinicalEncounterService
{
    public function __construct(
        protected ClinicalRecordService $recordService,
        protected ClinicalTemplateService $templateService,
        protected ClinicalValidationService $validationService,
        protected ClinicalAuditService $auditService
    ) {}

    /**
     * Start or retrieve a Clinical Encounter from an Appointment.
     */
    public function startEncounterFromAppointment(Appointment $appointment, Doctor $doctor): ClinicalEncounter
    {
        return DB::transaction(function () use ($appointment, $doctor) {
            // Check if encounter already exists for this appointment
            $existing = ClinicalEncounter::where('appointment_id', $appointment->id)->first();
            if ($existing) {
                return $existing;
            }

            $patient = $appointment->patient;
            $record = $this->recordService->getOrCreateRecordForPatient($patient);
            $specialty = $appointment->specialty;

            // Resolve active published template version for this specialty
            $templateVersion = $this->templateService->getActiveTemplateForSpecialty($specialty?->id);
            if (!$templateVersion) {
                throw new \DomainException("No existe una plantilla clínica publicada para la especialidad '{$specialty?->name}'. Por favor configure y publique una plantilla primero.");
            }

            $encounter = ClinicalEncounter::create([
                'clinical_record_id' => $record->id,
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'appointment_id' => $appointment->id,
                'specialty_id' => $specialty?->id ?? 1,
                'template_version_id' => $templateVersion->id,
                'status' => EncounterStatus::IN_PROGRESS->value,
                'consultation_type' => 'INITIAL',
                'reason_for_consultation' => $appointment->reason_for_visit ?? 'Consulta Médica',
                'started_at' => now(),
            ]);

            $this->auditService->log('EncounterStarted', $encounter, [
                'appointment_id' => $appointment->id,
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'template_version_id' => $templateVersion->id,
            ]);

            return $encounter;
        });
    }

    /**
     * Save Draft Values without blocking or requiring full finalization validation.
     */
    public function saveDraftValues(
        ClinicalEncounter $encounter,
        array $fieldValues,
        array $diagnoses = [],
        array $vitalSummary = [],
        ?string $reason = null
    ): ClinicalEncounter {
        if ($encounter->isFinalized()) {
            throw new \DomainException("Esta atención médica ya ha sido finalizada y no admite modificación directa. Debe emitir una adenda clínica.");
        }

        return DB::transaction(function () use ($encounter, $fieldValues, $diagnoses, $vitalSummary, $reason) {
            // Update reason and vitals if provided
            if ($reason !== null) {
                $encounter->reason_for_consultation = $reason;
            }
            if (!empty($vitalSummary)) {
                $encounter->vital_signs_summary = $vitalSummary;
            }
            $encounter->status = EncounterStatus::IN_PROGRESS->value;
            $encounter->save();

            // Persist field values
            $this->persistEncounterValues($encounter, $fieldValues);

            // Persist diagnoses
            if (!empty($diagnoses)) {
                $this->persistDiagnoses($encounter, $diagnoses);
            }

            $this->auditService->log('DraftSaved', $encounter, [
                'encounter_id' => $encounter->id,
                'fields_count' => count($fieldValues),
            ]);

            return $encounter->fresh(['values', 'diagnoses']);
        });
    }

    /**
     * Finalize Encounter: validates completeness, seals data, locks editing, updates appointment.
     */
    public function finalizeEncounter(
        ClinicalEncounter $encounter,
        array $fieldValues,
        array $diagnoses,
        array $vitalSummary,
        int $userId,
        ?string $reason = null
    ): ClinicalEncounter {
        if ($encounter->isFinalized()) {
            throw new \DomainException("La atención médica ya se encuentra finalizada.");
        }

        // 1. Strict Validation against Template Version rules
        $this->validationService->validateEncounterValues($encounter->templateVersion, $fieldValues, true);

        return DB::transaction(function () use ($encounter, $fieldValues, $diagnoses, $vitalSummary, $userId, $reason) {
            // Update Encounter Header
            $encounter->status = EncounterStatus::FINALIZED->value;
            $encounter->finalized_at = now();
            $encounter->finalized_by = $userId;
            if ($reason !== null) {
                $encounter->reason_for_consultation = $reason;
            }
            if (!empty($vitalSummary)) {
                $encounter->vital_signs_summary = $vitalSummary;
            }
            $encounter->save();

            // Save Values
            $this->persistEncounterValues($encounter, $fieldValues);

            // Save Diagnoses
            $this->persistDiagnoses($encounter, $diagnoses);

            // If linked to an appointment, mark appointment as COMPLETED (Atendida)
            if ($encounter->appointment) {
                $appointment = $encounter->appointment;
                if ($appointment->status !== AppointmentStatus::COMPLETED) {
                    $appointment->status = AppointmentStatus::COMPLETED;
                    $appointment->save();
                }
            }

            $this->auditService->log('EncounterFinalized', $encounter, [
                'encounter_id' => $encounter->id,
                'finalized_by' => $userId,
                'diagnoses_count' => count($diagnoses),
            ]);

            return $encounter->fresh(['values', 'diagnoses', 'finalizedByUser']);
        });
    }

    /**
     * Create an Addendum to an already Finalized Encounter.
     */
    public function createAddendum(ClinicalEncounter $encounter, string $reason, string $content, int $authorId): ClinicalAddendum
    {
        if (!$encounter->isFinalized()) {
            throw new \DomainException("Solo se pueden agregar adendas a atenciones médicas finalizadas.");
        }

        if (empty(trim($content))) {
            throw new \InvalidArgumentException("El contenido de la adenda no puede estar vacío.");
        }

        return DB::transaction(function () use ($encounter, $reason, $content, $authorId) {
            $addendum = $encounter->addendums()->create([
                'author_id' => $authorId,
                'reason' => $reason,
                'content' => $content,
                'created_at' => now(),
            ]);

            // Update status to AMENDED if it was FINALIZED
            if ($encounter->status === EncounterStatus::FINALIZED) {
                $encounter->status = EncounterStatus::AMENDED;
                $encounter->save();
            }

            $this->auditService->log('AddendumCreated', $addendum, [
                'encounter_id' => $encounter->id,
                'author_id' => $authorId,
                'reason' => $reason,
            ]);

            return $addendum;
        });
    }

    /**
     * Helper to persist typed encounter values.
     */
    protected function persistEncounterValues(ClinicalEncounter $encounter, array $fieldValues): void
    {
        foreach ($fieldValues as $fieldId => $rawValue) {
            $field = ClinicalTemplateField::find($fieldId);
            if (!$field) {
                continue;
            }

            $valuesList = is_array($rawValue) && !in_array($field->field_type, [FieldType::MULTISELECT, FieldType::CHECKBOX, FieldType::DIAGNOSIS, FieldType::MEDICATION])
                ? $rawValue 
                : [0 => $rawValue];

            foreach ($valuesList as $repIndex => $val) {
                $data = [
                    'section_id' => $field->section_id,
                    'value_text' => null,
                    'value_number' => null,
                    'value_date' => null,
                    'value_datetime' => null,
                    'value_boolean' => null,
                    'value_json' => null,
                ];

                switch ($field->field_type) {
                    case FieldType::INTEGER:
                    case FieldType::DECIMAL:
                        $data['value_number'] = ($val !== null && $val !== '') ? (float)$val : null;
                        break;

                    case FieldType::DATE:
                        $data['value_date'] = ($val !== null && $val !== '') ? $val : null;
                        break;

                    case FieldType::DATETIME:
                        $data['value_datetime'] = ($val !== null && $val !== '') ? $val : null;
                        break;

                    case FieldType::BOOLEAN:
                        $data['value_boolean'] = ($val !== null && $val !== '') ? (bool)$val : null;
                        break;

                    case FieldType::MULTISELECT:
                    case FieldType::CHECKBOX:
                    case FieldType::DIAGNOSIS:
                    case FieldType::MEDICATION:
                        $data['value_json'] = is_array($val) ? $val : ($val ? [$val] : null);
                        break;

                    default:
                        $data['value_text'] = ($val !== null) ? (string)$val : null;
                        break;
                }

                ClinicalEncounterValue::updateOrCreate(
                    [
                        'encounter_id' => $encounter->id,
                        'field_id' => $field->id,
                        'repeatable_index' => (int)$repIndex,
                    ],
                    $data
                );
            }
        }
    }

    /**
     * Helper to persist structured diagnoses.
     */
    protected function persistDiagnoses(ClinicalEncounter $encounter, array $diagnoses): void
    {
        // Remove existing diagnoses for this encounter and re-create fresh list
        $encounter->diagnoses()->delete();

        foreach ($diagnoses as $diag) {
            $code = $diag['code'] ?? 'R69';
            $description = $diag['description'] ?? 'Diagnóstico no especificado';
            $type = $diag['type'] ?? DiagnosisType::PRIMARY->value;

            $encounter->diagnoses()->create([
                'clinical_record_id' => $encounter->clinical_record_id,
                'code' => $code,
                'description' => $description,
                'type' => $type,
                'notes' => $diag['notes'] ?? null,
            ]);
        }
    }
}
