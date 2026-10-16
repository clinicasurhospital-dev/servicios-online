<?php

namespace App\Domain\Clinical\Services;

use App\Domain\Clinical\Enums\TemplateStatus;
use App\Models\ClinicalFieldOption;
use App\Models\ClinicalTemplate;
use App\Models\ClinicalTemplateField;
use App\Models\ClinicalTemplateSection;
use App\Models\ClinicalTemplateVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClinicalTemplateService
{
    public function __construct(
        protected ClinicalAuditService $auditService
    ) {}

    /**
     * Create a new Clinical Template with initial Version 1 Draft.
     */
    public function createTemplate(array $data, int $userId): ClinicalTemplate
    {
        return DB::transaction(function () use ($data, $userId) {
            $slug = Str::slug($data['name']);
            // Ensure unique slug
            $existingCount = ClinicalTemplate::where('slug', 'like', "{$slug}%")->count();
            if ($existingCount > 0) {
                $slug .= '-' . ($existingCount + 1);
            }

            $template = ClinicalTemplate::create([
                'specialty_id' => $data['specialty_id'] ?? null,
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'created_by' => $userId,
            ]);

            // Create initial Version 1 (DRAFT)
            $version = $template->versions()->create([
                'version_number' => 1,
                'status' => TemplateStatus::DRAFT->value,
                'changelog' => 'Versión inicial de plantilla.',
            ]);

            $this->auditService->log('TemplateCreated', $template, [
                'template_id' => $template->id,
                'name' => $template->name,
                'version' => 1,
            ]);

            return $template;
        });
    }

    /**
     * Create a new draft version from an existing template version (Versioning pattern).
     */
    public function createNewVersionDraft(ClinicalTemplate $template, ?string $changelog = null): ClinicalTemplateVersion
    {
        return DB::transaction(function () use ($template, $changelog) {
            $latestVersion = $template->latestVersion;
            $newVersionNumber = $latestVersion ? ($latestVersion->version_number + 1) : 1;

            $newVersion = $template->versions()->create([
                'version_number' => $newVersionNumber,
                'status' => TemplateStatus::DRAFT->value,
                'changelog' => $changelog ?? "Borrador de versión {$newVersionNumber}",
            ]);

            // Deep clone sections, fields, and options if a previous version exists
            if ($latestVersion) {
                foreach ($latestVersion->sections()->with('fields.options')->get() as $oldSection) {
                    $newSection = $newVersion->sections()->create([
                        'name' => $oldSection->name,
                        'description' => $oldSection->description,
                        'order' => $oldSection->order,
                        'is_active' => $oldSection->is_active,
                        'is_required' => $oldSection->is_required,
                        'is_repeatable' => $oldSection->is_repeatable,
                        'visibility' => $oldSection->visibility->value,
                    ]);

                    foreach ($oldSection->fields as $oldField) {
                        $newField = $newSection->fields()->create([
                            'name' => $oldField->name,
                            'code' => $oldField->code,
                            'label' => $oldField->label,
                            'description' => $oldField->description,
                            'field_type' => $oldField->field_type->value,
                            'placeholder' => $oldField->placeholder,
                            'default_value' => $oldField->default_value,
                            'unit' => $oldField->unit,
                            'validation_rules' => $oldField->validation_rules,
                            'order' => $oldField->order,
                            'is_required' => $oldField->is_required,
                            'is_repeatable' => $oldField->is_repeatable,
                            'is_sensitive' => $oldField->is_sensitive,
                            'is_clinical_alert' => $oldField->is_clinical_alert,
                            'is_patient_visible' => $oldField->is_patient_visible,
                            'is_active' => $oldField->is_active,
                            'help_text' => $oldField->help_text,
                        ]);

                        foreach ($oldField->options as $oldOpt) {
                            $newField->options()->create([
                                'label' => $oldOpt->label,
                                'value' => $oldOpt->value,
                                'order' => $oldOpt->order,
                                'is_default' => $oldOpt->is_default,
                            ]);
                        }
                    }
                }
            }

            $this->auditService->log('TemplateVersionCreated', $newVersion, [
                'template_id' => $template->id,
                'version_number' => $newVersionNumber,
            ]);

            return $newVersion;
        });
    }

    /**
     * Publish a template version (Seals it as immutable and active).
     */
    public function publishVersion(ClinicalTemplateVersion $version, int $userId): ClinicalTemplateVersion
    {
        return DB::transaction(function () use ($version, $userId) {
            $template = $version->template;

            // Mark previous published versions of this template as ARCHIVED
            $template->versions()
                ->where('id', '!=', $version->id)
                ->where('status', TemplateStatus::PUBLISHED->value)
                ->update(['status' => TemplateStatus::ARCHIVED->value]);

            $version->update([
                'status' => TemplateStatus::PUBLISHED->value,
                'published_at' => now(),
                'published_by' => $userId,
            ]);

            $this->auditService->log('TemplatePublished', $version, [
                'template_id' => $template->id,
                'version_number' => $version->version_number,
                'published_by' => $userId,
            ]);

            return $version->fresh();
        });
    }

    /**
     * Duplicate an entire template as a new template draft.
     */
    public function duplicateTemplate(ClinicalTemplate $template, int $userId): ClinicalTemplate
    {
        return DB::transaction(function () use ($template, $userId) {
            $copyData = [
                'specialty_id' => $template->specialty_id,
                'name' => "{$template->name} (Copia)",
                'description' => $template->description,
                'is_active' => true,
            ];

            $newTemplate = $this->createTemplate($copyData, $userId);
            $newVersion = $newTemplate->latestVersion;

            // Clone structure from active or latest version of source template
            $sourceVersion = $template->publishedVersion ?: $template->latestVersion;
            if ($sourceVersion) {
                // Delete default empty section if any
                $newVersion->sections()->delete();

                foreach ($sourceVersion->sections()->with('fields.options')->get() as $oldSection) {
                    $newSection = $newVersion->sections()->create([
                        'name' => $oldSection->name,
                        'description' => $oldSection->description,
                        'order' => $oldSection->order,
                        'is_active' => $oldSection->is_active,
                        'is_required' => $oldSection->is_required,
                        'is_repeatable' => $oldSection->is_repeatable,
                        'visibility' => $oldSection->visibility->value,
                    ]);

                    foreach ($oldSection->fields as $oldField) {
                        $newField = $newSection->fields()->create([
                            'name' => $oldField->name,
                            'code' => $oldField->code,
                            'label' => $oldField->label,
                            'description' => $oldField->description,
                            'field_type' => $oldField->field_type->value,
                            'placeholder' => $oldField->placeholder,
                            'default_value' => $oldField->default_value,
                            'unit' => $oldField->unit,
                            'validation_rules' => $oldField->validation_rules,
                            'order' => $oldField->order,
                            'is_required' => $oldField->is_required,
                            'is_repeatable' => $oldField->is_repeatable,
                            'is_sensitive' => $oldField->is_sensitive,
                            'is_clinical_alert' => $oldField->is_clinical_alert,
                            'is_patient_visible' => $oldField->is_patient_visible,
                            'is_active' => $oldField->is_active,
                            'help_text' => $oldField->help_text,
                        ]);

                        foreach ($oldField->options as $oldOpt) {
                            $newField->options()->create([
                                'label' => $oldOpt->label,
                                'value' => $oldOpt->value,
                                'order' => $oldOpt->order,
                                'is_default' => $oldOpt->is_default,
                            ]);
                        }
                    }
                }
            }

            return $newTemplate;
        });
    }

    /**
     * Add Section to a Template Version (Draft only).
     */
    public function addSection(ClinicalTemplateVersion $version, array $data): ClinicalTemplateSection
    {
        if ($version->isPublished()) {
            throw new \DomainException("No se pueden modificar secciones de una versión publicada. Cree una nueva versión en borrador.");
        }

        $nextOrder = ($version->sections()->max('order') ?? 0) + 1;

        return $version->sections()->create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'order' => $data['order'] ?? $nextOrder,
            'is_active' => $data['is_active'] ?? true,
            'is_required' => $data['is_required'] ?? false,
            'is_repeatable' => $data['is_repeatable'] ?? false,
            'visibility' => $data['visibility'] ?? 'ALL',
        ]);
    }

    /**
     * Add Field to a Section (Draft only).
     */
    public function addField(ClinicalTemplateSection $section, array $data): ClinicalTemplateField
    {
        if ($section->version->isPublished()) {
            throw new \DomainException("No se pueden agregar campos a una versión publicada.");
        }

        $nextOrder = ($section->fields()->max('order') ?? 0) + 1;
        $code = Str::slug($data['code'] ?? $data['name'], '_');

        $field = $section->fields()->create([
            'name' => $data['name'],
            'code' => $code,
            'label' => $data['label'] ?? $data['name'],
            'description' => $data['description'] ?? null,
            'field_type' => $data['field_type'],
            'placeholder' => $data['placeholder'] ?? null,
            'default_value' => $data['default_value'] ?? null,
            'unit' => $data['unit'] ?? null,
            'validation_rules' => $data['validation_rules'] ?? null,
            'order' => $data['order'] ?? $nextOrder,
            'is_required' => $data['is_required'] ?? false,
            'is_repeatable' => $data['is_repeatable'] ?? false,
            'is_sensitive' => $data['is_sensitive'] ?? false,
            'is_clinical_alert' => $data['is_clinical_alert'] ?? false,
            'is_patient_visible' => $data['is_patient_visible'] ?? true,
            'is_active' => $data['is_active'] ?? true,
            'help_text' => $data['help_text'] ?? null,
        ]);

        if (!empty($data['options']) && is_array($data['options'])) {
            $this->setFieldOptions($field, $data['options']);
        }

        return $field;
    }

    /**
     * Set / Replace options for a field.
     */
    public function setFieldOptions(ClinicalTemplateField $field, array $options): void
    {
        $field->options()->delete();
        $order = 1;
        foreach ($options as $opt) {
            $label = is_array($opt) ? ($opt['label'] ?? '') : (string)$opt;
            $value = is_array($opt) ? ($opt['value'] ?? $label) : (string)$opt;
            if (trim($label) !== '') {
                $field->options()->create([
                    'label' => trim($label),
                    'value' => trim($value),
                    'order' => $order++,
                    'is_default' => is_array($opt) ? ($opt['is_default'] ?? false) : false,
                ]);
            }
        }
    }

    /**
     * Reorder sections.
     */
    public function reorderSections(ClinicalTemplateVersion $version, array $orderedIds): void
    {
        if ($version->isPublished()) {
            throw new \DomainException("No se puede reordenar una versión publicada.");
        }

        foreach ($orderedIds as $index => $id) {
            $version->sections()->where('id', $id)->update(['order' => $index + 1]);
        }
    }

    /**
     * Reorder fields in section.
     */
    public function reorderFields(ClinicalTemplateSection $section, array $orderedIds): void
    {
        if ($section->version->isPublished()) {
            throw new \DomainException("No se puede reordenar una versión publicada.");
        }

        foreach ($orderedIds as $index => $id) {
            $section->fields()->where('id', $id)->update(['order' => $index + 1]);
        }
    }

    /**
     * Get the active published template version for a specialty or the general template.
     */
    public function getActiveTemplateForSpecialty(?int $specialtyId = null): ?ClinicalTemplateVersion
    {
        if ($specialtyId) {
            $template = ClinicalTemplate::where('specialty_id', $specialtyId)
                ->where('is_active', true)
                ->first();

            if ($template && $template->publishedVersion) {
                return $template->publishedVersion;
            }
        }

        // Fallback to General Template (specialty_id = null or slug like 'medicina-general')
        $generalTemplate = ClinicalTemplate::whereNull('specialty_id')
            ->where('is_active', true)
            ->first() ?? ClinicalTemplate::where('is_active', true)->first();

        return $generalTemplate?->publishedVersion ?: $generalTemplate?->latestVersion;
    }
}
