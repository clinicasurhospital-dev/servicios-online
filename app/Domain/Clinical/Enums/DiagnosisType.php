<?php

namespace App\Domain\Clinical\Enums;

enum DiagnosisType: string
{
    case PRIMARY = 'PRIMARY';
    case SECONDARY = 'SECONDARY';
    case PRESUMPTIVE = 'PRESUMPTIVE';
    case CONFIRMED = 'CONFIRMED';

    public function label(): string
    {
        return match ($this) {
            self::PRIMARY => 'Diagnóstico Principal',
            self::SECONDARY => 'Diagnóstico Secundario',
            self::PRESUMPTIVE => 'Presuntivo / En Estudio',
            self::CONFIRMED => 'Definitivo / Confirmado',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PRIMARY, self::CONFIRMED => 'bg-indigo-50 text-indigo-700 border-indigo-200 font-semibold',
            self::SECONDARY => 'bg-blue-50 text-blue-700 border-blue-200',
            self::PRESUMPTIVE => 'bg-amber-50 text-amber-700 border-amber-200 italic',
        };
    }
}
