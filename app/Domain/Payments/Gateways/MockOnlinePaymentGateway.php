<?php

namespace App\Domain\Payments\Gateways;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Payments\Contracts\PaymentGatewayInterface;
use App\Domain\Payments\DTOs\PaymentCreationDTO;
use App\Domain\Payments\DTOs\PaymentResultDTO;
use App\Domain\Payments\DTOs\PaymentStatusResultDTO;
use App\Domain\Payments\DTOs\RefundResultDTO;
use App\Domain\Payments\DTOs\WebhookResultDTO;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Models\Appointment;
use App\Models\AppointmentStatusHistory;
use App\Models\AuditLog;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MockOnlinePaymentGateway implements PaymentGatewayInterface
{
    public function createPayment(PaymentCreationDTO $dto): PaymentResultDTO
    {
        $gatewayTransactionId = 'mock_tx_' . Str::random(24);

        $payment = Payment::where('appointment_id', $dto->appointmentId)->first();
        if ($payment) {
            $payment->update([
                'gateway_name' => 'MockOnline',
                'gateway_transaction_id' => $gatewayTransactionId,
                'status' => PaymentStatus::PENDING,
            ]);
        }

        return new PaymentResultDTO(
            isSuccessful: true,
            gatewayTransactionId: $gatewayTransactionId,
            redirectUrl: route('patient.payment.success', ['code' => $gatewayTransactionId]),
            clientSecret: 'secret_' . Str::random(16),
            rawResponse: [
                'status' => 'initiated',
                'transaction_id' => $gatewayTransactionId,
            ]
        );
    }

    public function getStatus(string $gatewayTransactionId): PaymentStatusResultDTO
    {
        $payment = Payment::where('gateway_transaction_id', $gatewayTransactionId)->first();

        return new PaymentStatusResultDTO(
            gatewayTransactionId: $gatewayTransactionId,
            status: $payment ? $payment->status : PaymentStatus::PENDING,
            amount: $payment ? (float) $payment->amount : 0.0,
            currency: 'USD',
            paidAt: $payment?->paid_at?->toIso8601String()
        );
    }

    /**
     * Process webhook with strict idempotency protection.
     */
    public function handleWebhook(Request $request): WebhookResultDTO
    {
        $transactionId = $request->input('transaction_id');
        $eventType = $request->input('event_type', 'payment.succeeded');
        $statusStr = $request->input('status', 'PAID');

        $payment = Payment::where('gateway_transaction_id', $transactionId)->first();

        if (!$payment) {
            return new WebhookResultDTO(
                isValid: false,
                gatewayTransactionId: (string) $transactionId,
                eventType: $eventType,
                paymentStatus: PaymentStatus::REJECTED,
                rawPayload: $request->all()
            );
        }

        // Idempotency check: If payment is already PAID, do not re-process
        if ($payment->status === PaymentStatus::PAID) {
            return new WebhookResultDTO(
                isValid: true,
                gatewayTransactionId: $transactionId,
                eventType: $eventType,
                paymentStatus: PaymentStatus::PAID,
                appointmentId: $payment->appointment_id,
                amount: (float) $payment->amount,
                rawPayload: ['message' => 'Operation already processed previously (Idempotent bypass)']
            );
        }

        $newPaymentStatus = $statusStr === 'PAID' ? PaymentStatus::PAID : PaymentStatus::REJECTED;

        DB::transaction(function () use ($payment, $newPaymentStatus, $request) {
            $payment->update([
                'status' => $newPaymentStatus,
                'paid_at' => $newPaymentStatus === PaymentStatus::PAID ? now() : null,
                'payload_response' => $request->all(),
            ]);

            if ($newPaymentStatus === PaymentStatus::PAID && $payment->appointment) {
                $appointment = $payment->appointment;
                $appointment->update([
                    'status' => AppointmentStatus::CONFIRMED,
                ]);

                AppointmentStatusHistory::create([
                    'appointment_id' => $appointment->id,
                    'previous_status' => AppointmentStatus::PENDING_PAYMENT->value,
                    'new_status' => AppointmentStatus::CONFIRMED->value,
                    'reason' => 'Confirmación automática por pago online exitoso',
                    'metadata' => [
                        'transaction_id' => $payment->gateway_transaction_id,
                    ],
                    'created_at' => now(),
                ]);

                AuditLog::create([
                    'action' => 'PaymentValidated',
                    'auditable_type' => Payment::class,
                    'auditable_id' => (string) $payment->id,
                    'before_state' => ['status' => PaymentStatus::PENDING->value],
                    'after_state' => ['status' => PaymentStatus::PAID->value],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'created_at' => now(),
                ]);
            }
        });

        return new WebhookResultDTO(
            isValid: true,
            gatewayTransactionId: $transactionId,
            eventType: $eventType,
            paymentStatus: $newPaymentStatus,
            appointmentId: $payment->appointment_id,
            amount: (float) $payment->amount,
            rawPayload: $request->all()
        );
    }

    public function refund(string $gatewayTransactionId, float $amount): RefundResultDTO
    {
        $payment = Payment::where('gateway_transaction_id', $gatewayTransactionId)->first();
        if ($payment) {
            $payment->update(['status' => PaymentStatus::REFUNDED]);
        }

        return new RefundResultDTO(
            isSuccessful: true,
            refundTransactionId: 'ref_' . Str::random(20),
            amount: $amount
        );
    }
}
