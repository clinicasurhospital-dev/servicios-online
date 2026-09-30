<?php

namespace App\Domain\Availability\Services;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Availability\DTOs\AvailabilitySlotDTO;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorAbsence;
use App\Models\DoctorSchedule;
use App\Models\DoctorScheduleBlock;
use App\Models\Procedure;
use App\Models\Specialty;
use App\Models\SystemHoliday;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * Calculate available appointment slots for a doctor on a specific date.
     *
     * @param Doctor $doctor
     * @param Carbon $date
     * @param int|null $durationMinutes
     * @param int|null $specialtyId
     * @param int|null $procedureId
     * @param int|null $excludeAppointmentId For rescheduling
     * @return Collection<AvailabilitySlotDTO>
     */
    public function getAvailableSlotsForDate(
        Doctor $doctor,
        Carbon $date,
        ?int $durationMinutes = null,
        ?int $specialtyId = null,
        ?int $procedureId = null,
        ?int $excludeAppointmentId = null
    ): Collection {
        // Resolve duration
        $slotDuration = $this->resolveDuration($doctor, $durationMinutes, $specialtyId, $procedureId);

        $dateString = $date->toDateString();
        $dayOfWeek = $date->dayOfWeek; // 0 = Domingo, 1 = Lunes, ..., 6 = Sábado

        // 1. Check if the date is a configured Holiday
        if ($this->isHoliday($date)) {
            return collect();
        }

        // 2. Check if doctor is on approved Absence (Vacation, Medical Leave, etc.)
        if ($this->isDoctorAbsent($doctor->id, $date)) {
            return collect();
        }

        // 3. Get active working schedules for this day of week
        $schedules = DoctorSchedule::where('doctor_id', $doctor->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        if ($schedules->isEmpty()) {
            return collect();
        }

        // 4. Fetch all active appointments on this date
        $existingAppointmentsQuery = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('start_at', $dateString)
            ->whereIn('status', [
                AppointmentStatus::PENDING,
                AppointmentStatus::PENDING_PAYMENT,
                AppointmentStatus::CONFIRMED,
            ]);

        if ($excludeAppointmentId) {
            $existingAppointmentsQuery->where('id', '!=', $excludeAppointmentId);
        }

        $existingAppointments = $existingAppointmentsQuery->get(['id', 'start_at', 'end_at']);

        // 5. Fetch all schedule blocks for this date
        $scheduleBlocks = DoctorScheduleBlock::where('doctor_id', $doctor->id)
            ->where(function ($query) use ($date) {
                $startOfDay = $date->copy()->startOfDay();
                $endOfDay = $date->copy()->endOfDay();
                $query->where('start_datetime', '<=', $endOfDay)
                      ->where('end_datetime', '>=', $startOfDay);
            })
            ->get(['id', 'start_datetime', 'end_datetime', 'reason']);

        $slots = collect();
        $now = Carbon::now();

        foreach ($schedules as $schedule) {
            $scheduleStart = Carbon::parse("{$dateString} {$schedule->start_time}");
            $scheduleEnd = Carbon::parse("{$dateString} {$schedule->end_time}");

            $currentSlotStart = $scheduleStart->copy();

            while ($currentSlotStart->copy()->addMinutes($slotDuration)->lte($scheduleEnd)) {
                $currentSlotEnd = $currentSlotStart->copy()->addMinutes($slotDuration);

                // Check if slot is in the past (only for today)
                $isPast = $date->isToday() && $currentSlotStart->lte($now->copy()->addMinutes(15));

                if (!$isPast) {
                    // Check collision with existing appointments
                    $hasAppointmentCollision = $existingAppointments->some(function ($appt) use ($currentSlotStart, $currentSlotEnd) {
                        return $currentSlotStart->lt($appt->end_at) && $currentSlotEnd->gt($appt->start_at);
                    });

                    // Check collision with schedule blocks
                    $hasBlockCollision = $scheduleBlocks->some(function ($block) use ($currentSlotStart, $currentSlotEnd) {
                        return $currentSlotStart->lt($block->end_datetime) && $currentSlotEnd->gt($block->start_datetime);
                    });

                    if (!$hasAppointmentCollision && !$hasBlockCollision) {
                        $slots->push(new AvailabilitySlotDTO(
                            startAt: $currentSlotStart->copy(),
                            endAt: $currentSlotEnd->copy(),
                            doctorId: $doctor->id,
                            isAvailable: true,
                            durationMinutes: $slotDuration
                        ));
                    }
                }

                // Increment to next slot interval
                $intervalMinutes = $schedule->slot_duration_minutes > 0 ? $schedule->slot_duration_minutes : $slotDuration;
                $currentSlotStart->addMinutes($intervalMinutes);
            }
        }

        return $slots;
    }

    /**
     * Check if a specific slot is available in real time.
     */
    public function isSlotAvailable(
        int $doctorId,
        Carbon $startAt,
        Carbon $endAt,
        ?int $excludeAppointmentId = null
    ): bool {
        $doctor = Doctor::find($doctorId);
        if (!$doctor || !$doctor->is_active) {
            return false;
        }

        $date = $startAt->copy()->startOfDay();

        // 1. Holiday check
        if ($this->isHoliday($date)) {
            return false;
        }

        // 2. Doctor absence check
        if ($this->isDoctorAbsent($doctorId, $date)) {
            return false;
        }

        // 3. Schedule check
        $dayOfWeek = $date->dayOfWeek;
        $timeStr = $startAt->format('H:i:s');
        $endTimeStr = $endAt->format('H:i:s');

        $isWithinSchedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->where('start_time', '<=', $timeStr)
            ->where('end_time', '>=', $endTimeStr)
            ->exists();

        if (!$isWithinSchedule) {
            return false;
        }

        // 4. Block collision check
        $hasBlock = DoctorScheduleBlock::where('doctor_id', $doctorId)
            ->where('start_datetime', '<', $endAt)
            ->where('end_datetime', '>', $startAt)
            ->exists();

        if ($hasBlock) {
            return false;
        }

        // 5. Existing appointment collision check
        $appointmentQuery = Appointment::where('doctor_id', $doctorId)
            ->whereIn('status', [
                AppointmentStatus::PENDING,
                AppointmentStatus::PENDING_PAYMENT,
                AppointmentStatus::CONFIRMED,
            ])
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt);

        if ($excludeAppointmentId) {
            $appointmentQuery->where('id', '!=', $excludeAppointmentId);
        }

        return !$appointmentQuery->exists();
    }

    /**
     * Check if a date is configured as a system holiday.
     */
    public function isHoliday(Carbon $date): bool
    {
        return SystemHoliday::where('date', $date->toDateString())
            ->orWhere(function ($query) use ($date) {
                $query->where('is_recurring', true)
                      ->whereMonth('date', $date->month)
                      ->whereDay('date', $date->day);
            })
            ->exists();
    }

    /**
     * Check if doctor is absent on a given date.
     */
    public function isDoctorAbsent(int $doctorId, Carbon $date): bool
    {
        $dateStr = $date->toDateString();

        return DoctorAbsence::where('doctor_id', $doctorId)
            ->where('is_approved', true)
            ->where('start_date', '<=', $dateStr)
            ->where('end_date', '>=', $dateStr)
            ->exists();
    }

    /**
     * Helper to resolve appointment duration in minutes.
     */
    protected function resolveDuration(
        Doctor $doctor,
        ?int $durationMinutes = null,
        ?int $specialtyId = null,
        ?int $procedureId = null
    ): int {
        if ($durationMinutes && $durationMinutes > 0) {
            return $durationMinutes;
        }

        if ($procedureId) {
            $procedure = Procedure::find($procedureId);
            if ($procedure) {
                return $procedure->duration_minutes ?: 30;
            }
        }

        if ($specialtyId) {
            $specialty = Specialty::find($specialtyId);
            if ($specialty) {
                return $specialty->default_duration_minutes ?: 30;
            }
        }

        return $doctor->default_slot_duration_minutes ?: 30;
    }
}
