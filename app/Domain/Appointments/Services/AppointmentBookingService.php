<?php

namespace App\Domain\Appointments\Services;

use App\Domain\Appointments\DTOs\CreateAppointmentDTO;
use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Appointments\Exceptions\SlotAlreadyBookedException;
use App\Domain\Availability\Services\AvailabilityService;
use App\Domain\Payments\Enums\PaymentMethod;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Models\Appointment;
use App\Models\AppointmentStatusHistory;
use App\Models\AuditLog;
use App\Models\Doctor;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AppointmentBookingService
{
    public function __construct(
        protected AvailabilityService $availabilityService
    ) {}

    /**
     * Atomically books an appointment with pessimistic concurrency protection.
     *
     * @throws SlotAlreadyBookedException
     */
    public function book(CreateAppointmentDTO $dto): Appointment
    {
        return DB::transaction(function () use ($dto) {
            // 1. Pessimistic lock on the Doctor record to serialize concurrent booking requests for this doctor
            $doctor = Doctor::where('id', $dto->doctorId)->lockForUpdate()->firstOrFail();

            // 2. Real-time availability check under transaction lock
            $isAvailable = $this->availabilityService->isSlotAvailable(
                doctorId: $dto->doctorId,
                startAt: $dto->startAt,
                endAt: $dto->endAt
            );

            if (!$isAvailable) {
                throw new SlotAlreadyBookedException();
            }

            // 3. Generate unique business appointment code
            $prefix = config('branding.display.appointment_code_prefix', 'SUR');
            $year = $dto->startAt->format('Y');
            $randomNum = str_pad((string) random_int(1000, 99999), 5, '0', STR_PAD_LEFT);
            $code = "{$prefix}-{$year}-{$randomNum}";

            // Ensure uniqueness
            while (Appointment::where('code', $code)->exists()) {
                $randomNum = str_pad((string) random_int(1000, 99999), 5, '0', STR_PAD_LEFT);
                $code = "{$prefix}-{$year}-{$randomNum}";
            }

            $initialStatus = match ($dto->paymentMethod) {
                'ONLINE', 'BANK_TRANSFER' => AppointmentStatus::PENDING_PAYMENT,
                default => AppointmentStatus::PENDING,
            };

            // 4. Create appointment
            $appointment = Appointment::create([
                'code' => $code,
                'patient_id' => $dto->patientId,
                'doctor_id' => $dto->doctorId,
                'specialty_id' => $dto->specialtyId,
                'procedure_id' => $dto->procedureId,
                'start_at' => $dto->startAt,
                'end_at' => $dto->endAt,
                'status' => $initialStatus,
                'price' => $dto->price,
                'patient_notes' => $dto->patientNotes,
                'created_by' => $dto->createdByUserId,
            ]);

            // 5. Create initial payment record
            $paymentMethodEnum = match ($dto->paymentMethod) {
                'BANK_TRANSFER' => PaymentMethod::BANK_TRANSFER,
                'CASH' => PaymentMethod::CASH,
                'CARD_ONSITE' => PaymentMethod::CARD_ONSITE,
                default => PaymentMethod::ONLINE,
            };

            Payment::create([
                'appointment_id' => $appointment->id,
                'payment_method' => $paymentMethodEnum,
                'amount' => $dto->price,
                'currency' => 'USD',
                'status' => PaymentStatus::PENDING,
            ]);

            // 6. Record immutable status history
            AppointmentStatusHistory::create([
                'appointment_id' => $appointment->id,
                'previous_status' => null,
                'new_status' => $initialStatus->value,
                'changed_by_user_id' => $dto->createdByUserId,
                'reason' => 'Creación y reserva inicial de cita',
                'metadata' => [
                    'source' => $dto->createdByUserId ? 'reception_or_portal' : 'public_wizard',
                    'payment_method' => $dto->paymentMethod,
                ],
                'created_at' => now(),
            ]);

            // 7. Audit log
            AuditLog::create([
                'user_id' => $dto->createdByUserId,
                'action' => 'AppointmentCreated',
                'auditable_type' => Appointment::class,
                'auditable_id' => (string) $appointment->id,
                'before_state' => null,
                'after_state' => $appointment->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);

            return $appointment;
        });
    }
}
