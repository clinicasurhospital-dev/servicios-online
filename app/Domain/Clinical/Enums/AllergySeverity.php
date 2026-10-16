<?php

namespace App\Domain\Clinical\Enums;

enum AllergySeverity: string
{
    case MILD = 'MILD';
    case MODERATE = 'MODERATE';
    case SEVERE = 'SEVERE';
    case LIFE_THREATENING = 'LIFE_THREATENING';

    public function label(): string
    {
        return match ($this) {
            self::MILD => 'Leve',
            self::MODERATE => 'Moderada',
            self::SEVERE => 'Severa',
            self::LIFE_THREATENING => 'Potencialmente Mortal (Anafilaxia)',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::MILD => 'bg-amber-50 text-amber-700 border-amber-200',
            self::MODERATE => 'bg-orange-50 text-orange-700 border-orange-200',
            self::SEVERE, self::LIFE_THREATENING => 'bg-red-50 text-red-700 border-red-200 ring-1 ring-red-400',
        };
    }
}
