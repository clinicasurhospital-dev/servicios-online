<?php

namespace App\Domain\Clinical\Enums;

enum AllergyType: string
{
    case MEDICATION = 'MEDICATION';
    case FOOD = 'FOOD';
    case ENVIRONMENTAL = 'ENVIRONMENTAL';
    case OTHER = 'OTHER';

    public function label(): string
    {
        return match ($this) {
            self::MEDICATION => 'Medicamento',
            self::FOOD => 'Alimento',
            self::ENVIRONMENTAL => 'Ambiental',
            self::OTHER => 'Otro',
        };
    }
}
