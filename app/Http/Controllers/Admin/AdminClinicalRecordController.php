<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Clinical\Services\ClinicalDocumentService;
use App\Domain\Clinical\Services\ClinicalRecordService;
use App\Http\Controllers\Controller;
use App\Models\ClinicalDocument;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminClinicalRecordController extends Controller
{
    public function __construct(
        protected ClinicalRecordService $recordService,
        protected ClinicalDocumentService $documentService
    ) {}

    /**
     * Display Patient Longitudinal Clinical History.
     */
    public function show(Request $request, Patient $patient): View
    {
        $user = auth()->user();
        $isReception = $user->hasRole('RECEPTION') && !$user->hasRole(['ADMIN', 'SUPER_ADMIN', 'DOCTOR']);

        $patient->load([
            'clinicalRecord.allergies',
            'clinicalRecord.conditions',
            'clinicalRecord.medications',
            'clinicalRecord.backgrounds',
            'clinicalRecord.encounters.doctor.user',
            'clinicalRecord.encounters.specialty',
            'clinicalRecord.encounters.primaryDiagnosis',
            'clinicalRecord.diagnoses',
            'clinicalRecord.documents.uploader',
        ]);

        $summary = $this->recordService->getPatientSummary($patient);
        $record = $summary['record'];

        // If Reception role: filter out sensitive notes / details
        if ($isReception) {
            $summary['recent_encounters']->each(function ($encounter) {
                $encounter->reason_for_consultation = 'Atención Asistencial';
                $encounter->setRelation('primaryDiagnosis', null);
            });
        }

        return view('admin.clinical.records.show', compact('patient', 'record', 'summary', 'isReception'));
    }

    /**
     * Store new Allergy.
     */
    public function storeAllergy(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'substance' => ['required', 'string', 'max:255'],
            'allergy_type' => ['required', 'string'],
            'severity' => ['required', 'string'],
            'reaction' => ['nullable', 'string', 'max:255'],
            'is_critical' => ['boolean'],
            'diagnosed_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $record = $this->recordService->getOrCreateRecordForPatient($patient);
        $this->recordService->addAllergy($record, $validated);

        return redirect()->back()->with('success', "Alergia a '{$validated['substance']}' registrada con éxito.");
    }

    /**
     * Store new Chronic Condition.
     */
    public function storeCondition(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'condition_name' => ['required', 'string', 'max:255'],
            'icd10_code' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'string'],
            'diagnosed_date' => ['nullable', 'date'],
            'is_clinical_alert' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $record = $this->recordService->getOrCreateRecordForPatient($patient);
        $this->recordService->addCondition($record, $validated);

        return redirect()->back()->with('success', "Condición '{$validated['condition_name']}' registrada con éxito.");
    }

    /**
     * Store habitual medication.
     */
    public function storeMedication(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'drug_name' => ['required', 'string', 'max:255'],
            'concentration' => ['nullable', 'string', 'max:100'],
            'dose' => ['nullable', 'string', 'max:100'],
            'frequency' => ['nullable', 'string', 'max:100'],
            'route' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'string'],
            'start_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $record = $this->recordService->getOrCreateRecordForPatient($patient);
        $this->recordService->addMedication($record, $validated);

        return redirect()->back()->with('success', "Medicamento '{$validated['drug_name']}' registrado con éxito.");
    }

    /**
     * Store Background.
     */
    public function storeBackground(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'relationship' => ['nullable', 'string', 'max:100'],
            'event_year' => ['nullable', 'string', 'max:20'],
            'institution' => ['nullable', 'string', 'max:255'],
            'details' => ['nullable', 'string'],
            'is_relevant' => ['boolean'],
        ]);

        $record = $this->recordService->getOrCreateRecordForPatient($patient);
        $this->recordService->addBackground($record, $validated);

        return redirect()->back()->with('success', 'Antecedente registrado con éxito.');
    }

    /**
     * Upload private clinical document.
     */
    public function uploadDocument(Request $request, Patient $patient): RedirectResponse
    {
        $request->validate([
            'document_file' => ['required', 'file', 'max:20480'], // Max 20MB
            'document_type' => ['required', 'string'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_sensitive' => ['boolean'],
        ]);

        $record = $this->recordService->getOrCreateRecordForPatient($patient);

        $this->documentService->uploadDocument(
            $record,
            $request->file('document_file'),
            $request->input('document_type'),
            $request->input('encounter_id'),
            $request->input('description'),
            (bool)$request->input('is_sensitive', false),
            auth()->id()
        );

        return redirect()->back()->with('success', 'Documento clínico adjuntado de manera segura.');
    }

    /**
     * Stream secure document download.
     */
    public function downloadDocument(ClinicalDocument $document): StreamedResponse
    {
        $user = auth()->user();

        // Check if Reception is attempting to download sensitive clinical document
        if ($user->hasRole('RECEPTION') && !$user->hasRole(['ADMIN', 'SUPER_ADMIN', 'DOCTOR']) && $document->is_sensitive) {
            abort(403, 'Acceso denegado. Este documento contiene información médica confidencial.');
        }

        return $this->documentService->streamDownload($document);
    }
}
