<?php

namespace App\Domain\Payments\Contracts;

use App\Domain\Payments\DTOs\PaymentCreationDTO;
use App\Domain\Payments\DTOs\PaymentResultDTO;
use App\Domain\Payments\DTOs\PaymentStatusResultDTO;
use App\Domain\Payments\DTOs\RefundResultDTO;
use App\Domain\Payments\DTOs\WebhookResultDTO;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Initialize a payment intent/charge in the gateway.
     */
    public function createPayment(PaymentCreationDTO $dto): PaymentResultDTO;

    /**
     * Query the payment status from the gateway.
     */
    public function getStatus(string $gatewayTransactionId): PaymentStatusResultDTO;

    /**
     * Handle incoming asynchronous webhook from the payment provider (Idempotent).
     */
    public function handleWebhook(Request $request): WebhookResultDTO;

    /**
     * Refund a paid transaction.
     */
    public function refund(string $gatewayTransactionId, float $amount): RefundResultDTO;
}
