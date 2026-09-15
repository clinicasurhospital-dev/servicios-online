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
use App\Models\DoctorScheduleBlock;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\SystemHoliday;
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
            'consultation_duration_minutes' => 30,
            'is_active' => true,
        ]);

        // Monday is day 1
        $testDate = Carbon::parse('next monday');

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '12:00',
            'is_active' => true,
        ]);

        $slots = $this->service->getAvailableSlotsForDate($doctor, $testDate);

        // 8:00 to 12:00 with 30m = 8 slots: 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30
        $this->assertCount(8, $slots);
        $this->assertEquals('08:00', $slots->first()->startTime);
        $this->assertEquals('11:30', $slots->last()->startTime);
        $this->assertTrue($slots->every(fn ($s) => $s->isAvailable));
    }

    public function test_existing_appointment_marks_slot_as_unavailable(): void
    {
        $user = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'license_number' => 'DOC-999',
            'consultation_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Cardiologia',
            'base_price' => 150000,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $pUser = User::factory()->create();
        $patient = Patient::create([
            'user_id' => $pUser->id,
            'first_name' => 'Juan',
            'last_name' => 'Perez',
            'identification_type' => IdentificationType::CC,
            'identification_number' => '998877',
            'phone' => '3001112233',
            'gender' => Gender::MALE,
        ]);

        $testDate = Carbon::parse('next monday');

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '10:00',
            'is_active' => true,
        ]);

        // Create appointment at 08:30
        Appointment::create([
            'code' => 'AP-TEST-001',
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
            'start_at' => $testDate->copy()->setTime(8, 30),
            'end_at' => $testDate->copy()->setTime(9, 0),
            'duration_minutes' => 30,
            'status' => AppointmentStatus::CONFIRMED,
            'total_amount' => 150000,
        ]);

        $slots = $this->service->getAvailableSlotsForDate($doctor, $testDate);

        $slot0800 = $slots->firstWhere('startTime', '08:00');
        $slot0830 = $slots->firstWhere('startTime', '08:30');
        $slot0900 = $slots->firstWhere('startTime', '09:00');

        $this->assertTrue($slot0800->isAvailable);
        $this->assertFalse($slot0830->isAvailable);
        $this->assertTrue($slot0900->isAvailable);
    }

    public function test_doctor_absence_blocks_all_slots_for_date(): void
    {
        $user = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'license_number' => 'DOC-999',
            'consultation_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $testDate = Carbon::parse('next monday');

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 1,
            'start_time' => '08:00',
            'end_time' => '12:00',
            'is_active' => true,
        ]);

        DoctorAbsence::create([
            'doctor_id' => $doctor->id,
            'type' => DoctorAbsenceType::VACATION,
            'start_date' => $testDate->copy()->subDay(),
            'end_date' => $testDate->copy()->addDay(),
            'reason' => 'Vacaciones anuales',
        ]);

        $slots = $this->service->getAvailableSlotsForDate($doctor, $testDate);

        $this->assertTrue($slots->every(fn ($s) => ! $s->isAvailable));
    }
}
