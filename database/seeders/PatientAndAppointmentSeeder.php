<?php

namespace Database\Seeders;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Domain\Payments\Enums\BankTransferStatus;
use App\Domain\Payments\Enums\PaymentMethod;
use App\Domain\Payments\Enums\PaymentStatus;
use App\Models\Appointment;
use App\Models\AppointmentStatusHistory;
use App\Models\BankTransfer;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class PatientAndAppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@surhospital.com')->first();
        $doctors = Doctor::with('specialties')->get();
        if ($doctors->isEmpty()) {
            return;
        }

        // Ensure receipts directory exists
        $receiptsDir = storage_path('app/private/receipts');
        if (!File::exists($receiptsDir)) {
            File::makeDirectory($receiptsDir, 0755, true);
        }

        $patientsData = [
            [
                'name' => 'Ana Sofía Mendoza',
                'first_name' => 'Ana Sofía',
                'last_name' => 'Mendoza',
                'email' => 'paciente@surhospital.com',
                'password' => 'password',
                'phone' => '+593 99 123 4567',
                'identification_number' => '0958473621',
                'gender' => 'FEMALE',
                'birth_date' => '1994-08-12',
                'blood_type' => 'O+',
                'address' => 'Av. Francisco de Orellana y Plaza Quil, Guayaquil',
                'appointment_time' => Carbon::tomorrow()->setHour(10)->setMinute(0),
                'transfer_status' => BankTransferStatus::APPROVED,
                'bank' => 'Banco Pichincha',
                'ref' => 'PICH-9921001',
                'amount' => 45.00,
                'reason' => 'Consulta preventiva de chequeo general y revisión de rutina',
            ],
            [
                'name' => 'Carlos Mendoza',
                'first_name' => 'Carlos',
                'last_name' => 'Mendoza',
                'email' => 'carlos.mendoza@example.com',
                'phone' => '+593 99 111 2233',
                'identification_number' => '0928374111',
                'gender' => 'MALE',
                'birth_date' => '1985-03-12',
                'blood_type' => 'O+',
                'address' => 'Av. Francisco de Orellana, Guayaquil',
                'appointment_time' => Carbon::tomorrow()->setHour(10)->setMinute(0),
                'transfer_status' => BankTransferStatus::PENDING_VALIDATION,
                'bank' => 'Banco Pichincha',
                'ref' => 'PICH-88492019',
                'amount' => 45.00,
                'reason' => 'Chequeo cardiológico preventivo',
            ],
            [
                'name' => 'Elena Viteri',
                'first_name' => 'Elena',
                'last_name' => 'Viteri',
                'email' => 'elena.viteri@example.com',
                'phone' => '+593 98 777 8899',
                'identification_number' => '0918273222',
                'gender' => 'FEMALE',
                'birth_date' => '1991-08-25',
                'blood_type' => 'A+',
                'address' => 'Samborondón Plaza, Samborondón',
                'appointment_time' => Carbon::tomorrow()->setHour(11)->setMinute(30),
                'transfer_status' => BankTransferStatus::PENDING_VALIDATION,
                'bank' => 'Banco Guayaquil',
                'ref' => 'BG-9912034',
                'amount' => 50.00,
                'reason' => 'Consulta dermatológica por alergia',
            ],
            [
                'name' => 'Diego Noboa',
                'first_name' => 'Diego',
                'last_name' => 'Noboa',
                'email' => 'diego.noboa@example.com',
                'phone' => '+593 99 555 6677',
                'identification_number' => '0938475333',
                'gender' => 'MALE',
                'birth_date' => '1979-12-04',
                'blood_type' => 'B+',
                'address' => 'Los Ceibos, Guayaquil',
                'appointment_time' => Carbon::now()->addDays(2)->setHour(9)->setMinute(0),
                'transfer_status' => BankTransferStatus::PENDING_VALIDATION,
                'bank' => 'Produbanco',
                'ref' => 'PROD-441209',
                'amount' => 60.00,
                'reason' => 'Evaluación traumatológica rodilla',
            ],
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
                'appointment_time' => Carbon::now()->addDays(3)->setHour(15)->setMinute(0),
                'transfer_status' => BankTransferStatus::APPROVED,
                'bank' => 'Banco Pichincha',
                'ref' => 'PICH-55201934',
                'amount' => 45.00,
                'reason' => 'Control pediátrico regular',
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
                'appointment_time' => Carbon::now()->addDays(4)->setHour(16)->setMinute(0),
                'transfer_status' => BankTransferStatus::REJECTED,
                'bank' => 'Banco Bolivariano',
                'ref' => 'BOL-11029384',
                'amount' => 45.00,
                'reason' => 'Consulta general',
                'rejection_reason' => 'Comprobante borroso y valor de transferencia incompleto ($20 depositados de $45 requeridos).',
            ],
            [
                'name' => 'Gabriela Morales',
                'first_name' => 'Gabriela',
                'last_name' => 'Morales',
                'email' => 'gabriela.morales@example.com',
                'phone' => '+593 99 888 9900',
                'identification_number' => '0948576444',
                'gender' => 'FEMALE',
                'birth_date' => '1995-07-19',
                'blood_type' => 'O+',
                'address' => 'Puerto Santa Ana, Guayaquil',
                'appointment_time' => Carbon::now()->addDays(2)->setHour(14)->setMinute(0),
                'transfer_status' => BankTransferStatus::APPROVED,
                'bank' => 'Banco Internacional',
                'ref' => 'INT-7729104',
                'amount' => 50.00,
                'reason' => 'Consulta oftalmológica',
            ],
        ];

        foreach ($patientsData as $index => $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => Hash::make($data['password'] ?? 'password'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
            $user->syncRoles(['PATIENT']);

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
                    'emergency_contact_name' => 'Contacto Familiar',
                    'emergency_contact_phone' => '+593 99 000 1111',
                ]
            );

            $doc = $doctors[$index % $doctors->count()];
            $spec = $doc->specialties->first() ?? $doc->specialties()->first();

            $startAt = $data['appointment_time'];
            $endAt = $startAt->copy()->addMinutes($doc->consultation_duration_minutes ?: 30);

            $appointmentStatus = match ($data['transfer_status']) {
                BankTransferStatus::APPROVED => AppointmentStatus::CONFIRMED,
                BankTransferStatus::PENDING_VALIDATION => AppointmentStatus::PENDING_PAYMENT,
                BankTransferStatus::REJECTED => AppointmentStatus::CANCELLED,
            };

            $paymentStatus = match ($data['transfer_status']) {
                BankTransferStatus::APPROVED => PaymentStatus::PAID,
                BankTransferStatus::PENDING_VALIDATION => PaymentStatus::PENDING,
                BankTransferStatus::REJECTED => PaymentStatus::REJECTED,
            };

            $apptCode = "SUR-2026-T" . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            $appt = Appointment::firstOrCreate(
                ['code' => $apptCode],
                [
                    'patient_id' => $patient->id,
                    'doctor_id' => $doc->id,
                    'specialty_id' => $spec ? $spec->id : 1,
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'status' => $appointmentStatus,
                    'price' => $data['amount'],
                    'patient_notes' => $data['reason'],
                ]
            );

            $payment = Payment::firstOrCreate(
                ['appointment_id' => $appt->id],
                [
                    'payment_method' => PaymentMethod::BANK_TRANSFER,
                    'gateway_name' => 'BankTransfer',
                    'gateway_transaction_id' => "tx_{$data['ref']}",
                    'amount' => $data['amount'],
                    'currency' => 'USD',
                    'status' => $paymentStatus,
                    'paid_at' => $data['transfer_status'] === BankTransferStatus::APPROVED ? now() : null,
                ]
            );

            // Generate physical mock receipt file in receipts storage
            $receiptFileName = "receipt_{$appt->code}_{$data['ref']}.pdf";
            $receiptRelativePath = "receipts/{$receiptFileName}";
            $receiptFullPath = storage_path("app/private/{$receiptRelativePath}");

            $receiptContent = "%PDF-1.4 Mock Receipt for Appointment {$appt->code} - Bank: {$data['bank']} - Ref: {$data['ref']} - Amount: \${$data['amount']} USD";
            File::put($receiptFullPath, $receiptContent);

            BankTransfer::firstOrCreate(
                ['reference_number' => $data['ref']],
                [
                    'payment_id' => $payment->id,
                    'bank_name' => $data['bank'],
                    'receipt_path' => $receiptRelativePath,
                    'receipt_original_name' => "comprobante_{$data['ref']}.pdf",
                    'amount' => $data['amount'],
                    'transfer_date' => Carbon::now()->subDays($index),
                    'notes' => 'Comprobante transferido mediante banca móvil.',
                    'validation_status' => $data['transfer_status'],
                    'validated_by_user_id' => $data['transfer_status'] !== BankTransferStatus::PENDING_VALIDATION && $adminUser ? $adminUser->id : null,
                    'validated_at' => $data['transfer_status'] !== BankTransferStatus::PENDING_VALIDATION ? now() : null,
                    'rejection_reason' => $data['rejection_reason'] ?? null,
                ]
            );

            AppointmentStatusHistory::firstOrCreate(
                [
                    'appointment_id' => $appt->id,
                    'new_status' => $appointmentStatus->value,
                ],
                [
                    'previous_status' => AppointmentStatus::PENDING_PAYMENT->value,
                    'reason' => 'Registro de cita y comprobante de transferencia bancaria',
                    'created_at' => now(),
                ]
            );
        }
    }
}
