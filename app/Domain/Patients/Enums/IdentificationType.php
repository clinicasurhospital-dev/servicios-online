<?php

namespace App\Domain\Patients\Enums;

enum IdentificationType: string
{
    case CEDULA = 'CEDULA';
    case PASAPORTE = 'PASAPORTE';
    case RUC = 'RUC';

    public function label(): string
    {
        return match ($this) {
            self::CEDULA => 'Cédula de Identidad',
            self::PASAPORTE => 'Pasaporte',
            self::RUC => 'RUC',
        };
    }
}
