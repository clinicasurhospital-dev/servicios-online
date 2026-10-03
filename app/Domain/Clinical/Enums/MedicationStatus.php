<?php

namespace App\Domain\Clinical\Enums;

enum MedicationStatus: string
{
    case ACTIVE = 'ACTIVE';
    case SUSPENDED = 'SUSPENDED';
    case DISCONTINUED = 'DISCONTINUED';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Activo / En Toma',
            self::SUSPENDED => 'Suspendido Temporalmente',
            self::DISCONTINUED => 'Descontinuado / Finalizado',
        };
    }
}
