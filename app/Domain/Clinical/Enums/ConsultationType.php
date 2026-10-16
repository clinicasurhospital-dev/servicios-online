<?php

namespace App\Domain\Clinical\Enums;

enum ConsultationType: string
{
    case INITIAL = 'INITIAL';
    case FOLLOW_UP = 'FOLLOW_UP';
    case CONTROL = 'CONTROL';
    case EMERGENCY = 'EMERGENCY';
    case OTHER = 'OTHER';

    public function label(): string
    {
        return match ($this) {
            self::INITIAL => 'Primera Vez / Inicial',
            self::FOLLOW_UP => 'Subsecuente / Seguimiento',
            self::CONTROL => 'Control Rutinario',
            self::EMERGENCY => 'Urgencia Asistencial',
            self::OTHER => 'Otra Modalidad',
        };
    }
}
