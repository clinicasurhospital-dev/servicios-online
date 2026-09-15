<?php

namespace App\Models;

use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'identification_type',
        'identification_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'birth_date',
        'address',
        'blood_type',
        'emergency_contact_name',
        'emergency_contact_phone',
        'allergies',
        'medical_notes',
    ];

    protected $casts = [
        'identification_type' => IdentificationType::class,
        'gender' => Gender::class,
        'birth_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->orderBy('start_at', 'desc');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }
}
