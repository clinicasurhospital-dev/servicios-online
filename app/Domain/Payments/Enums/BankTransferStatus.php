<?php

namespace App\Domain\Payments\Enums;

enum BankTransferStatus: string
{
    case PENDING_VALIDATION = 'PENDING_VALIDATION';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING_VALIDATION => 'Pendiente de Validación',
            self::APPROVED => 'Aprobada',
            self::REJECTED => 'Rechazada',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::APPROVED => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            self::PENDING_VALIDATION => 'bg-amber-50 text-amber-700 border border-amber-200',
            self::REJECTED => 'bg-rose-50 text-rose-700 border border-rose-200',
        };
    }
}
