<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Specialty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'image_path',
        'base_price',
        'default_duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'default_duration_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($specialty) {
            if (empty($specialty->slug)) {
                $specialty->slug = Str::slug($specialty->name);
            }
        });
    }

    public function procedures(): HasMany
    {
        return $this->hasMany(Procedure::class)->where('is_active', true);
    }

    public function allProcedures(): HasMany
    {
        return $this->hasMany(Procedure::class);
    }

    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class, 'doctor_specialty')
            ->withPivot(['custom_price', 'custom_duration_minutes'])
            ->withTimestamps();
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
