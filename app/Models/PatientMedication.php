<?php

namespace App\Models;

use App\Domain\Clinical\Enums\MedicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientMedication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinical_record_id',
        'drug_name',
        'concentration',
        'dose',
        'frequency',
        'route',
        'status',
        'start_date',
        'notes',
    ];

    protected $casts = [
        'status' => MedicationStatus::class,
        'start_date' => 'date',
    ];

    public function clinicalRecord(): BelongsTo
    {
        return $this->belongsTo(ClinicalRecord::class);
    }
}
