<?php

namespace App\Domain\Appointments\Services;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\AppointmentStatusHistory;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class AppointmentCancellationService
{
    /**
     * Cancels an appointment and releases its time slot.
     */
    public function cancel(
        Appointment $appointment,
        string $reason,
        ?int $cancelledByUserId = null
    ): Appointment {
        if (!$appointment->canBeCancelled()) {
            throw new InvalidArgumentException(
                "No es posible cancelar una cita con estado '{$appointment->status->label()}'."
            );
        }

        return DB::transaction(function () use ($appointment, $reason, $cancelledByUserId) {
            $beforeState = $appointment->toArray();
            $previousStatus = $appointment->status->value;

            $appointment->update([
                'status' => AppointmentStatus::CANCELLED,
                'cancelled_reason' => $reason,
                'cancelled_by' => $cancelledByUserId,
                'cancelled_at' => now(),
            ]);

            // Record status history
            AppointmentStatusHistory::create([
                'appointment_id' => $appointment->id,
                'previous_status' => $previousStatus,
                'new_status' => AppointmentStatus::CANCELLED->value,
                'changed_by_user_id' => $cancelledByUserId,
                'reason' => "Cancelación: {$reason}",
                'metadata' => [
                    'cancelled_at' => now()->toIso8601String(),
                ],
                'created_at' => now(),
            ]);

            // Audit log
            AuditLog::create([
                'user_id' => $cancelledByUserId,
                'action' => 'AppointmentCancelled',
                'auditable_type' => Appointment::class,
                'auditable_id' => (string) $appointment->id,
                'before_state' => $beforeState,
                'after_state' => $appointment->fresh()->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return $appointment;
        });
    }
}
