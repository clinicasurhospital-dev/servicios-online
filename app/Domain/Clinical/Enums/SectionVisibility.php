<?php

namespace App\Domain\Clinical\Enums;

enum SectionVisibility: string
{
    case ALL = 'ALL';
    case DOCTOR_ONLY = 'DOCTOR_ONLY';
    case MEDICAL_STAFF = 'MEDICAL_STAFF';

    public function label(): string
    {
        return match ($this) {
            self::ALL => 'Todo el equipo asistencial',
            self::DOCTOR_ONLY => 'Solo Médicos Asignados',
            self::MEDICAL_STAFF => 'Médicos y Enfermería',
        };
    }
}
