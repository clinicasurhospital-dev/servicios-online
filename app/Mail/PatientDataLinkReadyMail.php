<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PatientDataLinkReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public string $dataLinkUrl,
        public float $amount,
        public ?string $patientName = null,
        public ?string $notes = null
    ) {
        $this->appointment->loadMissing(['patient', 'doctor.user', 'specialty']);
        if (!$this->patientName && $this->appointment->patient) {
            $this->patientName = $this->appointment->patient->full_name;
        }
    }

    public function envelope(): Envelope
    {
        $brandName = config('branding.client_name', 'Clínica Sur Hospital');
        $code = $this->appointment->code;

        return new Envelope(
            from: new Address(
                config('mail.from.address', 'notificaciones@surhospital.net'),
                config('mail.from.name', 'SurHospital Citas')
            ),
            subject: "💳 Enlace de Pago DataLink para tu Cita #{$code} — {$brandName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.patient-datalink-ready',
            with: [
                'appointment' => $this->appointment,
                'dataLinkUrl' => $this->dataLinkUrl,
                'amount' => $this->amount,
                'patientName' => $this->patientName,
                'notes' => $this->notes,
                'brandName' => config('branding.client_name', 'Clínica Sur Hospital'),
                'brandPhone' => config('branding.contact.phone_display', '042 590670 Ext. 100 / Ext. 0'),
                'brandWhatsapp' => config('branding.contact.whatsapp_display', '+593 99 778 3576'),
                'brandAddress' => config('branding.contact.address', 'José Mascote 2123, Guayaquil, Ecuador'),
            ]
        );
    }
}
