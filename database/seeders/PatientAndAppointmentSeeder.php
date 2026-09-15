<?php

namespace Database\Seeders;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Payments\Enums\PaymentMethod;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Models\Appointment;
use App\Models\AppointmentStatusHistory;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PatientAndAppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $patientsData = [
            [
                'name' => 'María Salazar',
                'first_name' => 'María',
                'last_name' => 'Salazar',
                'email' => 'paciente.maria@example.com',
                'phone' => '+593 99 333 4444',
                'identification_number' => '0928374651',
                'gender' => 'FEMALE',
                'birth_date' => '1992-05-14',
                'blood_type' => 'O+',
                'address' => 'Urdesa Central, Guayaquil',
            ],
            [
                'name' => 'Juan Ortiz',
                'first_name' => 'Juan',
                'last_name' => 'Ortiz',
                'email' => 'paciente.juan@example.com',
                'phone' => '+593 98 444 5555',
                'identification_number' => '0918273645',
                'gender' => 'MALE',
                'birth_date' => '1988-11-20',
                'blood_type' => 'A+',
                'address' => 'Samborondón Km 3.5, Guayaquil',
            ],
        ];

        $doctors = Doctor::with('specialties')->get();
        if ($doctors->isEmpty()) {
            return;
        }

        foreach ($patientsData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
            $user->assignRole('PATIENT');

            $patient = Patient::firstOrCreate(
                ['identification_number' => $data['identification_number']],
                [
                    'user_id' => $user->id,
                    'identification_type' => 'CEDULA',
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'gender' => $data['gender'],
                    'birth_date' => $data['birth_date'],
                    'blood_type' => $data['blood_type'],
                    'address' => $data['address'],
                    'emergency_contact_name' => 'Familiar de Referencia',
                    'emergency_contact_phone' => '+593 99 000 1111',
                ]
            );

            // Create upcoming confirmed appointment tomorrow
            $doc1 = $doctors->first();
            $spec1 = $doc1->specialties->first();
            $tomorrowStart = Carbon::tomorrow()->setHour(9)->setMinute(0)->setSecond(0);
            $tomorrowEnd = $tomorrowStart->copy()->addMinutes(30);

            $appt1 = Appointment::firstOrCreate(
                ['code' => "SUR-2026-{$patient->id}01"],
                [
                    'patient_id' => $patient->id,
                    'doctor_id' => $doc1->id,
                    'specialty_id' => $spec1 ? $spec1->id : 1,
                    'start_at' => $tomorrowStart,
                    'end_at' => $tomorrowEnd,
                    'status' => AppointmentStatus::CONFIRMED,
                    'price' => $doc1->default_consultation_price ?: 45.00,
                    'patient_notes' => 'Consulta de control y seguimiento de rutina.',
                ]
            );

            Payment::firstOrCreate(
                ['appointment_id' => $appt1->id],
                [
                    'payment_method' => PaymentMethod::ONLINE,
                    'gateway_name' => 'MockOnline',
                    'gateway_transaction_id' => "seed_tx_{$appt1->id}",
                    'amount' => $appt1->price,
                    'currency' => 'USD',
                    'status' => PaymentStatus::PAID,
                    'paid_at' => now(),
                ]
            );

            AppointmentStatusHistory::firstOrCreate(
                [
                    'appointment_id' => $appt1->id,
                    'new_status' => AppointmentStatus::CONFIRMED->value,
                ],
                [
                    'previous_status' => AppointmentStatus::PENDING_PAYMENT->value,
                    'reason' => 'Cita confirmada con pago inicial',
                    'created_at' => now(),
                ]
            );

            // Create completed appointment in the past
            $doc2 = $doctors->count() > 1 ? $doctors[1] : $doctors[0];
            $spec2 = $doc2->specialties->first();
            $pastStart = Carbon::now()->subDays(10)->setHour(11)->setMinute(0)->setSecond(0);
            $pastEnd = $pastStart->copy()->addMinutes(30);

            $appt2 = Appointment::firstOrCreate(
                ['code' => "SUR-2026-{$patient->id}02"],
                [
                    'patient_id' => $patient->id,
                    'doctor_id' => $doc2->id,
                    'specialty_id' => $spec2 ? $spec2->id : 1,
                    'start_at' => $pastStart,
                    'end_at' => $pastEnd,
                    'status' => AppointmentStatus::COMPLETED,
                    'price' => $doc2->default_consultation_price ?: 35.00,
                    'patient_notes' => 'Chequeo pediátrico / preventivo',
                    'clinical_notes' => 'Paciente en óptimas condiciones generales. Se indica continuar con plan alimentario y control en 6 meses.',
                ]
            );

            Payment::firstOrCreate(
                ['appointment_id' => $appt2->id],
                [
                    'payment_method' => PaymentMethod::BANK_TRANSFER,
                    'gateway_name' => 'BankTransfer',
                    'amount' => $appt2->price,
                    'currency' => 'USD',
                    'status' => PaymentStatus::PAID,
                    'paid_at' => $pastStart,
                ]
            );
        }
    }
}
