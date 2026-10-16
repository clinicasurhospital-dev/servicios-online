<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\DoctorReview;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DoctorReviewInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public string $reviewUrl,
        public ?string $patientName = null,
        public ?DoctorReview $review = null
    ) {
        if (!$this->patientName && $this->appointment->patient) {
            $this->patientName = $this->appointment->patient->full_name;
        }
    }

    public function envelope(): Envelope
    {
        $brandName = config('branding.client_name', 'Clínica Sur Hospital');
        $doctorName = $this->appointment->doctor?->user?->name ?? 'tu médico especialista';

        return new Envelope(
            from: new Address(
                config('mail.from.address', 'notificaciones@surhospital.com'),
                config('mail.from.name', 'SurHospital Citas')
            ),
            subject: "⭐ ¿Cómo fue tu consulta con el Dr(a). {$doctorName}? Cuéntanos tu experiencia — {$brandName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.doctor-review-invitation',
            with: [
                'appointment' => $this->appointment,
                'reviewUrl' => $this->reviewUrl,
                'patientName' => $this->patientName,
                'doctor' => $this->appointment->doctor,
                'brandName' => config('branding.client_name', 'Clínica Sur Hospital'),
                'brandPhone' => config('branding.contact.phone_display', '042 590670 Ext. 100 / 0'),
                'brandWhatsapp' => config('branding.contact.whatsapp_display', '+593 99 778 3576'),
                'brandAddress' => config('branding.contact.address', 'José Mascote 2123, Guayaquil, Ecuador'),
            ],
        );
    }
}
