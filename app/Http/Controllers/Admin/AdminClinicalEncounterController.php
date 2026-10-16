<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Clinical\Services\ClinicalEncounterService;
use App\Domain\Clinical\Services\ClinicalRecordService;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClinicalEncounter;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminClinicalEncounterController extends Controller
{
    public function __construct(
        protected ClinicalEncounterService $encounterService,
        protected ClinicalRecordService $recordService
    ) {}

    /**
     * Start clinical encounter from an appointment.
     */
    public function startFromAppointment(Appointment $appointment): RedirectResponse
    {
        $user = auth()->user();

        // If user is a Doctor, find their Doctor profile, otherwise use the assigned doctor in appointment
        $doctor = Doctor::where('user_id', $user->id)->first() ?? $appointment->doctor;

        if (!$doctor) {
            return redirect()->back()->with('error', 'No se pudo identificar al profesional médico para esta atención.');
        }

        try {
            $encounter = $this->encounterService->startEncounterFromAppointment($appointment, $doctor);

            return redirect()->route('admin.clinical.encounter.show', ['encounter' => $encounter->id])
                ->with('success', "Atención médica iniciada para {$appointment->patient->full_name}.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Medical Consultation Encounter View (Stepper, Vitals, Dynamic Fields, Diagnoses, Addendums).
     */
    public function show(ClinicalEncounter $encounter): View
    {
        $encounter->load([
            'patient.clinicalRecord.allergies',
            'patient.clinicalRecord.activeConditions',
            'patient.clinicalRecord.activeMedications',
            'doctor.user',
            'specialty',
            'appointment',
            'templateVersion.sections.fields.options',
            'values.field',
            'diagnoses',
            'addendums.author',
            'finalizedByUser',
        ]);

        $patientSummary = $this->recordService->getPatientSummary($encounter->patient);

        // Pre-index existing values by field_id and repeatable_index for easy blade access
        $existingValues = [];
        foreach ($encounter->values as $val) {
            if ($val->repeatable_index > 0) {
                $existingValues[$val->field_id][$val->repeatable_index] = $val->typed_value;
            } else {
                $existingValues[$val->field_id] = $val->typed_value;
            }
        }

        return view('admin.clinical.encounters.show', compact('encounter', 'patientSummary', 'existingValues'));
    }

    /**
     * Save Draft Encounter values.
     */
    public function saveDraft(Request $request, ClinicalEncounter $encounter): JsonResponse|RedirectResponse
    {
        try {
            $values = $request->input('values', []);
            $diagnoses = $request->input('diagnoses', []);
            $vitalSummary = $request->input('vital_summary', []);
            $reason = $request->input('reason_for_consultation');

            $this->encounterService->saveDraftValues($encounter, $values, $diagnoses, $vitalSummary, $reason);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Borrador guardado correctamente.',
                    'last_saved_at' => now()->format('H:i:s'),
                ]);
            }

            return redirect()->back()->with('success', 'Borrador guardado exitosamente.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Finalize Encounter.
     */
    public function finalize(Request $request, ClinicalEncounter $encounter): JsonResponse|RedirectResponse
    {
        try {
            $values = $request->input('values', []);
            $diagnoses = $request->input('diagnoses', []);
            $vitalSummary = $request->input('vital_summary', []);
            $reason = $request->input('reason_for_consultation');

            $this->encounterService->finalizeEncounter(
                $encounter,
                $values,
                $diagnoses,
                $vitalSummary,
                auth()->id(),
                $reason
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Atención médica finalizada y sellada correctamente.',
                    'redirect' => route('admin.clinical.encounter.show', ['encounter' => $encounter->id]),
                ]);
            }

            return redirect()->route('admin.clinical.encounter.show', ['encounter' => $encounter->id])
                ->with('success', 'Atención médica finalizada con éxito. El registro ha sido cerrado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faltan campos obligatorios o hay valores con formato incorrecto.',
                    'errors' => $e->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Addendum to a Finalized Encounter.
     */
    public function storeAddendum(Request $request, ClinicalEncounter $encounter): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        try {
            $this->encounterService->createAddendum(
                $encounter,
                $validated['reason'],
                $validated['content'],
                auth()->id()
            );

            return redirect()->back()->with('success', 'Adenda médica registrada con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
