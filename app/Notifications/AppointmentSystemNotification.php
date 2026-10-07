<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppointmentSystemNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public Appointment $appointment,
        public string $type = 'general',
        public ?string $actionUrl = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'appointment_id' => $this->appointment->id,
            'appointment_code' => $this->appointment->code,
            'patient_name' => $this->appointment->patient?->full_name ?? 'Paciente',
            'type' => $this->type,
            'action_url' => $this->actionUrl ?: url('/admin/citas?search=' . $this->appointment->code),
            'created_at' => now()->toIso8601String(),
        ];
    }
}
