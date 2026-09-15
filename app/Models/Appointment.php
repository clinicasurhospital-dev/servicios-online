<?php

namespace App\Models;

use App\Domain\Appointments\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'patient_id',
        'doctor_id',
        'specialty_id',
        'procedure_id',
        'start_at',
        'end_at',
        'status',
        'price',
        'patient_notes',
        'clinical_notes',
        'cancelled_reason',
        'cancelled_by',
        'cancelled_at',
        'created_by',
    ];

    protected $casts = [
        'status' => AppointmentStatus::class,
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    public function getTotalAmountAttribute(): float
    {
        return (float) ($this->price ?? 0);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(AppointmentStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            AppointmentStatus::PENDING,
            AppointmentStatus::PENDING_PAYMENT,
            AppointmentStatus::CONFIRMED,
        ]);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('start_at', today());
    }

    public function scopeForDoctor(Builder $query, int $doctorId): Builder
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeForPatient(Builder $query, int $patientId): Builder
    {
        return $query->where('patient_id', $patientId);
    }

    public function canBeRescheduled(): bool
    {
        return $this->status->canBeRescheduled();
    }

    public function canBeCancelled(): bool
    {
        return $this->status->canBeCancelled();
    }

    public function isPaid(): bool
    {
        return $this->payments()->where('status', 'PAID')->exists();
    }

    public function getServiceNameAttribute(): string
    {
        if ($this->procedure) {
            return "{$this->specialty->name} — {$this->procedure->name}";
        }

        return "Consulta: {$this->specialty->name}";
    }
}
