<?php

namespace App\Domain\Appointments\Enums;

enum AppointmentStatus: string
{
    case PENDING = 'PENDING';
    case PENDING_PAYMENT = 'PENDING_PAYMENT';
    case CONFIRMED = 'CONFIRMED';
    case RESCHEDULED = 'RESCHEDULED';
    case CANCELLED = 'CANCELLED';
    case COMPLETED = 'COMPLETED';
    case NO_SHOW = 'NO_SHOW';

    /**
     * Human-readable label in Spanish.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::PENDING_PAYMENT => 'Pendiente de Pago',
            self::CONFIRMED => 'Confirmada',
            self::RESCHEDULED => 'Reprogramada',
            self::CANCELLED => 'Cancelada',
            self::COMPLETED => 'Atendida',
            self::NO_SHOW => 'No asistió',
        };
    }

    /**
     * Tailwind badge classes according to UI/UX Guide.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::CONFIRMED => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            self::PENDING, self::PENDING_PAYMENT => 'bg-amber-50 text-amber-700 border border-amber-200',
            self::CANCELLED => 'bg-rose-50 text-rose-700 border border-rose-200',
            self::COMPLETED => 'bg-blue-50 text-blue-700 border border-blue-200',
            self::RESCHEDULED => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
            self::NO_SHOW => 'bg-slate-100 text-slate-700 border border-slate-300',
        };
    }

    /**
     * Determines if the appointment slot blocks new reservations.
     */
    public function blocksSlot(): bool
    {
        return in_array($this, [
            self::PENDING,
            self::PENDING_PAYMENT,
            self::CONFIRMED,
        ]);
    }

    /**
     * Determines if the appointment can be rescheduled.
     */
    public function canBeRescheduled(): bool
    {
        return in_array($this, [
            self::PENDING,
            self::PENDING_PAYMENT,
            self::CONFIRMED,
        ]);
    }

    /**
     * Determines if the appointment can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this, [
            self::PENDING,
            self::PENDING_PAYMENT,
            self::CONFIRMED,
        ]);
    }
}
