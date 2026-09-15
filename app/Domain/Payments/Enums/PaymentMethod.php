<?php

namespace App\Domain\Payments\Enums;

enum PaymentMethod: string
{
    case ONLINE = 'ONLINE';
    case BANK_TRANSFER = 'BANK_TRANSFER';
    case CASH = 'CASH';
    case CARD_ONSITE = 'CARD_ONSITE';

    public function label(): string
    {
        return match ($this) {
            self::ONLINE => 'Pago en Línea',
            self::BANK_TRANSFER => 'Transferencia Bancaria',
            self::CASH => 'Efectivo en Recepción',
            self::CARD_ONSITE => 'Tarjeta en Clínica',
        };
    }
}
