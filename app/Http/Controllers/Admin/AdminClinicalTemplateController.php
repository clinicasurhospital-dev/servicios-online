<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Clinical\Enums\FieldType;
use App\Domain\Clinical\Enums\SectionVisibility;
use App\Domain\Clinical\Enums\TemplateStatus;
use App\Domain\Clinical\Services\ClinicalTemplateService;
use App\Http\Controllers\Controller;
use App\Models\ClinicalTemplate;
use App\Models\ClinicalTemplateField;
use App\Models\ClinicalTemplateSection;
use App\Models\ClinicalTemplateVersion;
use App\Models\Specialty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminClinicalTemplateController extends Controller
{
    public function __construct(
        protected ClinicalTemplateService $templateService
    ) {}

    /**
     * Display listing of Clinical Templates.
     */
    public function index(Request $request): View
    {
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();

        $query = ClinicalTemplate::with(['specialty', 'publishedVersion', 'latestVersion', 'creator'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('specialty_id')) {
            $query->where('specialty_id', $request->specialty_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $templates = $query->paginate(15)->withQueryString();

        return view('admin.clinical.templates.index', compact('templates', 'specialties'));
    }

    /**
     * Store new Clinical Template and initial Draft version.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'specialty_id' => ['nullable', 'exists:specialties,id'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $template = $this->templateService->createTemplate($validated, auth()->id());

        return redirect()->route('admin.clinical.templates.builder', ['template' => $template->id])
            ->with('success', "Plantilla '{$template->name}' creada con éxito. Diseñe sus secciones y campos.");
    }

    /**
     * Template Builder UI (3 columns: Sections, Fields, Inspector & Live Preview).
     */
    public function builder(Request $request, ClinicalTemplate $template): View
    {
        $template->load([
            'specialty',
            'versions.sections.fields.options',
            'latestVersion.sections.fields.options',
            'publishedVersion.sections.fields.options',
        ]);

        // Default to editing the latest draft or create one if none in draft
        $activeVersion = $template->versions()
            ->where('status', TemplateStatus::DRAFT->value)
            ->latest('version_number')
            ->first();

        if (!$activeVersion) {
            $activeVersion = $template->latestVersion;
        }

        $fieldTypes = FieldType::cases();
        $visibilities = SectionVisibility::cases();

        return view('admin.clinical.templates.builder', compact('template', 'activeVersion', 'fieldTypes', 'visibilities'));
    }

    /**
     * Duplicate a template.
     */
    public function duplicate(ClinicalTemplate $template): RedirectResponse
    {
        $copy = $this->templateService->duplicateTemplate($template, auth()->id());

        return redirect()->route('admin.clinical.templates.builder', ['template' => $copy->id])
            ->with('success', "Plantilla duplicada con éxito como '{$copy->name}'.");
    }

    /**
     * Create a new draft version from an existing template.
     */
    public function newVersion(Request $request, ClinicalTemplate $template): RedirectResponse
    {
        $changelog = $request->input('changelog', 'Nueva versión creada.');
        $newVersion = $this->templateService->createNewVersionDraft($template, $changelog);

        return redirect()->route('admin.clinical.templates.builder', ['template' => $template->id])
            ->with('success', "Versión v{$newVersion->version_number} creada en borrador.");
    }

    /**
     * Publish a template version.
     */
    public function publishVersion(ClinicalTemplateVersion $version): RedirectResponse
    {
        $this->templateService->publishVersion($version, auth()->id());

        return redirect()->back()
            ->with('success', "Versión v{$version->version_number} de '{$version->template->name}' publicada e inmutable con éxito.");
    }

    /**
     * Add Section via API/AJAX.
     */
    public function storeSection(Request $request, ClinicalTemplateVersion $version): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_required' => ['boolean'],
            'is_repeatable' => ['boolean'],
            'visibility' => ['nullable', 'string'],
        ]);

        $section = $this->templateService->addSection($version, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Sección agregada exitosamente.',
            'section' => $section->load('fields.options'),
        ]);
    }

    /**
     * Add Field to Section via API/AJAX.
     */
    public function storeField(Request $request, ClinicalTemplateSection $section): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'code' => ['nullable', 'string', 'max:100'],
            'label' => ['required', 'string', 'max:255'],
            'field_type' => ['required', 'string'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'default_value' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:50'],
            'validation_rules' => ['nullable', 'array'],
            'is_required' => ['boolean'],
            'is_repeatable' => ['boolean'],
            'is_sensitive' => ['boolean'],
            'is_clinical_alert' => ['boolean'],
            'is_patient_visible' => ['boolean'],
            'help_text' => ['nullable', 'string', 'max:255'],
            'options' => ['nullable', 'array'],
        ]);

        $field = $this->templateService->addField($section, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Campo agregado exitosamente.',
            'field' => $field->load('options'),
        ]);
    }

    /**
     * Update Field in Section.
     */
    public function updateField(Request $request, ClinicalTemplateField $field): JsonResponse
    {
        if ($field->section->version->isPublished()) {
            return response()->json(['success' => false, 'message' => 'No se puede modificar una versión publicada.'], 422);
        }

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:100'],
            'placeholder' => ['nullable', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:50'],
            'is_required' => ['boolean'],
            'is_repeatable' => ['boolean'],
            'is_sensitive' => ['boolean'],
            'is_clinical_alert' => ['boolean'],
            'is_patient_visible' => ['boolean'],
            'help_text' => ['nullable', 'string', 'max:255'],
            'validation_rules' => ['nullable', 'array'],
            'options' => ['nullable', 'array'],
        ]);

        $field->update([
            'label' => $validated['label'],
            'code' => $validated['code'],
            'placeholder' => $validated['placeholder'] ?? null,
            'unit' => $validated['unit'] ?? null,
            'is_required' => $validated['is_required'] ?? false,
            'is_repeatable' => $validated['is_repeatable'] ?? false,
            'is_sensitive' => $validated['is_sensitive'] ?? false,
            'is_clinical_alert' => $validated['is_clinical_alert'] ?? false,
            'is_patient_visible' => $validated['is_patient_visible'] ?? true,
            'help_text' => $validated['help_text'] ?? null,
            'validation_rules' => $validated['validation_rules'] ?? null,
        ]);

        if (isset($validated['options']) && is_array($validated['options'])) {
            $this->templateService->setFieldOptions($field, $validated['options']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Campo actualizado exitosamente.',
            'field' => $field->fresh('options'),
        ]);
    }

    /**
     * Update Section in Version.
     */
    public function updateSection(Request $request, ClinicalTemplateSection $section): JsonResponse
    {
        if ($section->version->isPublished()) {
            return response()->json(['success' => false, 'message' => 'No se puede modificar una versión publicada.'], 422);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_required' => ['boolean'],
            'is_repeatable' => ['boolean'],
            'visibility' => ['nullable', 'string'],
        ]);

        $section->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_required' => $validated['is_required'] ?? false,
            'is_repeatable' => $validated['is_repeatable'] ?? false,
            'visibility' => $validated['visibility'] ?? 'ALL',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sección actualizada exitosamente.',
            'section' => $section->fresh('fields.options'),
        ]);
    }

    /**
     * Delete Section.
     */
    public function destroySection(ClinicalTemplateSection $section): JsonResponse
    {
        if ($section->version->isPublished()) {
            return response()->json(['success' => false, 'message' => 'No se puede eliminar de una versión publicada.'], 422);
        }

        $section->delete();

        return response()->json(['success' => true, 'message' => 'Sección eliminada exitosamente.']);
    }

    /**
     * Delete Field.
     */
    public function destroyField(ClinicalTemplateField $field): JsonResponse
    {
        if ($field->section->version->isPublished()) {
            return response()->json(['success' => false, 'message' => 'No se puede eliminar de una versión publicada.'], 422);
        }

        $field->delete();

        return response()->json(['success' => true, 'message' => 'Campo eliminado exitosamente.']);
    }

    /**
     * Reorder Sections.
     */
    public function reorderSections(Request $request, ClinicalTemplateVersion $version): JsonResponse
    {
        $orderedIds = $request->validate(['ordered_ids' => ['required', 'array']])['ordered_ids'];
        $this->templateService->reorderSections($version, $orderedIds);

        return response()->json(['success' => true]);
    }

    /**
     * Reorder Fields.
     */
    public function reorderFields(Request $request, ClinicalTemplateSection $section): JsonResponse
    {
        $orderedIds = $request->validate(['ordered_ids' => ['required', 'array']])['ordered_ids'];
        $this->templateService->reorderFields($section, $orderedIds);

        return response()->json(['success' => true]);
    }
}
