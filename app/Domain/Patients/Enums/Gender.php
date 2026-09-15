<?php

namespace App\Domain\Patients\Enums;

enum Gender: string
{
    case MALE = 'MALE';
    case FEMALE = 'FEMALE';
    case OTHER = 'OTHER';

    public function label(): string
    {
        return match ($this) {
            self::MALE => 'Masculino',
            self::FEMALE => 'Femenino',
            self::OTHER => 'Otro / No especifica',
        };
    }
}
