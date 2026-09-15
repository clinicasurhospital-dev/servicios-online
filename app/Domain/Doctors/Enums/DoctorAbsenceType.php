<?php

namespace App\Domain\Doctors\Enums;

enum DoctorAbsenceType: string
{
    case VACATION = 'VACATION';
    case MEDICAL_LEAVE = 'MEDICAL_LEAVE';
    case CONGRESS = 'CONGRESS';
    case PERSONAL = 'PERSONAL';
    case OTHER = 'OTHER';

    public function label(): string
    {
        return match ($this) {
            self::VACATION => 'Vacaciones',
            self::MEDICAL_LEAVE => 'Licencia Médica / Reposo',
            self::CONGRESS => 'Congreso / Capacitación',
            self::PERSONAL => 'Asunto Personal',
            self::OTHER => 'Otro Motivo',
        };
    }
}
