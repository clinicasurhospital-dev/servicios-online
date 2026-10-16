<?php

namespace App\Domain\Clinical\Enums;

enum BackgroundCategory: string
{
    case PERSONAL = 'PERSONAL';
    case FAMILY = 'FAMILY';
    case SURGICAL = 'SURGICAL';
    case HABIT = 'HABIT';
    case GYNECO_OBSTETRIC = 'GYNECO_OBSTETRIC';
    case PEDIATRIC = 'PEDIATRIC';

    public function label(): string
    {
        return match ($this) {
            self::PERSONAL => 'Personal / Patológico',
            self::FAMILY => 'Familiar / Hereditario',
            self::SURGICAL => 'Quirúrgico / Traumático',
            self::HABIT => 'Hábitos / Estilo de Vida',
            self::GYNECO_OBSTETRIC => 'Gineco-Obstétrico',
            self::PEDIATRIC => 'Perinatal / Pediátrico',
        };
    }
}
