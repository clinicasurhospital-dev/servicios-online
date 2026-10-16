<?php

namespace App\Mail;

use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public Payment $payment,
        public bool $isForStaff = true,
        public ?string $note = null
    ) {
        $this->appointment->loadMissing(['patient', 'doctor.user', 'specialty', 'procedure', 'payments']);
    }

    public function envelope(): Envelope
    {
        $brandName = config('branding.client_name', 'Clínica Sur Hospital');
        $code = $this->appointment->code;
        $patientName = $this->appointment->patient?->full_name ?? 'Paciente';
        $amount = number_format((float) $this->payment->amount, 2);

        $subject = $this->isForStaff
            ? "💳 Notificación de Pago Recibido: \${$amount} USD — Cita #{$code} ({$patientName})"
            : "✅ Comprobante de Pago Confirmado — Cita Médica #{$code} — {$brandName}";

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
            view: 'emails.payment-received',
            with: [
                'appointment' => $this->appointment,
                'payment' => $this->payment,
                'isForStaff' => $this->isForStaff,
                'note' => $this->note,
                'brandName' => config('branding.client_name', 'Clínica Sur Hospital'),
                'brandPhone' => config('branding.contact.phone_display', '042 590670 Ext. 100 / 0'),
                'brandWhatsapp' => config('branding.contact.whatsapp_display', '+593 99 778 3576'),
                'brandAddress' => config('branding.contact.address', 'José Mascote 2123, Guayaquil, Ecuador'),
            ],
        );
    }
}
