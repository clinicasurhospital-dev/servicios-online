<?php

namespace App\Models;

use App\Domain\Clinical\Enums\DiagnosisType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalDiagnosis extends Model
{
    use HasFactory;

    protected $fillable = [
        'encounter_id',
        'clinical_record_id',
        'code',
        'description',
        'type',
        'notes',
    ];

    protected $casts = [
        'type' => DiagnosisType::class,
    ];

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(ClinicalEncounter::class, 'encounter_id');
    }

    public function clinicalRecord(): BelongsTo
    {
        return $this->belongsTo(ClinicalRecord::class, 'clinical_record_id');
    }
}
