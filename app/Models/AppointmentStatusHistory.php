<?php

namespace App\Models;

use App\Domain\Appointments\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentStatusHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'appointment_id',
        'previous_status',
        'new_status',
        'changed_by_user_id',
        'reason',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'previous_status' => AppointmentStatus::class,
        'new_status' => AppointmentStatus::class,
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
