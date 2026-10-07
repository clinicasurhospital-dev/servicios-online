<?php

namespace App\Domain\Appointments\Services;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Appointments\Exceptions\InvalidReschedulePolicyException;
use App\Domain\Appointments\Exceptions\SlotAlreadyBookedException;
use App\Domain\Availability\Services\AvailabilityService;
use App\Domain\Branding\BrandingService;
use App\Models\Appointment;
use App\Models\AppointmentStatusHistory;
use App\Models\AuditLog;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentRescheduleService
{
    public function __construct(
        protected AvailabilityService $availabilityService,
        protected BrandingService $brandingService,
        protected ?AppointmentNotificationDispatcher $notificationDispatcher = null
    ) {
        $this->notificationDispatcher = $notificationDispatcher ?? app(AppointmentNotificationDispatcher::class);
    }

    /**
     * Reschedules an appointment to a new date/time adhering to policy and concurrency rules.
     *
     * @throws InvalidReschedulePolicyException
     * @throws SlotAlreadyBookedException
     */
    public function reschedule(
        Appointment $appointment,
        Carbon $newStartAt,
        Carbon $newEndAt,
        ?int $userId = null,
        ?string $reason = null
    ): Appointment {
        // 1. Validate Appointment Status
        if (!$appointment->canBeRescheduled()) {
            throw new InvalidReschedulePolicyException("No es posible reprogramar una cita con estado '{$appointment->status->label()}'.");
        }

        // 2. Validate Reschedule Policy (minimum hours requirement)
        $minHours = $this->brandingService->minimumRescheduleHours();
        $hoursUntilAppointment = now()->diffInHours($appointment->start_at, false);

        // If requested by patient and does not meet minimum hours policy
        if ($hoursUntilAppointment < $minHours && !auth()->user()?->isAdmin() && !auth()->user()?->isReception()) {
            throw new InvalidReschedulePolicyException(
                "Las citas deben reprogramarse con al menos {$minHours} horas de anticipación."
            );
        }

        return DB::transaction(function () use ($appointment, $newStartAt, $newEndAt, $userId, $reason) {
            // Lock doctor for update
            Doctor::where('id', $appointment->doctor_id)->lockForUpdate()->firstOrFail();

            // 3. Verify availability of the new slot (excluding current appointment)
            $isAvailable = $this->availabilityService->isSlotAvailable(
                doctorId: $appointment->doctor_id,
                startAt: $newStartAt,
                endAt: $newEndAt,
                excludeAppointmentId: $appointment->id
            );

            if (!$isAvailable) {
                throw new SlotAlreadyBookedException("El nuevo horario seleccionado ya no está disponible.");
            }

            $beforeState = $appointment->toArray();
            $oldStartAt = $appointment->start_at->copy();
            $oldEndAt = $appointment->end_at->copy();

            // 4. Update appointment with new schedule
            $appointment->update([
                'start_at' => $newStartAt,
                'end_at' => $newEndAt,
                'status' => AppointmentStatus::RESCHEDULED,
            ]);

            // 5. Record immutable status history
            AppointmentStatusHistory::create([
                'appointment_id' => $appointment->id,
                'previous_status' => $beforeState['status'],
                'new_status' => AppointmentStatus::RESCHEDULED->value,
                'changed_by_user_id' => $userId,
                'reason' => $reason ?: 'Reprogramación de fecha y hora de cita',
                'metadata' => [
                    'old_start_at' => $oldStartAt->toIso8601String(),
                    'old_end_at' => $oldEndAt->toIso8601String(),
                    'new_start_at' => $newStartAt->toIso8601String(),
                    'new_end_at' => $newEndAt->toIso8601String(),
                ],
                'created_at' => now(),
            ]);

            // 6. Audit log
            AuditLog::create([
                'user_id' => $userId,
                'action' => 'AppointmentRescheduled',
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

        // Dispatch notifications to patient, doctor, and staff
        $this->notificationDispatcher->dispatchAppointmentRescheduled(
            $appointment,
            $oldStartAt,
            $newStartAt,
            $reason
        );

        return $appointment;
    }
}
