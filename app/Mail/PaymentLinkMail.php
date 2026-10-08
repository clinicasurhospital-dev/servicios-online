<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public string $paymentLink,
        public float $amount,
        public ?string $patientName = null,
        public string $expiresIn = '2 horas',
        public string $paymentMethod = 'PAYMENT_LINK'
    ) {
        if (!$this->patientName && $this->appointment->patient) {
            $this->patientName = $this->appointment->patient->full_name;
        }
    }

    public function envelope(): Envelope
    {
        $brandName = config('branding.client_name', 'Clínica Sur Hospital');
        $code = $this->appointment->code;

        $subject = $this->paymentMethod === 'CREDIT_CARD' 
            ? "✅ Cita Generada con Éxito #{$code} — {$brandName}" 
            : "⏳ Cita Generada Pendiente de Confirmación #{$code} — {$brandName}";

        return new Envelope(
            from: new Address(
                config('mail.from.address', 'notificaciones@surhospital.com'),
                config('mail.from.name', 'SurHospital Citas')
            ),
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-link',
            with: [
                'appointment' => $this->appointment,
                'paymentLink' => $this->paymentLink,
                'amount' => $this->amount,
                'patientName' => $this->patientName,
                'expiresIn' => $this->expiresIn,
                'paymentMethod' => $this->paymentMethod,
                'brandName' => config('branding.client_name', 'Clínica Sur Hospital'),
                'brandPhone' => config('branding.contact.phone_display', '042 590670 Ext. 100 / 0'),
                'brandWhatsapp' => config('branding.contact.whatsapp_display', '+593 99 778 3576'),
                'brandAddress' => config('branding.contact.address', 'José Mascote 2123, Guayaquil, Ecuador'),
            ],
        );
    }
}
