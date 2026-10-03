<?php

namespace App\Models;

use App\Domain\Clinical\Enums\AllergySeverity;
use App\Domain\Clinical\Enums\AllergyType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientAllergy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinical_record_id',
        'substance',
        'allergy_type',
        'severity',
        'reaction',
        'is_critical',
        'diagnosed_at',
        'notes',
    ];

    protected $casts = [
        'allergy_type' => AllergyType::class,
        'severity' => AllergySeverity::class,
        'is_critical' => 'boolean',
        'diagnosed_at' => 'date',
    ];

    public function clinicalRecord(): BelongsTo
    {
        return $this->belongsTo(ClinicalRecord::class);
    }
}
