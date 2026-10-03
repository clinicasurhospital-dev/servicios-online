<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class DoctorReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'doctor_id',
        'patient_id',
        'rating',
        'attention_rating',
        'punctuality_rating',
        'facilities_rating',
        'recommendation_score',
        'comment',
        'is_anonymous',
        'channel',
        'token',
        'token_expires_at',
        'submitted_at',
        'is_visible',
    ];

    protected $casts = [
        'rating' => 'integer',
        'attention_rating' => 'integer',
        'punctuality_rating' => 'integer',
        'facilities_rating' => 'integer',
        'recommendation_score' => 'integer',
        'is_anonymous' => 'boolean',
        'is_visible' => 'boolean',
        'token_expires_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function isCompleted(): bool
    {
        return !is_null($this->submitted_at);
    }

    public function getPatientDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Paciente Anónimo';
        }

        return $this->patient ? $this->patient->full_name : 'Paciente';
    }

    public static function generateUniqueToken(): string
    {
        do {
            $token = Str::random(40);
        } while (static::where('token', $token)->exists());

        return $token;
    }
}
