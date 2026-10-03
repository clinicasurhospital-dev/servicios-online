<?php

namespace App\Domain\Doctors\Services;

use App\Mail\DoctorReviewInvitationMail;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorReview;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DoctorReviewService
{
    /**
     * Obtiene o inicializa el registro de valoración con token único para una cita médica.
     */
    public function getOrCreateReviewForAppointment(Appointment $appointment, string $channel = 'PORTAL'): DoctorReview
    {
        $review = DoctorReview::where('appointment_id', $appointment->id)->first();

        if (!$review) {
            $token = DoctorReview::generateUniqueToken();
            $review = DoctorReview::create([
                'appointment_id' => $appointment->id,
                'doctor_id' => $appointment->doctor_id,
                'patient_id' => $appointment->patient_id,
                'rating' => 5,
                'channel' => $channel,
                'token' => $token,
                'token_expires_at' => Carbon::now()->addDays(30),
                'is_visible' => true,
            ]);
        } elseif (!$review->token) {
            $review->update([
                'token' => DoctorReview::generateUniqueToken(),
                'token_expires_at' => Carbon::now()->addDays(30),
            ]);
        }

        return $review;
    }

    /**
     * Genera la URL pública directa para calificar la cita médica.
     */
    public function getReviewUrl(Appointment $appointment): string
    {
        $review = $this->getOrCreateReviewForAppointment($appointment);
        return route('reviews.public.show', ['token' => $review->token]);
    }

    /**
     * Genera el enlace directo para enviar por WhatsApp con mensaje pre-redactado.
     */
    public function getWhatsAppShareUrl(Appointment $appointment): string
    {
        $review = $this->getOrCreateReviewForAppointment($appointment, 'WHATSAPP');
        $reviewUrl = route('reviews.public.show', ['token' => $review->token]);

        $patient = $appointment->patient;
        $doctorName = $appointment->doctor?->user?->name ?? 'tu médico especialista';
        $brandName = config('branding.client_name', 'Clínica Sur Hospital');

        // Limpiar teléfono del paciente (Ecuador: remover caracteres y formatear con código de país 593 si inicia con 09...)
        $phone = $patient?->phone ?? '';
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '09') && strlen($cleanPhone) === 10) {
            $cleanPhone = '593' . substr($cleanPhone, 1);
        }

        $patientFirstName = $patient ? explode(' ', $patient->first_name)[0] : 'Paciente';

        $text = "¡Hola, {$patientFirstName}! 👋 En *{$brandName}* nos interesa mucho conocer tu opinión sobre tu consulta con el *Dr(a). {$doctorName}*.\n\n"
              . "⭐ ¿Nos ayudas con tu valoración y sugerencias? Solo te tomará 1 minuto:\n"
              . "👉 {$reviewUrl}\n\n"
              . "¡Muchas gracias por confiar en nuestra atención de salud!";

        return "https://wa.me/{$cleanPhone}?text=" . rawurlencode($text);
    }

    /**
     * Envía invitación de valoración por correo electrónico.
     */
    public function sendEmailInvitation(Appointment $appointment): bool
    {
        try {
            $patient = $appointment->patient;
            $email = $patient?->email ?? $patient?->user?->email;

            if (!$email) {
                return false;
            }

            $review = $this->getOrCreateReviewForAppointment($appointment, 'EMAIL');
            $reviewUrl = route('reviews.public.show', ['token' => $review->token]);

            Mail::to($email)->send(new DoctorReviewInvitationMail(
                appointment: $appointment,
                reviewUrl: $reviewUrl,
                patientName: $patient->full_name,
                review: $review
            ));

            return true;
        } catch (\Throwable $e) {
            Log::error('Error al enviar correo de invitación a valoración médica: ' . $e->getMessage(), [
                'appointment_id' => $appointment->id,
                'exception' => $e,
            ]);
            return false;
        }
    }

    /**
     * Guarda la valoración y sugerencia completada por el paciente.
     */
    public function submitReview(DoctorReview $review, array $data, ?string $channel = null): DoctorReview
    {
        $updateData = [
            'rating' => (int) ($data['rating'] ?? 5),
            'attention_rating' => isset($data['attention_rating']) ? (int) $data['attention_rating'] : null,
            'punctuality_rating' => isset($data['punctuality_rating']) ? (int) $data['punctuality_rating'] : null,
            'facilities_rating' => isset($data['facilities_rating']) ? (int) $data['facilities_rating'] : null,
            'recommendation_score' => isset($data['recommendation_score']) ? (int) $data['recommendation_score'] : null,
            'comment' => !empty($data['comment']) ? trim($data['comment']) : null,
            'is_anonymous' => !empty($data['is_anonymous']),
            'submitted_at' => Carbon::now(),
            'is_visible' => true,
        ];

        if ($channel) {
            $updateData['channel'] = $channel;
        }

        $review->update($updateData);

        return $review->fresh();
    }

    /**
     * Calcula estadísticas agregadas de valoraciones (globales o por médico).
     */
    public function getRatingStats(?int $doctorId = null): array
    {
        $query = DoctorReview::query()
            ->whereNotNull('submitted_at')
            ->where('is_visible', true);

        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        $totalReviews = (clone $query)->count();
        $averageRating = (float) round((clone $query)->avg('rating') ?: 5.0, 1);
        $avgAttention = (float) round((clone $query)->whereNotNull('attention_rating')->avg('attention_rating') ?: 5.0, 1);
        $avgPunctuality = (float) round((clone $query)->whereNotNull('punctuality_rating')->avg('punctuality_rating') ?: 5.0, 1);
        $avgFacilities = (float) round((clone $query)->whereNotNull('facilities_rating')->avg('facilities_rating') ?: 5.0, 1);

        // Desglose de estrellas (1 a 5)
        $starCounts = [
            5 => (clone $query)->where('rating', 5)->count(),
            4 => (clone $query)->where('rating', 4)->count(),
            3 => (clone $query)->where('rating', 3)->count(),
            2 => (clone $query)->where('rating', 2)->count(),
            1 => (clone $query)->where('rating', 1)->count(),
        ];

        $starPercentages = [];
        foreach ($starCounts as $star => $count) {
            $starPercentages[$star] = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
        }

        // Total de sugerencias con texto
        $totalComments = (clone $query)->whereNotNull('comment')->where('comment', '!=', '')->count();

        // Top médicos mejor valorados
        $topDoctors = Doctor::query()
            ->whereHas('reviews', fn($q) => $q->whereNotNull('submitted_at')->where('is_visible', true))
            ->with(['user', 'specialties'])
            ->withAvg(['reviews as avg_rating' => fn($q) => $q->whereNotNull('submitted_at')->where('is_visible', true)], 'rating')
            ->withCount(['reviews as total_reviews' => fn($q) => $q->whereNotNull('submitted_at')->where('is_visible', true)])
            ->orderByDesc('avg_rating')
            ->orderByDesc('total_reviews')
            ->take(5)
            ->get();

        return [
            'total_reviews' => $totalReviews,
            'average_rating' => $averageRating,
            'avg_attention' => $avgAttention,
            'avg_punctuality' => $avgPunctuality,
            'avg_facilities' => $avgFacilities,
            'star_counts' => $starCounts,
            'star_percentages' => $starPercentages,
            'total_comments' => $totalComments,
            'top_doctors' => $topDoctors,
        ];
    }
}
