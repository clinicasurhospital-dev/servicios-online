<?php

namespace App\Domain\Payments\DTOs;

class PaymentCreationDTO
{
    public function __construct(
        public int $appointmentId,
        public float $amount,
        public string $currency = 'USD',
        public string $patientEmail = '',
        public string $patientName = '',
        public ?string $description = null,
        public ?string $returnUrl = null,
        public ?string $cancelUrl = null,
        public array $metadata = []
    ) {}
}
