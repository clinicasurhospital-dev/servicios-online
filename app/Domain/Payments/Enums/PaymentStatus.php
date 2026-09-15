<?php

namespace App\Domain\Payments\Enums;

enum PaymentStatus: string
{
    case PENDING = 'PENDING';
    case PAID = 'PAID';
    case REJECTED = 'REJECTED';
    case REFUNDED = 'REFUNDED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente de Pago',
            self::PAID => 'Pagado',
            self::REJECTED => 'Rechazado',
            self::REFUNDED => 'Reembolsado',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::PAID => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            self::PENDING => 'bg-amber-50 text-amber-700 border border-amber-200',
            self::REJECTED => 'bg-rose-50 text-rose-700 border border-rose-200',
            self::REFUNDED => 'bg-slate-100 text-slate-700 border border-slate-300',
        };
    }
}
