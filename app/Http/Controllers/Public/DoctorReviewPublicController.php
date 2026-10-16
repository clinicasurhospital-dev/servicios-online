<?php

namespace App\Http\Controllers\Public;

use App\Domain\Doctors\Services\DoctorReviewService;
use App\Http\Controllers\Controller;
use App\Models\DoctorReview;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorReviewPublicController extends Controller
{
    public function __construct(
        protected DoctorReviewService $reviewService
    ) {}

    /**
     * Muestra el formulario público interactivo para calificar al médico y dejar sugerencias.
     */
    public function show(string $token): View
    {
        $review = DoctorReview::where('token', $token)
            ->with(['appointment.doctor.user', 'appointment.specialty', 'appointment.procedure', 'patient'])
            ->firstOrFail();

        $doctor = $review->doctor;
        $appointment = $review->appointment;
        $patient = $review->patient;

        $isExpired = $review->token_expires_at && $review->token_expires_at->isPast();

        return view('public.doctor-review', compact('review', 'doctor', 'appointment', 'patient', 'isExpired'));
    }

    /**
     * Guarda la valoración y sugerencia enviada por el paciente mediante su enlace directo.
     */
    public function store(Request $request, string $token): RedirectResponse
    {
        $review = DoctorReview::where('token', $token)->firstOrFail();

        if ($review->isCompleted()) {
            return back()->with('info', 'Esta consulta médica ya cuenta con una valoración registrada. ¡Muchas gracias!');
        }

        if ($review->token_expires_at && $review->token_expires_at->isPast()) {
            return back()->with('error', 'El enlace de valoración ha expirado.');
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
            'rating.required' => 'Por favor selecciona una calificación general de estrellas.',
            'rating.between' => 'La calificación debe ser entre 1 y 5 estrellas.',
            'comment.max' => 'El comentario o sugerencia no puede exceder los 2000 caracteres.',
        ]);

        $channel = $review->channel ?: 'WHATSAPP';
        $this->reviewService->submitReview($review, $validated, $channel);

        return back()->with('success', '¡Muchas gracias por tu valoración y sugerencias! Tu opinión nos ayuda a ofrecer una atención médica cada vez mejor.');
    }
}
