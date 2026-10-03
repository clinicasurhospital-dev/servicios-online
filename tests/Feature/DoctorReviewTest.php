<?php

namespace Tests\Feature;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Doctors\Services\DoctorReviewService;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorReview;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class DoctorReviewTest extends TestCase
{
    protected User $adminUser;
    protected User $doctorUser;
    protected Doctor $doctor;
    protected User $patientUser;
    protected Patient $patient;
    protected Specialty $specialty;
    protected Appointment $appointment;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Admin
        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'email' => 'admintest@surhospital.com',
            'password' => bcrypt('password'),
        ]);
        $this->adminUser->assignRole('ADMIN');

        // 2. Doctor
        $this->doctorUser = User::create([
            'name' => 'Dr. Carlos Mendoza',
            'email' => 'carlos.mendoza@surhospital.com',
            'password' => bcrypt('password'),
        ]);
        $this->doctorUser->assignRole('DOCTOR');

        $this->doctor = Doctor::create([
            'user_id' => $this->doctorUser->id,
            'license_number' => 'MSP-998877',
            'primary_specialty_name' => 'Cardiología',
            'default_consultation_price' => 45.00,
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $this->specialty = Specialty::create([
            'name' => 'Cardiología',
            'slug' => 'cardiologia',
            'base_price' => 45.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        // 3. Patient
        $this->patientUser = User::create([
            'name' => 'Ana Morales',
            'email' => 'ana.morales@example.com',
            'password' => bcrypt('password'),
        ]);
        $this->patientUser->assignRole('PATIENT');

        $this->patient = Patient::create([
            'user_id' => $this->patientUser->id,
            'identification_type' => 'CEDULA',
            'identification_number' => '0912345678',
            'first_name' => 'Ana',
            'last_name' => 'Morales',
            'email' => 'ana.morales@example.com',
            'phone' => '0997788990',
            'gender' => 'FEMALE',
            'birth_date' => '1990-05-15',
        ]);

        // 4. Appointment
        $this->appointment = Appointment::create([
            'code' => 'SUR-TEST-001',
            'patient_id' => $this->patient->id,
            'doctor_id' => $this->doctor->id,
            'specialty_id' => $this->specialty->id,
            'start_at' => Carbon::yesterday()->setHour(10),
            'end_at' => Carbon::yesterday()->setHour(10)->addMinutes(30),
            'status' => AppointmentStatus::COMPLETED,
            'price' => 45.00,
        ]);
    }

    public function test_can_generate_token_and_view_public_review_page(): void
    {
        $reviewService = app(DoctorReviewService::class);
        $review = $reviewService->getOrCreateReviewForAppointment($this->appointment, 'WHATSAPP');

        $this->assertNotNull($review->token);

        $response = $this->get(route('reviews.public.show', ['token' => $review->token]));
        $response->assertStatus(200);
        $response->assertSee('Dr. Carlos Mendoza');
        $response->assertSee('SUR-TEST-001');
    }

    public function test_can_submit_public_review_and_suggestions(): void
    {
        $reviewService = app(DoctorReviewService::class);
        $review = $reviewService->getOrCreateReviewForAppointment($this->appointment, 'WHATSAPP');

        $payload = [
            'rating' => 5,
            'attention_rating' => 5,
            'punctuality_rating' => 4,
            'facilities_rating' => 5,
            'recommendation_score' => 10,
            'comment' => 'Excelente atención del doctor, muy claro y profesional.',
            'is_anonymous' => 0,
        ];

        $response = $this->post(route('reviews.public.store', ['token' => $review->token]), $payload);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('doctor_reviews', [
            'appointment_id' => $this->appointment->id,
            'doctor_id' => $this->doctor->id,
            'rating' => 5,
            'attention_rating' => 5,
            'punctuality_rating' => 4,
            'comment' => 'Excelente atención del doctor, muy claro y profesional.',
        ]);
    }

    public function test_patient_can_submit_review_from_patient_portal(): void
    {
        $payload = [
            'rating' => 5,
            'attention_rating' => 5,
            'punctuality_rating' => 5,
            'facilities_rating' => 5,
            'comment' => 'Muy satisfecho con la consulta en SurHospital.',
            'is_anonymous' => 0,
        ];

        $response = $this->actingAs($this->patientUser)
            ->post(route('patient.appointment.review', $this->appointment), $payload);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('doctor_reviews', [
            'appointment_id' => $this->appointment->id,
            'channel' => 'PORTAL',
            'rating' => 5,
        ]);
    }

    public function test_admin_can_view_reviews_module_and_kpis(): void
    {
        // Pre-create submitted review
        DoctorReview::create([
            'appointment_id' => $this->appointment->id,
            'doctor_id' => $this->doctor->id,
            'patient_id' => $this->patient->id,
            'rating' => 5,
            'attention_rating' => 5,
            'punctuality_rating' => 5,
            'facilities_rating' => 5,
            'comment' => 'Atención impecable.',
            'channel' => 'WHATSAPP',
            'submitted_at' => now(),
            'is_visible' => true,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('admin.reviews'));
        $response->assertStatus(200);
        $response->assertSee('Sugerencias y Valoraciones del Médico');
        $response->assertSee('Atención impecable.');
        $response->assertSee('Dr. Carlos Mendoza');
    }

    public function test_whatsapp_share_url_generation(): void
    {
        $reviewService = app(DoctorReviewService::class);
        $whatsappUrl = $reviewService->getWhatsAppShareUrl($this->appointment);

        $this->assertStringContainsString('https://wa.me/593997788990', $whatsappUrl);
        $this->assertStringContainsString('Carlos%20Mendoza', $whatsappUrl);
    }
}
