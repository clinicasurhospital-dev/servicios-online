<?php

namespace App\Models;

use App\Domain\Clinical\Enums\ConditionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientCondition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinical_record_id',
        'condition_name',
        'icd10_code',
        'status',
        'diagnosed_date',
        'is_clinical_alert',
        'notes',
    ];

    protected $casts = [
        'status' => ConditionStatus::class,
        'is_clinical_alert' => 'boolean',
        'diagnosed_date' => 'date',
    ];

    public function clinicalRecord(): BelongsTo
    {
        return $this->belongsTo(ClinicalRecord::class);
    }
}
