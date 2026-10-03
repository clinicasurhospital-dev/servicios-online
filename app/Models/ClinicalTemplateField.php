<?php

namespace App\Models;

use App\Domain\Clinical\Enums\FieldType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicalTemplateField extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'name',
        'code',
        'label',
        'description',
        'field_type',
        'placeholder',
        'default_value',
        'unit',
        'validation_rules',
        'order',
        'is_required',
        'is_repeatable',
        'is_sensitive',
        'is_clinical_alert',
        'is_patient_visible',
        'is_active',
        'help_text',
    ];

    protected $casts = [
        'field_type' => FieldType::class,
        'validation_rules' => 'array',
        'order' => 'integer',
        'is_required' => 'boolean',
        'is_repeatable' => 'boolean',
        'is_sensitive' => 'boolean',
        'is_clinical_alert' => 'boolean',
        'is_patient_visible' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(ClinicalTemplateSection::class, 'section_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(ClinicalFieldOption::class, 'field_id')->orderBy('order', 'asc');
    }
}
