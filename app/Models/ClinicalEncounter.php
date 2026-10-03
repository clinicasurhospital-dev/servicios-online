<?php

namespace App\Models;

use App\Domain\Clinical\Enums\ConsultationType;
use App\Domain\Clinical\Enums\EncounterStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClinicalEncounter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinical_record_id',
        'patient_id',
        'doctor_id',
        'appointment_id',
        'specialty_id',
        'template_version_id',
        'status',
        'consultation_type',
        'reason_for_consultation',
        'vital_signs_summary',
        'started_at',
        'finalized_at',
        'finalized_by',
    ];

    protected $casts = [
        'status' => EncounterStatus::class,
        'consultation_type' => ConsultationType::class,
        'vital_signs_summary' => 'array',
        'started_at' => 'datetime',
        'finalized_at' => 'datetime',
    ];

    public function clinicalRecord(): BelongsTo
    {
        return $this->belongsTo(ClinicalRecord::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function templateVersion(): BelongsTo
    {
        return $this->belongsTo(ClinicalTemplateVersion::class, 'template_version_id');
    }

    public function finalizedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function values(): HasMany
    {
        return $this->hasMany(ClinicalEncounterValue::class, 'encounter_id');
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(ClinicalDiagnosis::class, 'encounter_id');
    }

    public function primaryDiagnosis()
    {
        return $this->hasOne(ClinicalDiagnosis::class, 'encounter_id')->where('type', 'PRIMARY');
    }

    public function addendums(): HasMany
    {
        return $this->hasMany(ClinicalAddendum::class, 'encounter_id')->orderBy('created_at', 'asc');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClinicalDocument::class, 'encounter_id');
    }

    public function isFinalized(): bool
    {
        return in_array($this->status, [EncounterStatus::FINALIZED, EncounterStatus::AMENDED], true);
    }

    public function isEditable(): bool
    {
        return $this->status->isEditable();
    }
}
