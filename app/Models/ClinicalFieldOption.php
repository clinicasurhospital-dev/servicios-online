<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalFieldOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'label',
        'value',
        'order',
        'is_default',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_default' => 'boolean',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(ClinicalTemplateField::class, 'field_id');
    }
}
