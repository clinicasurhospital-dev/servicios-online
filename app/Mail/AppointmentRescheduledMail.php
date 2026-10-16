<?php

namespace App\Mail;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentRescheduledMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public Carbon $oldStartAt,
        public Carbon $newStartAt,
        public ?string $reason = null,
        public bool $isForStaff = false
    ) {
        $this->appointment->loadMissing(['patient', 'doctor.user', 'specialty', 'procedure']);
    }

    public function envelope(): Envelope
    {
        $brandName = config('branding.client_name', 'Clínica Sur Hospital');
        $code = $this->appointment->code;
        $patientName = $this->appointment->patient?->full_name ?? 'Paciente';

        $subject = $this->isForStaff
            ? "📅 Cita Reprogramada #{$code} — {$patientName} ({$brandName})"
            : "📅 Actualización de Turno: Tu Cita #{$code} ha sido Reprogramada — {$brandName}";

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
            view: 'emails.appointment-rescheduled',
            with: [
                'appointment' => $this->appointment,
                'oldStartAt' => $this->oldStartAt,
                'newStartAt' => $this->newStartAt,
                'reason' => $this->reason,
                'isForStaff' => $this->isForStaff,
                'brandName' => config('branding.client_name', 'Clínica Sur Hospital'),
                'brandPhone' => config('branding.contact.phone_display', '042 590670 Ext. 100 / 0'),
                'brandWhatsapp' => config('branding.contact.whatsapp_display', '+593 99 778 3576'),
                'brandAddress' => config('branding.contact.address', 'José Mascote 2123, Guayaquil, Ecuador'),
            ],
        );
    }
}
