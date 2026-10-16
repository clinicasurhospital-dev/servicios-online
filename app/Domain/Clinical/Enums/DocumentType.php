<?php

namespace App\Domain\Clinical\Enums;

enum DocumentType: string
{
    case LAB_REPORT = 'LAB_REPORT';
    case IMAGING = 'IMAGING';
    case SURGICAL_REPORT = 'SURGICAL_REPORT';
    case CONSENT_FORM = 'CONSENT_FORM';
    case EXTERNAL_RECORD = 'EXTERNAL_RECORD';
    case OTHER = 'OTHER';

    public function label(): string
    {
        return match ($this) {
            self::LAB_REPORT => 'Informe de Laboratorio',
            self::IMAGING => 'Estudio de Imagen (Rayos X, Eco, TAC, Resonancia)',
            self::SURGICAL_REPORT => 'Reporte Quirúrgico / Protocolo',
            self::CONSENT_FORM => 'Consentimiento Informado',
            self::EXTERNAL_RECORD => 'Historia Externa / Transferencia',
            self::OTHER => 'Otro Documento Clínico',
        };
    }
}
