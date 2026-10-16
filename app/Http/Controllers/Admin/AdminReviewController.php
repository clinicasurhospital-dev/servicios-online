<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Doctors\Services\DoctorReviewService;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function __construct(
        protected DoctorReviewService $reviewService
    ) {}

    /**
     * Muestra el listado de sugerencias y valoraciones médicas con KPIs y filtros avanzados.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $isDoctor = $user->isDoctor();
        $doctorProfile = $isDoctor ? $user->doctor : null;

        $doctorId = $request->query('doctor_id');
        $rating = $request->query('rating');
        $channel = $request->query('channel');
        $hasComment = $request->query('has_comment');
        $search = $request->query('search');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        // Si es médico, solo puede ver sus propias valoraciones
        if ($isDoctor && $doctorProfile) {
            $doctorId = $doctorProfile->id;
        }

        $query = DoctorReview::query()
            ->whereNotNull('submitted_at')
            ->with(['doctor.user', 'patient', 'appointment.specialty', 'appointment.procedure']);

        // Aplicar filtros
        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        if ($rating) {
            $query->where('rating', (int) $rating);
        }

        if ($channel) {
            $query->where('channel', strtoupper($channel));
        }

        if ($hasComment === '1' || $hasComment === 'yes') {
            $query->whereNotNull('comment')->where('comment', '!=', '');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($pq) use ($search) {
                      $pq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('doctor.user', function ($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('appointment', function ($aq) use ($search) {
                      $aq->where('code', 'like', "%{$search}%");
                  });
            });
        }

        if ($dateFrom) {
            $query->whereDate('submitted_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('submitted_at', '<=', $dateTo);
        }

        $reviews = $query->orderBy('submitted_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Obtener KPIs y estadísticas agregadas
        $stats = $this->reviewService->getRatingStats($doctorId ? (int) $doctorId : null);

        // Lista de médicos para el filtro
        $doctors = Doctor::where('is_active', true)->with('user')->get();

        return view('admin.reviews', compact(
            'reviews',
            'stats',
            'doctors',
            'doctorId',
            'rating',
            'channel',
            'hasComment',
            'search',
            'dateFrom',
            'dateTo',
            'isDoctor'
        ));
    }

    /**
     * Genera y envía invitación de valoración (WhatsApp o Correo) para una cita atendida.
     */
    public function sendInvitation(Request $request, Appointment $appointment): JsonResponse|RedirectResponse
    {
        $channel = $request->input('channel', 'WHATSAPP');
        $review = $this->reviewService->getOrCreateReviewForAppointment($appointment, $channel);
        $reviewUrl = $this->reviewService->getReviewUrl($appointment);
        $whatsappUrl = $this->reviewService->getWhatsAppShareUrl($appointment);

        if ($channel === 'EMAIL') {
            $sent = $this->reviewService->sendEmailInvitation($appointment);
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => $sent,
                    'message' => $sent ? 'Invitación enviada por correo exitosamente.' : 'No se pudo enviar el correo (verifica el correo del paciente).',
                    'review_url' => $reviewUrl,
                ]);
            }

            return back()->with(
                $sent ? 'success' : 'error',
                $sent ? 'Invitación enviada por correo exitosamente.' : 'No se pudo enviar el correo al paciente.'
            );
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'whatsapp_url' => $whatsappUrl,
                'review_url' => $reviewUrl,
                'patient_phone' => $appointment->patient?->phone,
            ]);
        }

        return redirect()->away($whatsappUrl);
    }

    /**
     * Alterna la visibilidad pública / moderación de una sugerencia o valoración.
     */
    public function toggleVisibility(DoctorReview $review): RedirectResponse
    {
        $user = auth()->user();
        if ($user->isDoctor()) {
            abort(403, 'Solo administradores pueden moderar la visibilidad de las opiniones.');
        }

        $review->update(['is_visible' => !$review->is_visible]);

        $statusText = $review->is_visible ? 'visible' : 'oculta';
        return back()->with('success', "La valoración #{$review->id} ahora está {$statusText}.");
    }
}
