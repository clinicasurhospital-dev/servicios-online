<?php

namespace App\Domain\Clinical\Enums;

enum FieldType: string
{
    case TEXT = 'TEXT';
    case TEXTAREA = 'TEXTAREA';
    case INTEGER = 'INTEGER';
    case DECIMAL = 'DECIMAL';
    case DATE = 'DATE';
    case DATETIME = 'DATETIME';
    case TIME = 'TIME';
    case BOOLEAN = 'BOOLEAN';
    case SELECT = 'SELECT';
    case MULTISELECT = 'MULTISELECT';
    case RADIO = 'RADIO';
    case CHECKBOX = 'CHECKBOX';
    case FILE = 'FILE';
    case IMAGE = 'IMAGE';
    case DIAGNOSIS = 'DIAGNOSIS';
    case MEDICATION = 'MEDICATION';
    case CLINICAL_CATALOG = 'CLINICAL_CATALOG';

    public function label(): string
    {
        return match ($this) {
            self::TEXT => 'Texto Corto (Línea simple)',
            self::TEXTAREA => 'Texto Largo (Párrafo / Notas)',
            self::INTEGER => 'Número Entero',
            self::DECIMAL => 'Número Decimal (Signos / Medidas)',
            self::DATE => 'Fecha',
            self::DATETIME => 'Fecha y Hora',
            self::TIME => 'Hora',
            self::BOOLEAN => 'Interruptor Sí / No',
            self::SELECT => 'Desplegable (Opción única)',
            self::MULTISELECT => 'Selección Múltiple (Etiquetas)',
            self::RADIO => 'Botones de Radio (Única opción)',
            self::CHECKBOX => 'Casillas de Verificación',
            self::FILE => 'Documento Adjunto (PDF/DOC)',
            self::IMAGE => 'Imagen Clínica / Foto',
            self::DIAGNOSIS => 'Selector CIE-10 Diagnóstico',
            self::MEDICATION => 'Prescripción de Medicamento',
            self::CLINICAL_CATALOG => 'Catálogo Clínico Parametrizado',
        };
    }

    public function isOptionBased(): bool
    {
        return in_array($this, [self::SELECT, self::MULTISELECT, self::RADIO, self::CHECKBOX], true);
    }

    public function isNumeric(): bool
    {
        return in_array($this, [self::INTEGER, self::DECIMAL], true);
    }
}
