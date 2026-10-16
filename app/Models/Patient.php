<?php

namespace App\Models;

use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'identification_type',
        'identification_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'birth_date',
        'address',
        'blood_type',
        'emergency_contact_name',
        'emergency_contact_phone',
        'allergies',
        'medical_notes',
        'data_consent_accepted',
        'data_consent_accepted_at',
        'data_consent_ip',
        'data_consent_user_agent',
    ];

    protected $casts = [
        'identification_type' => IdentificationType::class,
        'gender' => Gender::class,
        'birth_date' => 'date',
        'data_consent_accepted' => 'boolean',
        'data_consent_accepted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->orderBy('start_at', 'desc');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(DoctorReview::class);
    }

    public function clinicalRecord(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ClinicalRecord::class);
    }

    public function encounters(): HasMany
    {
        return $this->hasMany(ClinicalEncounter::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get active clinical alerts (critical allergies and alert-tagged conditions).
     */
    public function getClinicalAlertsAttribute(): \Illuminate\Support\Collection
    {
        $record = $this->clinicalRecord;
        if (!$record) {
            return collect();
        }

        $alerts = collect();

        // Critical Allergies
        $criticalAllergies = $record->allergies()->where('is_critical', true)->get();
        foreach ($criticalAllergies as $allergy) {
            $alerts->push([
                'type' => 'ALLERGY',
                'title' => "Alergia Crítica: {$allergy->substance}",
                'severity' => $allergy->severity->label(),
                'details' => $allergy->reaction ?: 'Reacción severa',
                'badge' => 'bg-red-100 text-red-800 border-red-200',
            ]);
        }

        // Active Alert Conditions
        $alertConditions = $record->conditions()->where('is_clinical_alert', true)->where('status', 'ACTIVE')->get();
        foreach ($alertConditions as $cond) {
            $alerts->push([
                'type' => 'CONDITION',
                'title' => "Condición Relevante: {$cond->condition_name}",
                'severity' => 'Activa',
                'details' => $cond->notes ?: '',
                'badge' => 'bg-amber-100 text-amber-800 border-amber-200',
            ]);
        }

        return $alerts;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }
}
