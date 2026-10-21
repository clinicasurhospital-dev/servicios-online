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

        return array_values(array_unique(array_filter(array_map('strtolower', array_map('trim', $all)))));
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
     * Dispatch notifications when a new appointment is created.
     */
    public function dispatchAppointmentCreated(Appointment $appointment): void
    {
        $appointment->loadMissing(['patient', 'doctor.user', 'specialty', 'procedure']);
        $patientEmail = $appointment->patient?->email;
        $staffEmails = $this->getStaffEmails();
        $staffUsers = $this->getStaffUsers();

        // 1. Send email to patient
        if (!empty($patientEmail)) {
            try {
                Mail::to($patientEmail)->send(new AppointmentCreatedMail($appointment, isForStaff: false));
            } catch (\Throwable $e) {
                Log::error('Error enviando correo de cita creada al paciente: ' . $e->getMessage(), [
                    'appointment_id' => $appointment->id,
                    'email' => $patientEmail,
                ]);
            }
        }

        // 2. Send email to administration & reception
        foreach ($staffEmails as $staffEmail) {
            if ($staffEmail === $patientEmail) continue;
            try {
                Mail::to($staffEmail)->send(new AppointmentCreatedMail($appointment, isForStaff: true));
            } catch (\Throwable $e) {
                Log::error('Error enviando correo de cita creada al staff: ' . $e->getMessage(), [
                    'appointment_id' => $appointment->id,
                    'email' => $staffEmail,
                ]);
            }
        }

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
    }

    /**
     * Dispatch notification when appointment is confirmed by admin.
     */
    public function dispatchAppointmentConfirmed(Appointment $appointment): void
    {
        $appointment->loadMissing(['patient.user', 'doctor.user', 'specialty', 'procedure']);
        $patientEmail = $appointment->patient?->email ?: $appointment->patient?->user?->email;

        if (!empty($patientEmail)) {
            try {
                Mail::to($patientEmail)->send(new \App\Mail\AppointmentConfirmedMail($appointment));
            } catch (\Throwable $e) {
                Log::error('Error enviando correo de confirmación de cita al paciente: ' . $e->getMessage(), [
                    'appointment_id' => $appointment->id,
                    'email' => $patientEmail,
                ]);
            }
        }
    }

    /**
     * Dispatch notifications when a payment is received (card, link, transfer).
     */
    public function dispatchPaymentReceived(Appointment $appointment, Payment $payment, ?string $note = null): void
    {
        $appointment->loadMissing(['patient.user', 'doctor.user', 'specialty', 'procedure']);
        $patientEmail = $appointment->patient?->email ?: $appointment->patient?->user?->email;
        $staffEmails = $this->getStaffEmails();
        $staffUsers = $this->getStaffUsers();
        $amountFormatted = number_format((float) $payment->amount, 2);

        // 1. Send receipt email to patient
        if (!empty($patientEmail)) {
            try {
                Mail::to($patientEmail)->send(new PaymentReceivedMail($appointment, $payment, isForStaff: false, note: $note));
            } catch (\Throwable $e) {
                Log::error('Error enviando comprobante de pago al paciente: ' . $e->getMessage());
            }
        }

        // 2. Send payment notification email to admin & reception
        foreach ($staffEmails as $staffEmail) {
            try {
                Mail::to($staffEmail)->send(new PaymentReceivedMail($appointment, $payment, isForStaff: true, note: $note));
            } catch (\Throwable $e) {
                Log::error('Error enviando aviso de pago a administración/recepción: ' . $e->getMessage(), [
                    'email' => $staffEmail,
                ]);
            }
        }

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
        $staffEmails = $this->getStaffEmails();
        $staffUsers = $this->getStaffUsers();

        // 1. Send email to patient
        if (!empty($patientEmail)) {
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
        if ($doctorUser && !empty($doctorUser->email) && !in_array($doctorUser->email, $staffEmails)) {
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

        // 3. Send email to admin and reception
        foreach ($staffEmails as $staffEmail) {
            if ($staffEmail === $patientEmail) continue;
            try {
                Mail::to($staffEmail)->send(new AppointmentRescheduledMail(
                    $appointment,
                    $oldStartAt,
                    $newStartAt,
                    $reason,
                    isForStaff: true
                ));
            } catch (\Throwable $e) {
                Log::error('Error enviando correo de reprogramación al staff: ' . $e->getMessage());
            }
        }

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
        $staffEmails = $this->getStaffEmails();
        $staffUsers = $this->getStaffUsers();

        // 1. Send email to patient
        if (!empty($patientEmail)) {
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
        if ($doctorUser && !empty($doctorUser->email) && !in_array($doctorUser->email, $staffEmails)) {
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

        // 3. Send email to admin & reception
        foreach ($staffEmails as $staffEmail) {
            if ($staffEmail === $patientEmail) continue;
            try {
                Mail::to($staffEmail)->send(new AppointmentCancelledMail(
                    $appointment,
                    $reason,
                    $cancelledByRole,
                    isForStaff: true
                ));
            } catch (\Throwable $e) {
                Log::error('Error enviando correo de cancelación al staff: ' . $e->getMessage());
            }
        }

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
    }

    /**
     * Dispatch notifications when patient requests manual DataLink payment.
     */
    public function dispatchDataLinkRequested(Appointment $appointment, Payment $payment): void
    {
        $appointment->loadMissing(['patient.user', 'doctor.user', 'specialty']);
        $patientEmail = $appointment->patient?->email ?: $appointment->patient?->user?->email;
        $staffEmails = $this->getStaffEmails();
        $staffUsers = $this->getStaffUsers();

        // 1. Send confirmation to patient that appointment is reserved and link is being prepared
        if (!empty($patientEmail)) {
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

        // 2. Send urgent alert to reception and administration
        foreach ($staffEmails as $staffEmail) {
            if ($staffEmail === $patientEmail) continue;
            try {
                Mail::to($staffEmail)->send(new StaffDataLinkAlertMail(
                    appointment: $appointment,
                    amount: (float) $payment->amount,
                    patientName: $appointment->patient?->full_name,
                    patientPhone: $appointment->patient?->phone,
                    patientEmail: $patientEmail
                ));
            } catch (\Throwable $e) {
                Log::error('Error enviando alerta de DataLink al staff: ' . $e->getMessage());
            }
        }

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
    }

    /**
     * Dispatch notification when reception emits and sends DataLink payment link to the patient.
     */
    public function dispatchDataLinkSent(Appointment $appointment, Payment $payment, string $dataLinkUrl, ?string $notes = null): void
    {
        $appointment->loadMissing(['patient.user', 'doctor.user', 'specialty']);
        $patientEmail = $appointment->patient?->email ?: $appointment->patient?->user?->email;

        if (!empty($patientEmail)) {
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
    }
}
