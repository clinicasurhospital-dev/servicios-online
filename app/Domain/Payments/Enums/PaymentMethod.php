<?php

namespace App\Domain\Payments\Enums;

enum PaymentMethod: string
{
    case ONLINE = 'ONLINE';
    case CREDIT_CARD = 'CREDIT_CARD';
    case PAYMENT_LINK = 'PAYMENT_LINK';
    case BANK_TRANSFER = 'BANK_TRANSFER';
    case TRANSFER = 'TRANSFER';
    case CASH = 'CASH';
    case CARD_ONSITE = 'CARD_ONSITE';

    public function label(): string
    {
        return match ($this) {
            self::CREDIT_CARD => 'Tarjeta de Débito / Crédito',
            self::PAYMENT_LINK => 'Enlace de Pago DataLink',
            self::ONLINE => 'Pago en Línea',
            self::BANK_TRANSFER, self::TRANSFER => 'Transferencia Bancaria',
            self::CASH => 'Efectivo en Recepción',
            self::CARD_ONSITE => 'Tarjeta en Clínica',
        };
    }
}
