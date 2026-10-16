<?php

namespace App\Models;

use App\Domain\Clinical\Enums\BackgroundCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientBackground extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinical_record_id',
        'category',
        'title',
        'relationship',
        'event_year',
        'institution',
        'details',
        'is_relevant',
    ];

    protected $casts = [
        'category' => BackgroundCategory::class,
        'is_relevant' => 'boolean',
    ];

    public function clinicalRecord(): BelongsTo
    {
        return $this->belongsTo(ClinicalRecord::class);
    }
}
