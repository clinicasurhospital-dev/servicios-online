<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffDataLinkAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public float $amount,
        public ?string $patientName = null,
        public ?string $patientPhone = null,
        public ?string $patientEmail = null,
    ) {
        $this->appointment->loadMissing(['patient', 'doctor.user', 'specialty']);
        if (!$this->patientName && $this->appointment->patient) {
            $this->patientName = $this->appointment->patient->full_name;
        }
        if (!$this->patientPhone && $this->appointment->patient) {
            $this->patientPhone = $this->appointment->patient->phone;
        }
        if (!$this->patientEmail && $this->appointment->patient) {
            $this->patientEmail = $this->appointment->patient->email;
        }
    }

    public function envelope(): Envelope
    {
        $brandName = config('branding.client_name', 'Clínica Sur Hospital');
        $code = $this->appointment->code;

        return new Envelope(
            from: new Address(
                config('mail.from.address', 'notificaciones@surhospital.com'),
                config('mail.from.name', 'SurHospital Notificaciones')
            ),
            subject: "🔔 ACCIÓN REQUERIDA: Generar Enlace DataLink — Cita #{$code} ({$this->patientName}) — {$brandName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.staff-datalink-alert',
            with: [
                'appointment' => $this->appointment,
                'amount' => $this->amount,
                'patientName' => $this->patientName,
                'patientPhone' => $this->patientPhone,
                'patientEmail' => $this->patientEmail,
                'brandName' => config('branding.client_name', 'Clínica Sur Hospital'),
                'adminUrl' => url('/admin/transferencias?tab=datalink'),
            ]
        );
    }
}
