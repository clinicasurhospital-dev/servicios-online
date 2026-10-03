<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClinicalTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'specialty_id',
        'name',
        'slug',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ClinicalTemplateVersion::class)->orderBy('version_number', 'desc');
    }

    public function latestVersion(): HasOne
    {
        return $this->hasOne(ClinicalTemplateVersion::class)->latestOfMany('version_number');
    }

    public function publishedVersion(): HasOne
    {
        return $this->hasOne(ClinicalTemplateVersion::class)->where('status', 'PUBLISHED')->latestOfMany('version_number');
    }
}
