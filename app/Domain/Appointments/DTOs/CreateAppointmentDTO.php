<?php

namespace App\Domain\Appointments\DTOs;

use Carbon\Carbon;

class CreateAppointmentDTO
{
    public function __construct(
        public int $patientId,
        public int $doctorId,
        public int $specialtyId,
        public ?int $procedureId,
        public Carbon $startAt,
        public Carbon $endAt,
        public float $price,
        public ?string $patientNotes = null,
        public ?int $createdByUserId = null,
        public string $paymentMethod = 'ONLINE', // ONLINE, BANK_TRANSFER
    ) {}
}
