<?php

namespace App\Domain\Clinical\Enums;

enum EncounterStatus: string
{
    case DRAFT = 'DRAFT';
    case IN_PROGRESS = 'IN_PROGRESS';
    case FINALIZED = 'FINALIZED';
    case AMENDED = 'AMENDED';
    case CANCELLED = 'CANCELLED';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::IN_PROGRESS => 'En Consulta',
            self::FINALIZED => 'Atención Finalizada (Cerrada)',
            self::AMENDED => 'Finalizada con Adenda(s)',
            self::CANCELLED => 'Atención Cancelada',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-amber-50 text-amber-700 border-amber-200',
            self::IN_PROGRESS => 'bg-blue-50 text-blue-700 border-blue-200',
            self::FINALIZED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::AMENDED => 'bg-purple-50 text-purple-700 border-purple-200',
            self::CANCELLED => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::DRAFT, self::IN_PROGRESS], true);
    }
}
