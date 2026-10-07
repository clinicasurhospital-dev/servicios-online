<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public bool $isForStaff = false
    ) {
        $this->appointment->loadMissing(['patient', 'doctor.user', 'specialty', 'procedure', 'payments']);
    }

    public function envelope(): Envelope
    {
        $brandName = config('branding.client_name', 'Clínica Sur Hospital');
        $code = $this->appointment->code;
        $patientName = $this->appointment->patient?->full_name ?? 'Paciente';

        $subject = $this->isForStaff
            ? "🔔 Nueva Cita Agendada #{$code} — {$patientName} ({$brandName})"
            : "📅 Confirmación de Agendamiento de Cita Médica #{$code} — {$brandName}";

        return new Envelope(
            from: new Address(
                config('mail.from.address', 'notificaciones@surhospital.net'),
                config('mail.from.name', 'SurHospital Citas')
            ),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment-created',
            with: [
                'appointment' => $this->appointment,
                'isForStaff' => $this->isForStaff,
                'brandName' => config('branding.client_name', 'Clínica Sur Hospital'),
                'brandPhone' => config('branding.contact.phone_display', '042 590670 Ext. 100 / 0'),
                'brandWhatsapp' => config('branding.contact.whatsapp_display', '+593 99 778 3576'),
                'brandAddress' => config('branding.contact.address', 'José Mascote 2123, Guayaquil, Ecuador'),
            ],
        );
    }
}
