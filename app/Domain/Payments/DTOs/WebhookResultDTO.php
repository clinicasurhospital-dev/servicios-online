<?php

namespace App\Domain\Payments\DTOs;

use App\Domain\Payments\Enums\PaymentStatus;

class WebhookResultDTO
{
    public function __construct(
        public bool $isValid,
        public string $gatewayTransactionId,
        public string $eventType,
        public PaymentStatus $paymentStatus,
        public ?int $appointmentId = null,
        public ?float $amount = null,
        public array $rawPayload = []
    ) {}
}
