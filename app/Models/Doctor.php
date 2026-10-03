<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'primary_specialty_name',
        'bio',
        'photo_path',
        'room_office',
        'default_consultation_price',
        'default_slot_duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'default_consultation_price' => 'decimal:2',
        'default_slot_duration_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specialties(): BelongsToMany
    {
        return $this->belongsToMany(Specialty::class, 'doctor_specialty')
            ->withPivot(['custom_price', 'custom_duration_minutes'])
            ->withTimestamps();
    }

    public function procedures(): BelongsToMany
    {
        return $this->belongsToMany(Procedure::class, 'doctor_procedure')
            ->withPivot(['custom_price', 'custom_duration_minutes'])
            ->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function scheduleBlocks(): HasMany
    {
        return $this->hasMany(DoctorScheduleBlock::class);
    }

    public function absences(): HasMany
    {
        return $this->hasMany(DoctorAbsence::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(DoctorReview::class);
    }

    public function getAverageRatingAttribute(): float
    {
        return (float) round($this->reviews()->whereNotNull('submitted_at')->where('is_visible', true)->avg('rating') ?: 5.0, 1);
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->reviews()->whereNotNull('submitted_at')->where('is_visible', true)->count();
    }

    public function getNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'Médico';
    }

    public function getEmailAttribute(): string
    {
        return $this->user ? $this->user->email : '';
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->user ? $this->user->phone : null;
    }
}
