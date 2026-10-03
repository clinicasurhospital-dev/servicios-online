<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalEncounterValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'encounter_id',
        'field_id',
        'section_id',
        'repeatable_index',
        'value_text',
        'value_number',
        'value_date',
        'value_datetime',
        'value_boolean',
        'value_json',
    ];

    protected $casts = [
        'value_number' => 'decimal:4',
        'value_date' => 'date',
        'value_datetime' => 'datetime',
        'value_boolean' => 'boolean',
        'value_json' => 'array',
        'repeatable_index' => 'integer',
    ];

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(ClinicalEncounter::class, 'encounter_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(ClinicalTemplateField::class, 'field_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(ClinicalTemplateSection::class, 'section_id');
    }

    /**
     * Get the dynamically typed value regardless of primitive storage column.
     */
    public function getTypedValueAttribute(): mixed
    {
        if ($this->value_number !== null) {
            // Return float or int
            return (float)$this->value_number == (int)$this->value_number ? (int)$this->value_number : (float)$this->value_number;
        }
        if ($this->value_boolean !== null) {
            return (bool)$this->value_boolean;
        }
        if ($this->value_date !== null) {
            return $this->value_date->format('Y-m-d');
        }
        if ($this->value_datetime !== null) {
            return $this->value_datetime->format('Y-m-d H:i:s');
        }
        if ($this->value_json !== null) {
            return $this->value_json;
        }
        return $this->value_text;
    }
}
