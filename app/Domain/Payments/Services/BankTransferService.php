<?php

namespace App\Domain\Payments\Services;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Payments\Enums\BankTransferStatus;
use App\Domain\Payments\Enums\PaymentMethod;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Models\Appointment;
use App\Models\AppointmentStatusHistory;
use App\Models\AuditLog;
use App\Models\BankTransfer;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Domain\Appointments\Services\AppointmentNotificationDispatcher;
use App\Notifications\AppointmentSystemNotification;
use Illuminate\Support\Facades\Notification;

class BankTransferService
{
    public function __construct(
        protected ?AppointmentNotificationDispatcher $notificationDispatcher = null
    ) {
        $this->notificationDispatcher = $notificationDispatcher ?? app(AppointmentNotificationDispatcher::class);
    }

    /**
     * Submit a new bank transfer record with receipt stored in private disk.
     */
    public function submitTransfer(
        Appointment $appointment,
        array $data,
        UploadedFile $receiptFile
    ): BankTransfer {
        $bankTransfer = DB::transaction(function () use ($appointment, $data, $receiptFile) {
            // Find or create payment for this appointment
            $payment = Payment::firstOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'payment_method' => PaymentMethod::BANK_TRANSFER,
                    'amount' => $appointment->price,
                    'currency' => 'USD',
                    'status' => PaymentStatus::PENDING,
                ]
            );

            // Store receipt securely in private disk
            $filename = 'receipt_' . $appointment->code . '_' . Str::random(12) . '.' . $receiptFile->getClientOriginalExtension();
            $path = $receiptFile->storeAs('receipts', $filename, 'receipts');

            $bankTransfer = BankTransfer::create([
                'payment_id' => $payment->id,
                'bank_name' => $data['bank_name'],
                'reference_number' => $data['reference_number'],
                'receipt_path' => $path,
                'receipt_original_name' => $receiptFile->getClientOriginalName(),
                'amount' => (float) ($data['amount'] ?? $appointment->price),
                'transfer_date' => $data['transfer_date'],
                'notes' => $data['notes'] ?? null,
                'validation_status' => BankTransferStatus::PENDING_VALIDATION,
            ]);

            return $bankTransfer;
        });

        // Notify staff of submitted voucher
        try {
            $staffUsers = $this->notificationDispatcher->getStaffUsers();
            if ($staffUsers->isNotEmpty()) {
                $patientName = $appointment->patient?->full_name ?? 'Paciente';
                Notification::send($staffUsers, new AppointmentSystemNotification(
                    title: '🏦 Comprobante de Pago Subido',
                    message: "{$patientName} subió un comprobante de transferencia bancaria para la cita #{$appointment->code} (Ref: {$bankTransfer->reference_number}).",
                    appointment: $appointment,
                    type: 'transfer_submitted',
                    actionUrl: url('/admin/transferencias')
                ));
            }
        } catch (\Throwable $e) {
            // Ignore failure in log
        }

        return $bankTransfer;
    }

    /**
     * Reception / Admin approves or rejects a bank transfer.
     */
    public function validateTransfer(
        BankTransfer $transfer,
        User $validator,
        bool $isApproved,
        ?string $rejectionReason = null
    ): BankTransfer {
        return DB::transaction(function () use ($transfer, $validator, $isApproved, $rejectionReason) {
            $newStatus = $isApproved ? BankTransferStatus::APPROVED : BankTransferStatus::REJECTED;
            $beforeState = $transfer->toArray();

            $transfer->update([
                'validation_status' => $newStatus,
                'validated_by_user_id' => $validator->id,
                'validated_at' => now(),
                'rejection_reason' => $isApproved ? null : $rejectionReason,
            ]);

            $payment = $transfer->payment;
            $appointment = $payment->appointment;

            if ($isApproved) {
                $payment->update([
                    'status' => PaymentStatus::PAID,
                    'paid_at' => now(),
                ]);

                $appointment->update([
                    'status' => AppointmentStatus::CONFIRMED,
                ]);

                AppointmentStatusHistory::create([
                    'appointment_id' => $appointment->id,
                    'previous_status' => $appointment->status->value,
                    'new_status' => AppointmentStatus::CONFIRMED->value,
                    'changed_by_user_id' => $validator->id,
                    'reason' => 'Transferencia bancaria aprobada por recepción',
                    'metadata' => [
                        'bank_name' => $transfer->bank_name,
                        'reference' => $transfer->reference_number,
                    ],
                    'created_at' => now(),
                ]);
            } else {
                $payment->update([
                    'status' => PaymentStatus::REJECTED,
                ]);

                AppointmentStatusHistory::create([
                    'appointment_id' => $appointment->id,
                    'previous_status' => $appointment->status->value,
                    'new_status' => $appointment->status->value,
                    'changed_by_user_id' => $validator->id,
                    'reason' => "Transferencia bancaria rechazada: {$rejectionReason}",
                    'metadata' => [
                        'bank_name' => $transfer->bank_name,
                        'reference' => $transfer->reference_number,
                    ],
                    'created_at' => now(),
                ]);
            }

            AuditLog::create([
                'user_id' => $validator->id,
                'action' => 'PaymentValidated',
                'auditable_type' => BankTransfer::class,
                'auditable_id' => (string) $transfer->id,
                'before_state' => $beforeState,
                'after_state' => $transfer->fresh()->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return $transfer;
        });

        if ($isApproved) {
            $appointment = $transfer->payment?->appointment;
            if ($appointment) {
                $this->notificationDispatcher->dispatchPaymentReceived(
                    $appointment,
                    $transfer->payment,
                    "Transferencia bancaria validada (Banco: {$transfer->bank_name}, Ref: {$transfer->reference_number})"
                );
            }
        }

        return $transfer;
    }
}
