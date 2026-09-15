<?php

namespace App\Domain\Payments\DTOs;

class PaymentResultDTO
{
    public function __construct(
        public bool $isSuccessful,
        public string $gatewayTransactionId,
        public ?string $redirectUrl = null,
        public ?string $clientSecret = null,
        public ?string $errorMessage = null,
        public array $rawResponse = []
    ) {}
}
