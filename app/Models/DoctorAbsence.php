<?php

namespace App\Models;

use App\Domain\Doctors\Enums\DoctorAbsenceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorAbsence extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'type',
        'start_date',
        'end_date',
        'notes',
        'is_approved',
        'approved_by',
    ];

    protected $casts = [
        'type' => DoctorAbsenceType::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'is_approved' => 'boolean',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
