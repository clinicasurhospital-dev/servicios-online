<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $token
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $brandName = config('branding.client_name', 'Clínica Sur Hospital');
        $email = $notifiable->getEmailForPasswordReset();
        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $email,
        ]);
        $expiresIn = (int) config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject("🔐 Restablecer Contraseña — {$brandName}")
            ->view('emails.reset-password', [
                'resetUrl' => $resetUrl,
                'userName' => $notifiable->name ?? 'Usuario',
                'userEmail' => $email,
                'expiresIn' => $expiresIn,
                'brandName' => $brandName,
                'brandPhone' => config('branding.contact.phone_display', '042 590670 Ext. 100 / 0'),
                'brandWhatsapp' => config('branding.contact.whatsapp_display', '+593 99 778 3576'),
                'brandEmail' => config('branding.contact.email', 'info@surhospital.net'),
                'brandAddress' => config('branding.contact.address', 'José Mascote 2123, Guayaquil, Ecuador'),
            ]);
    }
}
