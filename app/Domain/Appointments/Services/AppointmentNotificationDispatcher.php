<?php

namespace App\Domain\Appointments\Services;

use App\Mail\AppointmentCancelledMail;
use App\Mail\AppointmentCreatedMail;
use App\Mail\AppointmentRescheduledMail;
use App\Mail\PaymentReceivedMail;
use App\Mail\PatientDataLinkPendingMail;
use App\Mail\PatientDataLinkReadyMail;
use App\Mail\StaffDataLinkAlertMail;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\AppointmentSystemNotification;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class AppointmentNotificationDispatcher
{
    /**
     * Get unique configurable emails for administrators and receptionists.
     */
    public function getStaffEmails(): array
    {
        $adminEmails = config('branding.notifications.admin_emails', []);
        $receptionEmails = config('branding.notifications.reception_emails', []);

        // Also fetch email of active users with ADMIN or RECEPTION roles
        $dbStaffEmails = User::where('is_active', true)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['SUPER_ADMIN', 'ADMIN', 'RECEPTION']);
            })
            ->pluck('email')
            ->toArray();

        $all = array_merge((array) $adminEmails, (array) $receptionEmails, $dbStaffEmails);

        $cleaned = array_values(array_unique(array_filter(array_map('strtolower', array_map('trim', $all)))));
        return array_values(array_filter($cleaned, fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL)));
    }

    /**
     * Get collection of internal staff users (Admin & Reception).
     */
    public function getStaffUsers(): Collection
    {
        return User::where('is_active', true)
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['SUPER_ADMIN', 'ADMIN', 'RECEPTION']);
            })
            ->get();
    }

    /**
     * Executes notification sending in background/deferred manner if supported,
     * ensuring web requests complete immediately without waiting for SMTP handshakes.
     */
    protected function executeDeferred(callable $callback): void
    {
        if (function_exists('defer')) {
            defer($callback);
        } else {
            $callback();
        }
    }

    /**
     * Send email to staff list efficiently using a single connection with BCC.
     */
    protected function sendStaffMail(\Illuminate\Mail\Mailable $mailable, array $excludeEmails = []): void
    {
        $staffEmails = $this->getStaffEmails();
        $excludeNormalized = array_map('strtolower', array_map('trim', $excludeEmails));

        $validStaff = array_values(array_filter($staffEmails, function ($email) use ($excludeNormalized) {
            $normalized = strtolower(trim($email));
            return !empty($normalized) 
                && filter_var($normalized, FILTER_VALIDATE_EMAIL)
                && !in_array($normalized, $excludeNormalized, true);
        }));

        if (empty($validStaff)) {
            return;
        }

        try {
            $primary = array_shift($validStaff);
            $mail = Mail::to($primary);
            if (!empty($validStaff)) {
                $mail->bcc($validStaff);
            }
            $mail->send($mailable);
        } catch (\Throwable $e) {
            Log::error('Error enviando notificación por correo al staff: ' . $e->getMessage(), [
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Dispatch notifications when a new appointment is created.
     */
    public function dispatchAppointmentCreated(Appointment $appointment): void
    {
        $appointment->loadMissing(['patient', 'doctor.user', 'specialty', 'procedure']);
        $patientEmail = $appointment->patient?->email;
        $staffUsers = $this->getStaffUsers();

        $this->executeDeferred(function () use ($appointment, $patientEmail, $staffUsers) {
            // 1. Send email to patient
            if (!empty($patientEmail) && filter_var($patientEmail, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($patientEmail)->send(new AppointmentCreatedMail($appointment, isForStaff: false));
                } catch (\Throwable $e) {
                    Log::error('Error enviando correo de cita creada al paciente: ' . $e->getMessage(), [
                        'appointment_id' => $appointment->id,
                        'email' => $patientEmail,
                    ]);
                }
            }

            // 2. Send single batched email to administration & reception
            $this->sendStaffMail(
                new AppointmentCreatedMail($appointment, isForStaff: true),
                excludeEmails: $patientEmail ? [$patientEmail] : []
            );

            // 3. Internal system notification for staff
            if ($staffUsers->isNotEmpty()) {
                try {
                    $patientName = $appointment->patient?->full_name ?? 'Paciente';
                    Notification::send($staffUsers, new AppointmentSystemNotification(
                        title: '🏥 Nueva Cita Agendada',
                        message: "Nueva cita médica registrada (#{$appointment->code}) para {$patientName} con {$appointment->doctor?->user?->name}.",
                        appointment: $appointment,
                        type: 'created'
                    ));
                } catch (\Throwable $e) {
                    Log::error('Error registrando notificación interna de cita creada: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Dispatch notification when appointment is confirmed by admin.
     */
    public function dispatchAppointmentConfirmed(Appointment $appointment): void
    {
        $appointment->loadMissing(['patient.user', 'doctor.user', 'specialty', 'procedure']);
        $patientEmail = $appointment->patient?->email ?: $appointment->patient?->user?->email;

        $this->executeDeferred(function () use ($appointment, $patientEmail) {
            if (!empty($patientEmail) && filter_var($patientEmail, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($patientEmail)->send(new \App\Mail\AppointmentConfirmedMail($appointment));
                } catch (\Throwable $e) {
                    Log::error('Error enviando correo de confirmación de cita al paciente: ' . $e->getMessage(), [
                        'appointment_id' => $appointment->id,
                        'email' => $patientEmail,
                    ]);
                }
            }
        });
    }

    /**
     * Dispatch notifications when a payment is received (card, link, transfer).
     */
    public function dispatchPaymentReceived(Appointment $appointment, Payment $payment, ?string $note = null): void
    {
        $appointment->loadMissing(['patient.user', 'doctor.user', 'specialty', 'procedure']);
        $patientEmail = $appointment->patient?->email ?: $appointment->patient?->user?->email;
        $staffUsers = $this->getStaffUsers();
        $amountFormatted = number_format((float) $payment->amount, 2);

        $this->executeDeferred(function () use ($appointment, $payment, $patientEmail, $staffUsers, $note, $amountFormatted) {
            // 1. Send receipt email to patient
            if (!empty($patientEmail) && filter_var($patientEmail, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($patientEmail)->send(new PaymentReceivedMail($appointment, $payment, isForStaff: false, note: $note));
                } catch (\Throwable $e) {
                    Log::error('Error enviando comprobante de pago al paciente: ' . $e->getMessage());
                }
            }

            // 2. Send payment notification email to admin & reception in single batch
            $this->sendStaffMail(
                new PaymentReceivedMail($appointment, $payment, isForStaff: true, note: $note)
            );

            // 3. Internal system notification for staff
            if ($staffUsers->isNotEmpty()) {
                try {
                    $patientName = $appointment->patient?->full_name ?? 'Paciente';
                    Notification::send($staffUsers, new AppointmentSystemNotification(
                        title: '💳 Pago Recibido',
                        message: "Se registró el pago de \${$amountFormatted} USD para la cita #{$appointment->code} ({$patientName}).",
                        appointment: $appointment,
                        type: 'payment',
                        actionUrl: url('/admin/citas?search=' . $appointment->code)
                    ));
                } catch (\Throwable $e) {
                    Log::error('Error registrando notificación interna de pago: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Dispatch notifications when an appointment is rescheduled.
     */
    public function dispatchAppointmentRescheduled(
        Appointment $appointment,
        Carbon $oldStartAt,
        Carbon $newStartAt,
        ?string $reason = null
    ): void {
        $appointment->loadMissing(['patient', 'doctor.user', 'specialty', 'procedure']);
        $patientEmail = $appointment->patient?->email;
        $doctorUser = $appointment->doctor?->user;
        $staffUsers = $this->getStaffUsers();

        $this->executeDeferred(function () use ($appointment, $oldStartAt, $newStartAt, $reason, $patientEmail, $doctorUser, $staffUsers) {
            // 1. Send email to patient
            if (!empty($patientEmail) && filter_var($patientEmail, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($patientEmail)->send(new AppointmentRescheduledMail(
                        $appointment,
                        $oldStartAt,
                        $newStartAt,
                        $reason,
                        isForStaff: false
                    ));
                } catch (\Throwable $e) {
                    Log::error('Error enviando correo de reprogramación al paciente: ' . $e->getMessage());
                }
            }

            // 2. Send email to doctor
            if ($doctorUser && !empty($doctorUser->email) && filter_var($doctorUser->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($doctorUser->email)->send(new AppointmentRescheduledMail(
                        $appointment,
                        $oldStartAt,
                        $newStartAt,
                        $reason,
                        isForStaff: true
                    ));
                } catch (\Throwable $e) {
                    Log::error('Error enviando correo de reprogramación al médico: ' . $e->getMessage());
                }
            }

            // 3. Send email to admin and reception in batch
            $excludes = array_filter([$patientEmail, $doctorUser?->email]);
            $this->sendStaffMail(
                new AppointmentRescheduledMail(
                    $appointment,
                    $oldStartAt,
                    $newStartAt,
                    $reason,
                    isForStaff: true
                ),
                excludeEmails: $excludes
            );

            // 4. Internal database notification
            $recipients = $staffUsers;
            if ($doctorUser && !$recipients->contains('id', $doctorUser->id)) {
                $recipients = $recipients->concat([$doctorUser]);
            }

            if ($recipients->isNotEmpty()) {
                try {
                    $patientName = $appointment->patient?->full_name ?? 'Paciente';
                    Notification::send($recipients, new AppointmentSystemNotification(
                        title: '📅 Cita Reprogramada',
                        message: "La cita #{$appointment->code} de {$patientName} fue reprogramada para el {$newStartAt->translatedFormat('d M Y - h:i A')}.",
                        appointment: $appointment,
                        type: 'reschedule',
                        actionUrl: url('/admin/agenda?date=' . $newStartAt->format('Y-m-d'))
                    ));
                } catch (\Throwable $e) {
                    Log::error('Error registrando notificación interna de reprogramación: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Dispatch notifications when an appointment is cancelled.
     */
    public function dispatchAppointmentCancelled(
        Appointment $appointment,
        string $reason,
        string $cancelledByRole = 'PACIENTE'
    ): void {
        $appointment->loadMissing(['patient', 'doctor.user', 'specialty', 'procedure']);
        $patientEmail = $appointment->patient?->email;
        $doctorUser = $appointment->doctor?->user;
        $staffUsers = $this->getStaffUsers();

        $this->executeDeferred(function () use ($appointment, $reason, $cancelledByRole, $patientEmail, $doctorUser, $staffUsers) {
            // 1. Send email to patient
            if (!empty($patientEmail) && filter_var($patientEmail, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($patientEmail)->send(new AppointmentCancelledMail(
                        $appointment,
                        $reason,
                        $cancelledByRole,
                        isForStaff: false
                    ));
                } catch (\Throwable $e) {
                    Log::error('Error enviando correo de cancelación al paciente: ' . $e->getMessage());
                }
            }

            // 2. Send email to doctor
            if ($doctorUser && !empty($doctorUser->email) && filter_var($doctorUser->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($doctorUser->email)->send(new AppointmentCancelledMail(
                        $appointment,
                        $reason,
                        $cancelledByRole,
                        isForStaff: true
                    ));
                } catch (\Throwable $e) {
                    Log::error('Error enviando correo de cancelación al médico: ' . $e->getMessage());
                }
            }

            // 3. Send email to admin & reception in batch
            $excludes = array_filter([$patientEmail, $doctorUser?->email]);
            $this->sendStaffMail(
                new AppointmentCancelledMail(
                    $appointment,
                    $reason,
                    $cancelledByRole,
                    isForStaff: true
                ),
                excludeEmails: $excludes
            );

            // 4. Internal database notification
            $recipients = $staffUsers;
            if ($doctorUser && !$recipients->contains('id', $doctorUser->id)) {
                $recipients = $recipients->concat([$doctorUser]);
            }

            if ($recipients->isNotEmpty()) {
                try {
                    $patientName = $appointment->patient?->full_name ?? 'Paciente';
                    Notification::send($recipients, new AppointmentSystemNotification(
                        title: '❌ Cita Cancelada',
                        message: "La cita #{$appointment->code} de {$patientName} fue cancelada ({$cancelledByRole}). Motivo: {$reason}.",
                        appointment: $appointment,
                        type: 'cancel',
                        actionUrl: url('/admin/citas?search=' . $appointment->code)
                    ));
                } catch (\Throwable $e) {
                    Log::error('Error registrando notificación interna de cancelación: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Dispatch notifications when patient requests manual DataLink payment.
     */
    public function dispatchDataLinkRequested(Appointment $appointment, Payment $payment): void
    {
        $appointment->loadMissing(['patient.user', 'doctor.user', 'specialty']);
        $patientEmail = $appointment->patient?->email ?: $appointment->patient?->user?->email;
        $staffUsers = $this->getStaffUsers();

        $this->executeDeferred(function () use ($appointment, $payment, $patientEmail, $staffUsers) {
            // 1. Send confirmation to patient that appointment is reserved and link is being prepared
            if (!empty($patientEmail) && filter_var($patientEmail, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($patientEmail)->send(new PatientDataLinkPendingMail(
                        appointment: $appointment,
                        amount: (float) $payment->amount,
                        patientName: $appointment->patient?->full_name
                    ));
                } catch (\Throwable $e) {
                    Log::error('Error enviando correo de DataLink pendiente al paciente: ' . $e->getMessage());
                }
            }

            // 2. Send urgent alert to reception and administration in a single batched connection
            $this->sendStaffMail(
                new StaffDataLinkAlertMail(
                    appointment: $appointment,
                    amount: (float) $payment->amount,
                    patientName: $appointment->patient?->full_name,
                    patientPhone: $appointment->patient?->phone,
                    patientEmail: $patientEmail
                ),
                excludeEmails: $patientEmail ? [$patientEmail] : []
            );

            // 3. In-app system notification for reception/admin
            if ($staffUsers->isNotEmpty()) {
                try {
                    $patientName = $appointment->patient?->full_name ?? 'Paciente';
                    Notification::send($staffUsers, new AppointmentSystemNotification(
                        title: '🔗 Emitir Enlace DataLink',
                        message: "Cita #{$appointment->code} ({$patientName}) requiere emisión manual de Enlace de Pago DataLink por $" . number_format((float) $payment->amount, 2) . ".",
                        appointment: $appointment,
                        type: 'datalink_required',
                        actionUrl: url('/admin/transferencias?tab=datalink')
                    ));
                } catch (\Throwable $e) {
                    Log::error('Error registrando notificación interna de DataLink: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Dispatch notification when reception emits and sends DataLink payment link to the patient.
     */
    public function dispatchDataLinkSent(Appointment $appointment, Payment $payment, string $dataLinkUrl, ?string $notes = null): void
    {
        $appointment->loadMissing(['patient.user', 'doctor.user', 'specialty']);
        $patientEmail = $appointment->patient?->email ?: $appointment->patient?->user?->email;

        $this->executeDeferred(function () use ($appointment, $patientEmail, $dataLinkUrl, $payment, $notes) {
            if (!empty($patientEmail) && filter_var($patientEmail, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($patientEmail)->send(new PatientDataLinkReadyMail(
                        appointment: $appointment,
                        dataLinkUrl: $dataLinkUrl,
                        amount: (float) $payment->amount,
                        patientName: $appointment->patient?->full_name,
                        notes: $notes
                    ));
                } catch (\Throwable $e) {
                    Log::error('Error enviando correo con enlace DataLink al paciente: ' . $e->getMessage());
                }
            }
        });
    }
}
