<?php

namespace App\Domain\Availability\DTOs;

use Carbon\Carbon;

class AvailabilitySlotDTO
{
    public function __construct(
        public Carbon $startAt,
        public Carbon $endAt,
        public int $doctorId,
        public bool $isAvailable = true,
        public ?string $reasonUnavailable = null,
        public ?int $durationMinutes = 30,
    ) {}

    public function timeFormatted(): string
    {
        return $this->startAt->format('H:i');
    }

    public function endTimeFormatted(): string
    {
        return $this->endAt->format('H:i');
    }

    public function dateFormatted(): string
    {
        return $this->startAt->format('Y-m-d');
    }

    public function toArray(): array
    {
        return [
            'start_at' => $this->startAt->toIso8601String(),
            'end_at' => $this->endAt->toIso8601String(),
            'time' => $this->timeFormatted(),
            'end_time' => $this->endTimeFormatted(),
            'date' => $this->dateFormatted(),
            'doctor_id' => $this->doctorId,
            'is_available' => $this->isAvailable,
            'reason_unavailable' => $this->reasonUnavailable,
            'duration_minutes' => $this->durationMinutes,
        ];
    }
}
