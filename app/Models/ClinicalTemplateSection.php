<?php

namespace App\Models;

use App\Domain\Clinical\Enums\SectionVisibility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicalTemplateSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_version_id',
        'name',
        'description',
        'order',
        'is_active',
        'is_required',
        'is_repeatable',
        'visibility',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_required' => 'boolean',
        'is_repeatable' => 'boolean',
        'visibility' => SectionVisibility::class,
        'order' => 'integer',
    ];

    public function version(): BelongsTo
    {
        return $this->belongsTo(ClinicalTemplateVersion::class, 'template_version_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(ClinicalTemplateField::class, 'section_id')->orderBy('order', 'asc');
    }
}
