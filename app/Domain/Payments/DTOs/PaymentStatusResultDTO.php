<?php

namespace App\Domain\Payments\DTOs;

use App\Domain\Payments\Enums\PaymentStatus;

class PaymentStatusResultDTO
{
    public function __construct(
        public string $gatewayTransactionId,
        public PaymentStatus $status,
        public float $amount,
        public string $currency,
        public ?string $paidAt = null,
        public array $rawResponse = []
    ) {}
}
