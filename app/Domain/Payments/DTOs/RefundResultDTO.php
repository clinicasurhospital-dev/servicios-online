<?php

namespace App\Domain\Payments\DTOs;

class RefundResultDTO
{
    public function __construct(
        public bool $isSuccessful,
        public string $refundTransactionId,
        public float $amount,
        public ?string $errorMessage = null,
        public array $rawResponse = []
    ) {}
}
