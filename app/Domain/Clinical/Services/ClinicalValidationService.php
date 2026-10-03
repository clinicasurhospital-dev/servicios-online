<?php

namespace App\Domain\Clinical\Services;

use App\Domain\Clinical\Enums\FieldType;
use App\Models\ClinicalEncounter;
use App\Models\ClinicalTemplateField;
use App\Models\ClinicalTemplateVersion;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ClinicalValidationService
{
    /**
     * Validate an array of dynamic field values against a template version.
     * $values is mapped as: [ field_id => value ] or [ field_id => [ index => value ] ] for repeatable.
     */
    public function validateEncounterValues(ClinicalTemplateVersion $version, array $submittedValues, bool $isFinalizing = false): array
    {
        $errors = [];

        foreach ($version->sections()->with('fields.options')->get() as $section) {
            // If section is required, ensure at least one field has value
            $hasSectionValue = false;

            foreach ($section->fields as $field) {
                $fieldId = $field->id;
                $rawVal = $submittedValues[$fieldId] ?? null;

                // Handle repeatable fields
                $valuesList = is_array($rawVal) && !in_array($field->field_type, [FieldType::MULTISELECT, FieldType::CHECKBOX, FieldType::DIAGNOSIS, FieldType::MEDICATION])
                    ? $rawVal 
                    : [0 => $rawVal];

                foreach ($valuesList as $repIndex => $val) {
                    $fieldError = $this->validateSingleField($field, $val, $isFinalizing);
                    if ($fieldError) {
                        $errors["field_{$fieldId}"] = $fieldError;
                    }
                    if ($val !== null && $val !== '' && $val !== []) {
                        $hasSectionValue = true;
                    }
                }
            }

            if ($isFinalizing && $section->is_required && !$hasSectionValue) {
                $errors["section_{$section->id}"] = "La sección '{$section->name}' es obligatoria y debe contener información.";
            }
        }

        if (!empty($errors) && $isFinalizing) {
            throw ValidationException::withMessages($errors);
        }

        return $errors;
    }

    /**
     * Validate a single field according to its type, rules, and requirement.
     */
    public function validateSingleField(ClinicalTemplateField $field, mixed $value, bool $isFinalizing): ?string
    {
        $isEmpty = ($value === null || $value === '' || (is_array($value) && empty($value)));

        // 1. Required Check
        if ($field->is_required && $isFinalizing && $isEmpty) {
            return "El campo '{$field->label}' es obligatorio.";
        }

        if ($isEmpty) {
            return null; // If not required and empty, no further format validation needed
        }

        $rules = $field->validation_rules ?? [];

        // 2. Type Specific Format and Range Validation
        switch ($field->field_type) {
            case FieldType::INTEGER:
                if (!filter_var($value, FILTER_VALIDATE_INT) && !is_int($value) && $value !== '0' && $value !== 0) {
                    return "El campo '{$field->label}' debe ser un número entero válido.";
                }
                $num = (int)$value;
                if (isset($rules['min']) && $num < $rules['min']) {
                    return "El campo '{$field->label}' no puede ser menor a {$rules['min']}" . ($field->unit ? " {$field->unit}" : '') . ".";
                }
                if (isset($rules['max']) && $num > $rules['max']) {
                    return "El campo '{$field->label}' no puede exceder {$rules['max']}" . ($field->unit ? " {$field->unit}" : '') . ".";
                }
                break;

            case FieldType::DECIMAL:
                if (!is_numeric($value)) {
                    return "El campo '{$field->label}' debe ser un valor numérico.";
                }
                $num = (float)$value;
                if (isset($rules['min']) && $num < (float)$rules['min']) {
                    return "El campo '{$field->label}' no puede ser menor a {$rules['min']}" . ($field->unit ? " {$field->unit}" : '') . ".";
                }
                if (isset($rules['max']) && $num > (float)$rules['max']) {
                    return "El campo '{$field->label}' no puede exceder {$rules['max']}" . ($field->unit ? " {$field->unit}" : '') . ".";
                }
                break;

            case FieldType::TEXT:
            case FieldType::TEXTAREA:
                $str = (string)$value;
                if (isset($rules['min_length']) && mb_strlen($str) < (int)$rules['min_length']) {
                    return "El campo '{$field->label}' debe tener al menos {$rules['min_length']} caracteres.";
                }
                if (isset($rules['max_length']) && mb_strlen($str) > (int)$rules['max_length']) {
                    return "El campo '{$field->label}' no puede exceder {$rules['max_length']} caracteres.";
                }
                if (isset($rules['regex']) && !preg_match("/{$rules['regex']}/", $str)) {
                    return "El formato ingresado para '{$field->label}' no es válido.";
                }
                break;

            case FieldType::DATE:
                if (!strtotime((string)$value)) {
                    return "La fecha en '{$field->label}' no tiene un formato válido.";
                }
                break;

            case FieldType::SELECT:
            case FieldType::RADIO:
                $validValues = $field->options->pluck('value')->all();
                if (!empty($validValues) && !in_array((string)$value, $validValues, true)) {
                    return "La opción seleccionada para '{$field->label}' no es válida.";
                }
                break;

            case FieldType::MULTISELECT:
            case FieldType::CHECKBOX:
                if (is_array($value)) {
                    $validValues = $field->options->pluck('value')->all();
                    foreach ($value as $selected) {
                        if (!empty($validValues) && !in_array((string)$selected, $validValues, true)) {
                            return "Una o más opciones en '{$field->label}' no son válidas.";
                        }
                    }
                }
                break;
        }

        return null;
    }
}
