<?php

namespace App\Models;

use App\Domain\Clinical\Enums\TemplateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicalTemplateVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinical_template_id',
        'version_number',
        'status',
        'changelog',
        'published_at',
        'published_by',
    ];

    protected $casts = [
        'status' => TemplateStatus::class,
        'published_at' => 'datetime',
        'version_number' => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(ClinicalTemplate::class, 'clinical_template_id');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ClinicalTemplateSection::class, 'template_version_id')->orderBy('order', 'asc');
    }

    public function isPublished(): bool
    {
        return $this->status === TemplateStatus::PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status === TemplateStatus::DRAFT;
    }
}
