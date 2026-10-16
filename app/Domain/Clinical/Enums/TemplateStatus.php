<?php

namespace App\Domain\Clinical\Enums;

enum TemplateStatus: string
{
    case DRAFT = 'DRAFT';
    case PUBLISHED = 'PUBLISHED';
    case ARCHIVED = 'ARCHIVED';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Borrador',
            self::PUBLISHED => 'Publicada (Activa)',
            self::ARCHIVED => 'Archivada',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-amber-50 text-amber-700 border-amber-200',
            self::PUBLISHED => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::ARCHIVED => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }
}
