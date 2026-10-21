<?php

namespace Tests\Feature;

use App\Livewire\BookingWizard;
use App\Models\Appointment;
use App\Models\BankTransfer;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BookingWizardTest extends TestCase
{
    // TestCase already includes RefreshDatabase and seeds roles in setUp()

    public function test_booking_wizard_renders_and_completes_5_step_flow_with_credit_card(): void
    {
        $user = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'license_number' => 'DOC-TEST-WIZ-1',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Cardiología Especial',
            'slug' => 'cardiologia-especial',
            'base_price' => 75.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $doctor->specialties()->attach($specialty->id);

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => now()->addDays(2)->dayOfWeek,
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $date = now()->addDays(2)->format('Y-m-d');

        $test = Livewire::test(BookingWizard::class)
            ->assertSet('step', 1)
            ->call('selectSpecialty', $specialty->id)
            ->call('selectProcedure', 0)
            ->call('proceedToStep2')
            ->assertSet('step', 2)
            ->call('selectDoctor', $doctor->id)
            ->set('selectedDate', $date)
            ->call('selectSlot', '09:00', '09:30')
            ->call('proceedToStep3')
            ->assertSet('step', 3)
            ->set('firstName', 'Carlos')
            ->set('lastName', 'Mendoza')
            ->set('identificationType', 'CEDULA')
            ->set('identificationNumber', '0944332211')
            ->set('email', 'carlos.mendoza@testsur.com')
            ->set('phone', '0981122334')
            ->set('birthDate', '1990-05-10')
            ->set('gender', 'MALE')
            ->set('password', 'password123')
            ->set('reason', 'Chequeo cardiológico')
            ->call('proceedToStep4')
            ->assertSet('step', 4)
            ->call('fillDemoCard')
            ->assertSet('paymentMethod', 'CREDIT_CARD')
            ->call('confirmBooking')
            ->assertSet('step', 5)
            ->assertHasNoErrors();

        $code = $test->get('confirmedAppointmentCode');
        $this->assertNotEmpty($code);

        $appt = Appointment::where('code', $code)->with('payment')->first();
        $this->assertNotNull($appt);
        $this->assertEquals('CONFIRMED', $appt->status->value);
        $this->assertEquals('CREDIT_CARD', $appt->payment->payment_method->value);
        $this->assertEquals('PAID', $appt->payment->status->value);
    }

    public function test_booking_wizard_generates_payment_link_demo(): void
    {
        $user = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'license_number' => 'DOC-TEST-WIZ-2',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Pediatría Especial',
            'slug' => 'pediatria-especial',
            'base_price' => 60.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $doctor->specialties()->attach($specialty->id);

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => now()->addDays(2)->dayOfWeek,
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $date = now()->addDays(2)->format('Y-m-d');

        $test = Livewire::test(BookingWizard::class)
            ->call('selectSpecialty', $specialty->id)
            ->call('proceedToStep2')
            ->call('selectDoctor', $doctor->id)
            ->set('selectedDate', $date)
            ->call('selectSlot', '10:00', '10:30')
            ->call('proceedToStep3')
            ->set('firstName', 'Valeria')
            ->set('lastName', 'Paredes')
            ->set('identificationType', 'CEDULA')
            ->set('identificationNumber', '0922334455')
            ->set('email', 'valeria.paredes@testsur.com')
            ->set('phone', '0982233445')
            ->set('birthDate', '1996-08-20')
            ->set('gender', 'FEMALE')
            ->set('password', 'password123')
            ->call('proceedToStep4')
            ->assertSet('step', 4)
            ->call('generateDemoPaymentLink')
            ->assertSet('paymentMethod', 'PAYMENT_LINK')
            ->assertSet('paymentLinkSent', true)
            ->call('confirmBooking')
            ->assertSet('step', 5)
            ->assertHasNoErrors();

        $code = $test->get('confirmedAppointmentCode');
        $this->assertNotEmpty($code);

        $appt = Appointment::where('code', $code)->with('payment')->first();
        $this->assertNotNull($appt);
        $this->assertEquals('PENDING_PAYMENT', $appt->status->value);
        $this->assertEquals('PAYMENT_LINK', $appt->payment->payment_method->value);
        $this->assertEquals('PENDING', $appt->payment->status->value);
    }

    public function test_booking_wizard_bank_transfer(): void
    {
        $user = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'license_number' => 'DOC-TEST-WIZ-3',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Dermatología Especial',
            'slug' => 'dermatologia-especial',
            'base_price' => 70.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $doctor->specialties()->attach($specialty->id);

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => now()->addDays(2)->dayOfWeek,
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $date = now()->addDays(2)->format('Y-m-d');

        $test = Livewire::test(BookingWizard::class)
            ->call('selectSpecialty', $specialty->id)
            ->call('proceedToStep2')
            ->call('selectDoctor', $doctor->id)
            ->set('selectedDate', $date)
            ->call('selectSlot', '11:00', '11:30')
            ->call('proceedToStep3')
            ->set('firstName', 'Mateo')
            ->set('lastName', 'Rios')
            ->set('identificationType', 'CEDULA')
            ->set('identificationNumber', '0911223344')
            ->set('email', 'mateo.rios@testsur.com')
            ->set('phone', '0971122334')
            ->set('birthDate', '1992-03-14')
            ->set('gender', 'MALE')
            ->set('password', 'password123')
            ->call('proceedToStep4')
            ->assertSet('step', 4)
            ->set('paymentMethod', 'BANK_TRANSFER')
            ->set('transferBank', 'Banco Pichincha')
            ->set('transferReference', 'PICH-883719')
            ->call('confirmBooking')
            ->assertSet('step', 5)
            ->assertHasNoErrors();

        $code = $test->get('confirmedAppointmentCode');
        $this->assertNotEmpty($code);

        $appt = Appointment::where('code', $code)->with('payment')->first();
        $this->assertNotNull($appt);
        $this->assertEquals('PENDING_PAYMENT', $appt->status->value);
        $this->assertEquals('BANK_TRANSFER', $appt->payment->payment_method->value);
    }

    public function test_booking_wizard_bank_transfer_with_receipt_upload(): void
    {
        Storage::fake('receipts');

        $user = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'license_number' => 'DOC-TEST-WIZ-RECEIPT',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Traumatología Especial',
            'slug' => 'traumatologia-especial',
            'base_price' => 80.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $doctor->specialties()->attach($specialty->id);

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => now()->addDays(2)->dayOfWeek,
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $date = now()->addDays(2)->format('Y-m-d');
        $fakePdf = UploadedFile::fake()->create('comprobante_deposito.pdf', 350, 'application/pdf');

        $test = Livewire::test(BookingWizard::class)
            ->call('selectSpecialty', $specialty->id)
            ->call('proceedToStep2')
            ->call('selectDoctor', $doctor->id)
            ->set('selectedDate', $date)
            ->call('selectSlot', '14:00', '14:30')
            ->call('proceedToStep3')
            ->set('firstName', 'Gabriela')
            ->set('lastName', 'Moran')
            ->set('identificationType', 'CEDULA')
            ->set('identificationNumber', '0922338899')
            ->set('email', 'gabriela.moran@testsur.com')
            ->set('phone', '0993344556')
            ->set('birthDate', '1995-07-22')
            ->set('gender', 'FEMALE')
            ->set('password', 'password123')
            ->call('proceedToStep4')
            ->assertSet('step', 4)
            ->set('paymentMethod', 'BANK_TRANSFER')
            ->set('transferBank', 'Banco Pichincha')
            ->set('transferReference', 'PICH-992384')
            ->set('transferReceipt', $fakePdf)
            ->assertSet('uploadedReceiptName', 'comprobante_deposito.pdf')
            ->call('confirmBooking')
            ->assertSet('step', 5)
            ->assertHasNoErrors();

        $code = $test->get('confirmedAppointmentCode');
        $this->assertNotEmpty($code);

        $appt = Appointment::where('code', $code)->with(['payment.bankTransfer'])->first();
        $this->assertNotNull($appt);
        $this->assertEquals('PENDING_PAYMENT', $appt->status->value);
        $this->assertEquals('BANK_TRANSFER', $appt->payment->payment_method->value);

        $transfer = BankTransfer::where('payment_id', $appt->payment->id)->first();
        $this->assertNotNull($transfer);
        $this->assertEquals('Banco Pichincha', $transfer->bank_name);
        $this->assertEquals('PICH-992384', $transfer->reference_number);
        $this->assertEquals('comprobante_deposito.pdf', $transfer->receipt_original_name);
        $this->assertNotNull($transfer->receipt_path);
        Storage::disk('receipts')->assertExists($transfer->receipt_path);
    }

    public function test_manual_booking_with_existing_patient_cedula_autocompletes_without_crashing(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('SUPER_ADMIN');

        $user = User::factory()->create();
        $doctor = Doctor::create([
            'user_id' => $user->id,
            'license_number' => 'DOC-TEST-CED-1',
            'default_slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $specialty = Specialty::create([
            'name' => 'Medicina General',
            'slug' => 'medicina-general-test',
            'base_price' => 45.00,
            'default_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $doctor->specialties()->attach($specialty->id);

        $patientUser = User::factory()->create(['email' => 'paciente.existente@test.com']);
        $existingPatient = Patient::create([
            'user_id' => $patientUser->id,
            'first_name' => 'Maria',
            'last_name' => 'González',
            'identification_type' => \App\Domain\Patients\Enums\IdentificationType::CEDULA,
            'identification_number' => '0912345678',
            'email' => 'paciente.existente@test.com',
            'phone' => '0998877665',
            'birth_date' => '1988-04-12',
            'gender' => \App\Domain\Patients\Enums\Gender::FEMALE,
            'data_consent_accepted' => true,
        ]);

        DoctorSchedule::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => now()->addDays(2)->dayOfWeek,
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'slot_duration_minutes' => 30,
            'is_active' => true,
        ]);

        $date = now()->addDays(2)->format('Y-m-d');

        $this->actingAs($admin);

        Livewire::test(BookingWizard::class, ['isManual' => true])
            ->assertSet('step', 1)
            ->call('selectSpecialty', $specialty->id)
            ->call('selectProcedure', 0)
            ->call('proceedToStep2')
            ->assertSet('step', 2)
            ->call('selectDoctor', $doctor->id)
            ->set('selectedDate', $date)
            ->call('selectSlot', '10:00', '10:30')
            ->call('proceedToStep3')
            ->assertSet('step', 3)
            // Enter existing patient cedula
            ->set('identificationNumber', '0912345678')
            // Must stay on step 3!
            ->assertSet('step', 3)
            ->assertSet('patientExists', true)
            ->assertSet('firstName', 'Maria')
            ->assertSet('lastName', 'González')
            ->assertSet('phone', '0998877665')
            ->assertSet('cedulaStatus', 'valid')
            // Now confirm manual booking
            ->call('confirmBooking')
            ->assertSet('step', 5)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $existingPatient->id,
            'doctor_id' => $doctor->id,
            'specialty_id' => $specialty->id,
        ]);

        // If dataConsent is false, validation fails on manual booking
        Livewire::test(BookingWizard::class, ['isManual' => true])
            ->assertSet('step', 1)
            ->call('selectSpecialty', $specialty->id)
            ->call('selectProcedure', 0)
            ->call('proceedToStep2')
            ->call('selectDoctor', $doctor->id)
            ->set('selectedDate', $date)
            ->call('selectSlot', '11:00', '11:30')
            ->call('proceedToStep3')
            ->set('firstName', 'Juan')
            ->set('lastName', 'Pérez')
            ->set('identificationType', 'CEDULA')
            ->set('identificationNumber', '0998877665')
            ->set('email', 'juan.perez@test.com')
            ->set('phone', '0991122334')
            ->set('birthDate', '1992-05-15')
            ->set('gender', 'MALE')
            ->set('dataConsent', false)
            ->call('confirmBooking')
            ->assertHasErrors(['dataConsent' => 'accepted']);
    }
}
