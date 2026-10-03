<?php

namespace App\Http\Controllers\Patient;

use App\Domain\Appointments\Services\AppointmentCancellationService;
use App\Domain\Appointments\Services\AppointmentRescheduleService;
use App\Domain\Availability\Services\AvailabilityService;
use App\Domain\Doctors\Services\DoctorReviewService;
use App\Domain\Payments\Services\BankTransferService;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\BankTransfer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientPortalController extends Controller
{
    public function __construct(
        protected AppointmentCancellationService $cancellationService,
        protected AppointmentRescheduleService $rescheduleService,
        protected AvailabilityService $availabilityService,
        protected BankTransferService $transferService,
        protected DoctorReviewService $reviewService
    ) {}

    public function dashboard(): View
    {
        $user = Auth::user();
        $patient = $user->patient;

        if (!$patient) {
            abort(403, 'Perfil de paciente no asociado.');
        }

        $nextAppointment = Appointment::where('patient_id', $patient->id)
            ->where('start_at', '>=', now())
            ->whereIn('status', ['PENDING', 'PENDING_PAYMENT', 'CONFIRMED', 'RESCHEDULED'])
            ->with(['doctor.user', 'specialty', 'procedure', 'payments'])
            ->orderBy('start_at', 'asc')
            ->first();

        // Buscar si tiene alguna cita atendida pendiente de valoración
        $pendingReviewAppointment = Appointment::where('patient_id', $patient->id)
            ->where('status', 'COMPLETED')
            ->whereDoesntHave('review', fn($q) => $q->whereNotNull('submitted_at'))
            ->with(['doctor.user', 'specialty', 'procedure'])
            ->orderBy('start_at', 'desc')
            ->first();

        $recentAppointments = Appointment::where('patient_id', $patient->id)
            ->with(['doctor.user', 'specialty', 'procedure', 'payments', 'review'])
            ->orderBy('start_at', 'desc')
            ->take(5)
            ->get();

        return view('patient.dashboard', compact('patient', 'nextAppointment', 'recentAppointments', 'pendingReviewAppointment'));
    }

    public function appointments(): View
    {
        $patient = Auth::user()->patient;
        if (!$patient) {
            abort(403, 'Perfil de paciente no asociado.');
        }

        $pendingReviewAppointment = Appointment::where('patient_id', $patient->id)
            ->where('status', 'COMPLETED')
            ->whereDoesntHave('review', fn($q) => $q->whereNotNull('submitted_at'))
            ->with(['doctor.user', 'specialty', 'procedure'])
            ->orderBy('start_at', 'desc')
            ->first();

        $appointments = Appointment::where('patient_id', $patient->id)
            ->with(['doctor.user', 'specialty', 'procedure', 'payments.bankTransfer', 'histories', 'review'])
            ->orderBy('start_at', 'desc')
            ->paginate(10);

        return view('patient.appointments', compact('patient', 'appointments', 'pendingReviewAppointment'));
    }

    public function showAppointment(Appointment $appointment): View
    {
        $this->authorize('view', $appointment);

        $appointment->load(['doctor.user', 'specialty', 'procedure', 'payments.bankTransfer', 'histories.user', 'cancelledBy', 'review']);

        return view('patient.appointment-show', compact('appointment'));
    }

    public function availableSlots(Request $request, Appointment $appointment): JsonResponse
    {
        $this->authorize('view', $appointment);

        $dateStr = $request->query('date');
        if (!$dateStr) {
            return response()->json(['success' => false, 'message' => 'Fecha requerida', 'slots' => []], 422);
        }

        try {
            $date = Carbon::parse($dateStr);
            $appointment->loadMissing(['doctor', 'specialty', 'procedure']);

            $slots = $this->availabilityService->getAvailableSlotsForDate(
                doctor: $appointment->doctor,
                date: $date,
                durationMinutes: $appointment->duration_minutes,
                specialtyId: $appointment->specialty_id,
                procedureId: $appointment->procedure_id,
                excludeAppointmentId: $appointment->id
            );

            $availableSlots = $slots->filter(fn($s) => $s->isAvailable)->map(fn($s) => [
                'time' => $s->startAt->format('H:i'),
                'label' => $s->startAt->format('h:i A') . ' - ' . $s->endAt->format('h:i A'),
                'start_at' => $s->startAt->format('Y-m-d H:i:s'),
                'end_at' => $s->endAt->format('Y-m-d H:i:s'),
            ])->values();

            return response()->json([
                'success' => true,
                'date' => $date->format('Y-m-d'),
                'slots' => $availableSlots,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar horarios: ' . $e->getMessage(),
                'slots' => [],
            ], 400);
        }
    }

    public function rescheduleAppointment(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);

        $validated = $request->validate([
            'new_date' => ['required', 'date', 'after_or_equal:today'],
            'new_time' => ['required', 'string', 'regex:/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'reason' => ['nullable', 'string', 'max:500'],
        ], [
            'new_date.required' => 'Debes seleccionar una fecha.',
            'new_date.after_or_equal' => 'La fecha no puede ser anterior a hoy.',
            'new_time.required' => 'Debes seleccionar un horario disponible.',
            'new_time.regex' => 'El formato de hora no es válido.',
        ]);

        try {
            $newStartAt = Carbon::parse("{$validated['new_date']} {$validated['new_time']}");
            $duration = $appointment->duration_minutes ?: 30;
            $newEndAt = $newStartAt->copy()->addMinutes($duration);

            $this->rescheduleService->reschedule(
                appointment: $appointment,
                newStartAt: $newStartAt,
                newEndAt: $newEndAt,
                userId: Auth::id(),
                reason: $validated['reason'] ?? 'Reprogramación solicitada desde el portal del paciente'
            );

            return back()->with('success', '¡Cita reprogramada exitosamente para el ' . $newStartAt->translatedFormat('l, d \d\e F \a \l\a\s H:i') . '!');
        } catch (\Exception $e) {
            return back()->withErrors(['reschedule' => $e->getMessage()])->withInput();
        }
    }

    public function cancelAppointment(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('cancel', $appointment);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'Por favor indícanos el motivo de la cancelación.',
        ]);

        try {
            $this->cancellationService->cancel($appointment, $validated['reason'], Auth::id());
            return back()->with('success', 'La cita #' . $appointment->code . ' ha sido cancelada correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['cancel' => $e->getMessage()]);
        }
    }

    public function submitTransfer(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('view', $appointment);

        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'reference_number' => ['required', 'string', 'max:50'],
            'transfer_date' => ['required', 'date'],
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // 5MB max
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->transferService->submitTransfer(
            appointment: $appointment,
            data: $validated,
            receiptFile: $request->file('receipt')
        );

        return back()->with('success', 'Comprobante de transferencia enviado. Nuestro equipo de recepción lo validará a la brevedad.');
    }

    public function paymentSuccess(): View
    {
        return view('patient.payment-success');
    }

    public function downloadClinicalRecord(Appointment $appointment, string $type): StreamedResponse|RedirectResponse
    {
        $this->authorize('view', $appointment);

        $isProcedure = in_array(strtolower($type), ['procedimiento', 'procedimientos', 'resultado', 'resultados', 'estudio']);
        $path = $isProcedure ? $appointment->procedure_document_path : $appointment->clinical_history_path;
        $name = $isProcedure 
            ? ($appointment->procedure_document_filename ?: "resultado_procedimiento_{$appointment->code}.pdf") 
            : ($appointment->clinical_history_filename ?: "historia_clinica_{$appointment->code}.pdf");

        if (! $path || ! Storage::disk('medical_records')->exists($path)) {
            return back()->with('error', 'El documento clínico solicitado no está disponible para descarga o aún no ha sido cargado.');
        }

        return Storage::disk('medical_records')->download($path, $name);
    }

    public function storeReview(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('view', $appointment);

        if ($appointment->status->value !== 'COMPLETED') {
            return back()->with('error', 'Solo es posible calificar y dejar sugerencias para citas médicas que ya hayan sido atendidas.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'attention_rating' => ['nullable', 'integer', 'between:1,5'],
            'punctuality_rating' => ['nullable', 'integer', 'between:1,5'],
            'facilities_rating' => ['nullable', 'integer', 'between:1,5'],
            'recommendation_score' => ['nullable', 'integer', 'between:1,10'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'is_anonymous' => ['nullable', 'boolean'],
        ], [
            'rating.required' => 'Por favor selecciona una calificación general en estrellas.',
            'rating.between' => 'La calificación debe ser entre 1 y 5 estrellas.',
            'comment.max' => 'El comentario no puede superar los 2000 caracteres.',
        ]);

        $review = $this->reviewService->getOrCreateReviewForAppointment($appointment, 'PORTAL');
        $this->reviewService->submitReview($review, $validated, 'PORTAL');

        return back()->with('success', '¡Muchas gracias! Tu valoración y sugerencias han sido guardadas exitosamente.');
    }
}

