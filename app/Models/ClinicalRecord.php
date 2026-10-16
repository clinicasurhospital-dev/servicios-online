<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'record_number',
        'blood_type',
        'organ_donor',
        'notes',
    ];

    protected $casts = [
        'organ_donor' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function allergies(): HasMany
    {
        return $this->hasMany(PatientAllergy::class);
    }

    public function criticalAllergies(): HasMany
    {
        return $this->hasMany(PatientAllergy::class)->where('is_critical', true);
    }

    public function conditions(): HasMany
    {
        return $this->hasMany(PatientCondition::class);
    }

    public function activeConditions(): HasMany
    {
        return $this->hasMany(PatientCondition::class)->where('status', 'ACTIVE');
    }

    public function medications(): HasMany
    {
        return $this->hasMany(PatientMedication::class);
    }

    public function activeMedications(): HasMany
    {
        return $this->hasMany(PatientMedication::class)->where('status', 'ACTIVE');
    }

    public function backgrounds(): HasMany
    {
        return $this->hasMany(PatientBackground::class);
    }

    public function encounters(): HasMany
    {
        return $this->hasMany(ClinicalEncounter::class)->orderBy('created_at', 'desc');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClinicalDocument::class)->orderBy('created_at', 'desc');
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(ClinicalDiagnosis::class)->orderBy('created_at', 'desc');
    }

    /**
     * Generate unique record number if not set.
     */
    public static function generateRecordNumber(int $patientId): string
    {
        $year = date('Y');
        return sprintf('HC-%s-%05d', $year, $patientId);
    }
}
