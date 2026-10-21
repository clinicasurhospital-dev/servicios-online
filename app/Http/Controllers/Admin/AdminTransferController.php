<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Appointments\Services\AppointmentNotificationDispatcher;
use App\Domain\Payments\Enums\BankTransferStatus;
use App\Domain\Payments\Enums\PaymentMethod;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Domain\Payments\Services\BankTransferService;
use App\Http\Controllers\Controller;
use App\Models\AppointmentStatusHistory;
use App\Models\AuditLog;
use App\Models\BankTransfer;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminTransferController extends Controller
{
    public function __construct(
        protected BankTransferService $transferService,
        protected ?AppointmentNotificationDispatcher $notificationDispatcher = null
    ) {
        $this->notificationDispatcher = $notificationDispatcher ?? app(AppointmentNotificationDispatcher::class);
    }

    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'transfers');
        $status = $request->query('status');
        $datalinkStatus = $request->query('datalink_status');

        $transfers = BankTransfer::query()
            ->with(['payment.appointment.patient.user', 'payment.appointment.doctor.user', 'payment.appointment.specialty', 'validator'])
            ->when($status, fn ($q) => $q->where('validation_status', $status))
            ->latest()
            ->paginate(15, ['*'], 'transfers_page')
            ->withQueryString();

        $datalinkPayments = Payment::query()
            ->where('payment_method', PaymentMethod::PAYMENT_LINK)
            ->with(['appointment.patient.user', 'appointment.doctor.user', 'appointment.specialty'])
            ->when($datalinkStatus, fn ($q) => $q->where('status', $datalinkStatus))
            ->latest()
            ->paginate(15, ['*'], 'datalink_page')
            ->withQueryString();

        $stats = [
            'pending' => BankTransfer::where('validation_status', BankTransferStatus::PENDING_VALIDATION)->count(),
            'approved' => BankTransfer::where('validation_status', BankTransferStatus::APPROVED)->count(),
            'rejected' => BankTransfer::where('validation_status', BankTransferStatus::REJECTED)->count(),
            'datalink_pending' => Payment::where('payment_method', PaymentMethod::PAYMENT_LINK)->where('status', PaymentStatus::PENDING)->count(),
            'datalink_paid' => Payment::where('payment_method', PaymentMethod::PAYMENT_LINK)->where('status', PaymentStatus::PAID)->count(),
            'datalink_total' => Payment::where('payment_method', PaymentMethod::PAYMENT_LINK)->count(),
        ];

        return view('admin.transfers', compact('transfers', 'datalinkPayments', 'stats', 'status', 'datalinkStatus', 'tab'));
    }

    public function validateTransfer(Request $request, BankTransfer $transfer): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:APPROVED,REJECTED',
            'notes' => 'nullable|string|max:500',
        ]);

        $isApproved = $request->string('status')->value() === 'APPROVED';

        try {
            $this->transferService->validateTransfer(
                transfer: $transfer,
                validator: $request->user(),
                isApproved: $isApproved,
                rejectionReason: ! $isApproved ? $request->input('notes') : null
            );

            return back()->with('success', 'Transferencia procesada correctamente.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Error al procesar la transferencia: ' . $e->getMessage());
        }
    }

    public function downloadReceipt(BankTransfer $transfer): StreamedResponse|RedirectResponse
    {
        if (! $transfer->receipt_path || ! Storage::disk('receipts')->exists($transfer->receipt_path)) {
            return back()->with('error', 'El comprobante no fue encontrado en el almacenamiento seguro.');
        }

        return Storage::disk('receipts')->download(
            $transfer->receipt_path,
            'comprobante_' . ($transfer->reference_number ?: $transfer->id) . '.' . pathinfo($transfer->receipt_path, PATHINFO_EXTENSION)
        );
    }

    public function sendDataLinkEmail(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate([
            'datalink_url' => 'required|url|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        try {
            $appointment = $payment->appointment;
            if (! $appointment) {
                return back()->with('error', 'La cita médica asociada no existe.');
            }

            $url = $request->input('datalink_url');
            $notes = $request->input('notes');

            $payload = is_array($payment->payload_response) ? $payment->payload_response : [];
            $payload['datalink_url'] = $url;
            $payload['datalink_sent_at'] = now()->toIso8601String();
            $payload['notes'] = $notes;

            $payment->update([
                'datalink_url' => $url,
                'datalink_sent_at' => now(),
                'payload_response' => $payload,
            ]);

            $this->notificationDispatcher->dispatchDataLinkSent(
                appointment: $appointment,
                payment: $payment,
                dataLinkUrl: $url,
                notes: $notes
            );

            AuditLog::create([
                'user_id' => $request->user()?->id,
                'action' => 'datalink_email_sent',
                'auditable_type' => Payment::class,
                'auditable_id' => (string) $payment->id,
                'after_state' => [
                    'appointment_id' => $appointment->id,
                    'appointment_code' => $appointment->code,
                    'datalink_url' => $url,
                    'patient_email' => $appointment->patient?->email ?: $appointment->patient?->user?->email,
                ],
                'ip_address' => $request->ip(),
                'created_at' => now(),
            ]);

            return back()->with('success', 'Enlace DataLink enviado al paciente por correo exitosamente.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Error al enviar enlace DataLink: ' . $e->getMessage());
        }
    }

    public function markDataLinkPaid(Request $request, Payment $payment): RedirectResponse
    {
        try {
            $appointment = $payment->appointment;

            DB::transaction(function () use ($payment, $appointment, $request) {
                $payment->update([
                    'status' => PaymentStatus::PAID,
                    'paid_at' => now(),
                ]);

                if ($appointment) {
                    $previousStatus = $appointment->status->value;
                    $appointment->update([
                        'status' => AppointmentStatus::CONFIRMED,
                    ]);

                    AppointmentStatusHistory::create([
                        'appointment_id' => $appointment->id,
                        'previous_status' => $previousStatus,
                        'new_status' => AppointmentStatus::CONFIRMED->value,
                        'changed_by_user_id' => $request->user()?->id,
                        'reason' => 'Pago por Enlace DataLink verificado y confirmado por personal de clínica',
                        'metadata' => [
                            'payment_id' => $payment->id,
                            'method' => 'PAYMENT_LINK',
                        ],
                        'created_at' => now(),
                    ]);

                    $this->notificationDispatcher->dispatchPaymentReceived(
                        appointment: $appointment,
                        payment: $payment,
                        note: 'Pago por Enlace DataLink confirmado exitosamente.'
                    );
                    $this->notificationDispatcher->dispatchAppointmentConfirmed($appointment);
                }

                AuditLog::create([
                    'user_id' => $request->user()?->id,
                    'action' => 'datalink_marked_paid',
                    'auditable_type' => Payment::class,
                    'auditable_id' => (string) $payment->id,
                    'after_state' => [
                        'appointment_id' => $appointment?->id,
                        'amount' => $payment->amount,
                    ],
                    'ip_address' => $request->ip(),
                    'created_at' => now(),
                ]);
            });

            return back()->with('success', 'El pago DataLink ha sido marcado como PAGADO y la cita médica ha sido confirmada.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Error al marcar pago como completado: ' . $e->getMessage());
        }
    }
}
