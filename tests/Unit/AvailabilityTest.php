<?php

namespace Tests\Unit;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Availability\Services\AvailabilityService;
use App\Domain\Doctors\Enums\DoctorAbsenceType;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorAbsence;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class AvailabilityTest extends TestCase
{
    protected AvailabilityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AvailabilityService();
    }

    public function test_slots_generated_correctly_matching_schedule(): void
    {
        $user = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'license_number' => 'DOC-999',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        // Next Monday (Day 1)
        $testDate = Carbon::now()->next(Carbon::MONDAY);

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '12:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $slots = $this->service->getAvailableSlotsForDate($doctor, $testDate);

        // 8:00 to 12:00 with 30m = 8 slots: 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30
        $this->assertCount(8, $slots);
        $this->assertEquals('08:00', $slots->first()->timeFormatted());
        $this->assertEquals('11:30', $slots->last()->timeFormatted());
        $this->assertTrue($slots->every(fn ($s) => $s->isAvailable));
    }

    public function test_existing_appointment_marks_slot_as_unavailable(): void
    {
        $user = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'license_number' => 'DOC-999',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Cardiologia',
            'slug' => 'cardiologia',
            'base_price' => 150000,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Juan',
            'last_name' => 'Perez',
            'identification_type' => IdentificationType::CEDULA,
            'identification_number' => '0912345678',
            'phone' => '0991112233',
            'gender' => Gender::MALE,
        ]);

        $testDate = Carbon::now()->next(Carbon::MONDAY);

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '10:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        // Create appointment at 08:30
        $apptSlotStart = $testDate->copy()->setTime(8, 30);
        $apptSlotEnd = $testDate->copy()->setTime(9, 0);

        Appointment::create([
            'code' => 'AP-TEST-001',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
            'start_at' => $apptSlotStart,
            'end_at' => $apptSlotEnd,
            'status' => AppointmentStatus::CONFIRMED,
            'price' => 150000,
        ]);

        $slots = $this->service->getAvailableSlotsForDate($doctor, $testDate);

        // Should have 3 slots instead of 4: 08:00, 09:00, 09:30
        $this->assertCount(3, $slots);
        $this->assertTrue($slots->contains(fn($s) => $s->timeFormatted() === '08:00'));
        $this->assertFalse($slots->contains(fn($s) => $s->timeFormatted() === '08:30'));
        $this->assertTrue($slots->contains(fn($s) => $s->timeFormatted() === '09:00'));

        $this->assertFalse($this->service->isSlotAvailable($doctor->id, $apptSlotStart, $apptSlotEnd));
        $this->assertTrue($this->service->isSlotAvailable($doctor->id, $testDate->copy()->setTime(8, 0), $testDate->copy()->setTime(8, 30)));
    }

    public function test_doctor_absence_blocks_all_slots_for_date(): void
    {
        $user = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'license_number' => 'DOC-999',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $testDate = Carbon::now()->next(Carbon::MONDAY);

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '12:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        DoctorAbsence::create([
            'doctor_id' => $doctor->id,
            'type' => DoctorAbsenceType::VACATION,
            'start_date' => $testDate->copy()->subDay()->toDateString(),
            'end_date' => $testDate->copy()->addDay()->toDateString(),
            'notes' => 'Vacaciones anuales',
            'is_approved' => true,
        ]);

        $slots = $this->service->getAvailableSlotsForDate($doctor, $testDate);

        $this->assertCount(0, $slots);
        $this->assertTrue($this->service->isDoctorAbsent($doctor->id, $testDate));
    }
}
