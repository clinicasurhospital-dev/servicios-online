<?php

namespace App\Domain\Clinical\Enums;

enum ConditionStatus: string
{
    case ACTIVE = 'ACTIVE';
    case CONTROLLED = 'CONTROLLED';
    case REMISSION = 'REMISSION';
    case RESOLVED = 'RESOLVED';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Activa / En Tratamiento',
            self::CONTROLLED => 'Controlada',
            self::REMISSION => 'En Remisión',
            self::RESOLVED => 'Resuelta / Curada',
        };
    }
}
