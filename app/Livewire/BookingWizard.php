<?php

namespace App\Livewire;

use App\Domain\Appointments\DTOs\CreateAppointmentDTO;
use App\Domain\Appointments\Services\AppointmentBookingService;
use App\Domain\Availability\Services\AvailabilityService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Domain\Payments\Enums\BankTransferStatus;
use App\Mail\PaymentLinkMail;
use App\Models\Appointment;
use App\Models\BankTransfer;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Permission\Models\Role;

class BookingWizard extends Component
{
    use WithFileUploads;

    /**
     * Wizard steps:
     * 1: Especialidad & Servicio (Consulta / Procedimiento)
     * 2: Especialista & Fecha / Turno
     * 3: Datos del Paciente (Registro / Verificación)
     * 4: Método de Pago (Tarjeta, Link de Pago, Transferencia)
     * 5: Confirmación & Comprobante Digital
     */
    public int $step = 1;

    // Step 1: Specialty & Procedure
    public ?int $specialtyId = null;
    public ?int $procedureId = null; // null or 0 = Consulta base, >0 = Procedimiento específico

    // Step 2: Doctor & Time Slot
    public ?int $doctorId = null;
    public string $selectedDate = '';
    public string $selectedTime = '';
    public string $selectedEndTime = '';

    // Step 3: Patient Info
    public string $firstName = '';
    public string $lastName = '';
    public string $identificationType = 'CEDULA';
    public string $identificationNumber = '';
    public string $email = '';
    public string $phone = '';
    public string $birthDate = '';
    public string $gender = 'OTHER';
    public string $password = '';
    public string $reason = '';
    public bool $dataConsent = true;
    public ?string $cedulaStatus = null;
    public string $cedulaMessage = '';
    public string $provinceName = '';

    // Step 4: Payment Selection & Gateway Demo Data
    public string $paymentMethod = 'CREDIT_CARD'; // CREDIT_CARD, PAYMENT_LINK, BANK_TRANSFER, CASH, CARD_ONSITE
    public string $cardHolder = '';
    public string $cardNumber = '';
    public string $cardExpiry = '';
    public string $cardCvv = '';
    public string $generatedPaymentLink = '';
    public bool $paymentLinkSent = false;
    public string $transferBank = 'Banco Pichincha';
    public string $transferReference = '';
    public string $transferDate = '';
    public string $transferNotes = '';
    public $transferReceipt = null;
    public ?string $uploadedReceiptName = null;
    public ?string $uploadedReceiptSize = null;

    // Step 5: Confirmation Data
    public ?int $confirmedAppointmentId = null;
    public ?string $confirmedAppointmentCode = null;
    public ?string $errorMessage = null;

    public bool $isManual = false;

    protected function rules(): array
    {
        if ($this->step === 3) {
            if (!$this->isManual && Auth::check() && Auth::user()->patient) {
                return [
                    'reason' => 'nullable|string|max:500',
                ];
            }

            $rules = [
                'firstName' => 'required|string|min:2|max:100',
                'lastName' => 'required|string|min:2|max:100',
                'identificationType' => 'required|string|in:CEDULA,PASAPORTE,RUC',
                'identificationNumber' => 'required|string|max:30',
                'email' => 'required|email|max:150',
                'phone' => 'required|string|min:7|max:25',
                'birthDate' => 'required|date|before:today',
                'gender' => 'required|string|in:MALE,FEMALE,OTHER',
                'reason' => 'nullable|string|max:500',
            ];

            if (!$this->isManual && !Auth::check()) {
                $rules['password'] = 'required|string|min:8';
                $rules['dataConsent'] = 'accepted';
            }

            return $rules;
        }

        if ($this->step === 4) {
            if ($this->paymentMethod === 'CREDIT_CARD') {
                return [
                    'cardHolder' => 'required|string|min:3',
                    'cardNumber' => 'required|string|min:14',
                    'cardExpiry' => 'required|string|min:4',
                    'cardCvv' => 'required|string|min:3|max:4',
                ];
            }

            if ($this->paymentMethod === 'BANK_TRANSFER' || $this->paymentMethod === 'TRANSFER') {
                $rules = [
                    'transferBank' => 'required|string|max:100',
                    'transferReference' => 'nullable|string|max:50',
                    'transferDate' => 'nullable|date',
                    'transferNotes' => 'nullable|string|max:500',
                ];
                if ($this->transferReceipt) {
                    $rules['transferReceipt'] = 'file|mimes:jpg,jpeg,png,webp,pdf|max:10240';
                }
                return $rules;
            }

            if ($this->transferReceipt) {
                return [
                    'transferReceipt' => 'file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
                ];
            }
        }

        return [];
    }

    protected $messages = [
        'firstName.required' => 'El nombre del paciente es obligatorio.',
        'lastName.required' => 'El apellido del paciente es obligatorio.',
        'identificationNumber.required' => 'El número de identificación es obligatorio.',
        'email.required' => 'El correo electrónico es obligatorio para el envío del comprobante.',
        'phone.required' => 'El teléfono / WhatsApp es obligatorio para confirmación de turnos.',
        'birthDate.required' => 'La fecha de nacimiento es obligatoria.',
        'password.required' => 'Por favor define una contraseña para consultar tus citas.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'cardHolder.required' => 'Ingresa el nombre del titular como figura en la tarjeta.',
        'cardNumber.required' => 'Ingresa el número de tarjeta (16 dígitos).',
        'cardExpiry.required' => 'Ingresa la fecha de vencimiento (MM/AA).',
        'cardCvv.required' => 'Ingresa el código de seguridad CVV.',
        'transferBank.required' => 'Por favor selecciona la entidad bancaria.',
        'transferReceipt.file' => 'El comprobante debe ser un archivo válido.',
        'transferReceipt.mimes' => 'El comprobante debe ser una imagen (JPG, PNG, WEBP) o un documento PDF.',
        'transferReceipt.max' => 'El archivo del comprobante no debe superar los 10MB.',
    ];

    public function updatedTransferReceipt(): void
    {
        $this->validateOnly('transferReceipt', [
            'transferReceipt' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);
        if ($this->transferReceipt) {
            $this->uploadedReceiptName = $this->transferReceipt->getClientOriginalName();
            $this->uploadedReceiptSize = round($this->transferReceipt->getSize() / 1024, 1) . ' KB';
        }
    }

    public function removeReceipt(): void
    {
        $this->transferReceipt = null;
        $this->uploadedReceiptName = null;
        $this->uploadedReceiptSize = null;
    }

    public function mount(bool $isManual = false): void
    {
        if (Auth::check() && Auth::user()->isDoctor()) {
            abort(403, 'Los usuarios con rol médico no tienen permisos para crear citas.');
        }

        $this->isManual = $isManual;
        $this->selectedDate = now()->addDay()->format('Y-m-d');
        $this->paymentMethod = $isManual ? 'PAYMENT_LINK' : 'CREDIT_CARD';

        if (!$this->isManual && Auth::check() && Auth::user()->patient) {
            $patient = Auth::user()->patient;
            $this->firstName = $patient->first_name;
            $this->lastName = $patient->last_name;
            $this->identificationType = $patient->identification_type->value ?? 'CEDULA';
            $this->identificationNumber = $patient->identification_number;
            $this->email = Auth::user()->email;
            $this->phone = $patient->phone;
            $this->birthDate = $patient->birth_date?->format('Y-m-d') ?? '';
            $this->gender = $patient->gender->value ?? 'OTHER';
            $this->cardHolder = strtoupper(Auth::user()->name);
        }
    }

    public function updatedIdentificationNumber($value): void
    {
        if ($this->identificationType === 'CEDULA') {
            $clean = preg_replace('/\D/', '', (string) $value);
            $this->identificationNumber = substr($clean, 0, 10);

            if (strlen($this->identificationNumber) === 10) {
                $idService = app(\App\Domain\Patients\Services\EcuadorianIdService::class);
                $result = $idService->lookup($this->identificationNumber);

                if ($result['is_valid']) {
                    $this->cedulaStatus = 'valid';
                    $this->provinceName = $result['province'] ?? '';
                    $this->cedulaMessage = $result['message'] ?? 'Cédula válida en Registro Civil';

                    if (!empty($result['first_name']) && empty($this->firstName)) {
                        $this->firstName = $result['first_name'];
                    }
                    if (!empty($result['last_name']) && empty($this->lastName)) {
                        $this->lastName = $result['last_name'];
                    }
                    if (!empty($result['birth_date']) && empty($this->birthDate)) {
                        $this->birthDate = $result['birth_date'];
                    }
                    if (!empty($result['gender']) && $this->gender === 'OTHER') {
                        $this->gender = $result['gender'];
                    }
                    if (!empty($result['email']) && empty($this->email)) {
                        $this->email = $result['email'];
                    }
                    if (!empty($result['phone']) && empty($this->phone)) {
                        $this->phone = $result['phone'];
                    }
                } else {
                    $this->cedulaStatus = 'invalid';
                    $this->cedulaMessage = $result['error'] ?? 'Cédula no válida según el algoritmo del Registro Civil.';
                }
            } else {
                $this->cedulaStatus = null;
                $this->cedulaMessage = '';
                $this->provinceName = '';
            }
        }
    }

    // Step 1 Actions
    public function selectSpecialty(int $id): void
    {
        $this->specialtyId = $id;
        $this->procedureId = 0; // default to general consultation
        $this->doctorId = null;
        $this->selectedTime = '';
        $this->selectedEndTime = '';
        $this->errorMessage = null;
    }

    public function selectProcedure(int $id): void
    {
        $this->procedureId = $id > 0 ? $id : 0;
        $this->errorMessage = null;
    }

    public function proceedToStep2(): void
    {
        $this->errorMessage = null;
        if (!$this->specialtyId) {
            $this->errorMessage = 'Por favor selecciona una especialidad médica antes de continuar.';
            return;
        }
        $this->step = 2;
    }

    // Step 2 Actions
    public function selectDoctor(int $id): void
    {
        $this->doctorId = $id;
        $this->selectedTime = '';
        $this->selectedEndTime = '';
        $this->errorMessage = null;
    }

    public function selectSlot(string $startTime, string $endTime): void
    {
        $this->selectedTime = $startTime;
        $this->selectedEndTime = $endTime;
        $this->errorMessage = null;
    }

    public function proceedToStep3(): void
    {
        $this->errorMessage = null;
        if (!$this->doctorId) {
            $this->errorMessage = 'Por favor selecciona un médico especialista.';
            return;
        }
        if (empty($this->selectedTime)) {
            $this->errorMessage = 'Por favor selecciona un turno u horario disponible.';
            return;
        }
        $this->step = 3;
    }

    // Step 3 Actions
    public function proceedToStep4(): void
    {
        $this->errorMessage = null;
        $this->validate();

        if ($this->isManual) {
            $this->confirmBooking(app(AppointmentBookingService::class), app(AvailabilityService::class));
            return;
        }

        if (empty($this->cardHolder) && !empty($this->firstName)) {
            $this->cardHolder = strtoupper("{$this->firstName} {$this->lastName}");
        }

        $this->step = 4;
    }

    // Reset Wizard (Para volver a agendar otra cita desde el modal)
    public function resetWizard(): void
    {
        $this->step = 1;
        $this->specialtyId = null;
        $this->procedureId = null;
        $this->doctorId = null;
        $this->selectedDate = now()->addDay()->format('Y-m-d');
        $this->selectedTime = '';
        $this->selectedEndTime = '';
        $this->firstName = '';
        $this->lastName = '';
        $this->identificationType = 'CEDULA';
        $this->identificationNumber = '';
        $this->email = '';
        $this->phone = '';
        $this->birthDate = '';
        $this->gender = 'OTHER';
        $this->password = '';
        $this->reason = '';
        $this->paymentMethod = $this->isManual ? 'PAYMENT_LINK' : 'CREDIT_CARD';
        $this->confirmedAppointmentId = null;
        $this->confirmedAppointmentCode = null;
        $this->errorMessage = null;
        $this->paymentLinkSent = false;
        $this->generatedPaymentLink = '';
        $this->uploadedReceiptName = null;
        $this->uploadedReceiptSize = null;
        $this->transferReceipt = null;
    }

    // Step 4 Actions & Payment Demos
    public function fillDemoCard(): void
    {
        $this->paymentMethod = 'CREDIT_CARD';
        $this->cardNumber = '4532 8900 1234 5678';
        $this->cardHolder = !empty($this->firstName) ? strtoupper("{$this->firstName} {$this->lastName}") : 'JUAN CARLOS PÉREZ';
        $this->cardExpiry = '12/28';
        $this->cardCvv = '842';
        $this->errorMessage = null;
    }

    public function generateDemoPaymentLink(): void
    {
        $this->paymentMethod = 'PAYMENT_LINK';
        if (empty($this->generatedPaymentLink)) {
            $this->generatedPaymentLink = url('/paciente/citas/' . ($this->confirmedAppointmentId ?: 'checkout'));
        }
        $this->paymentLinkSent = true;
        $this->errorMessage = null;
    }

    public function resendPaymentLinkEmail(): void
    {
        if (empty($this->email)) {
            $this->errorMessage = 'No se encontró un correo electrónico registrado para enviar el enlace.';
            return;
        }

        $appointment = $this->confirmedAppointmentId ? Appointment::find($this->confirmedAppointmentId) : null;
        $amount = 0;
        if ($this->specialtyId) {
            $proc = ($this->procedureId && $this->procedureId > 0) ? Procedure::find($this->procedureId) : null;
            $spec = Specialty::find($this->specialtyId);
            $amount = $proc ? $proc->price : ($spec ? $spec->base_price : 45);
        }

        if (empty($this->generatedPaymentLink)) {
            $this->generatedPaymentLink = $appointment ? route('patient.appointment.show', $appointment) : url('/paciente');
        }

        if ($appointment) {
            try {
                Mail::to($this->email)->send(new PaymentLinkMail(
                    appointment: $appointment,
                    paymentLink: $this->generatedPaymentLink,
                    amount: (float) $amount,
                    patientName: "{$this->firstName} {$this->lastName}"
                ));
                $this->paymentLinkSent = true;
                $this->errorMessage = null;
            } catch (\Throwable $e) {
                logger()->error('Error reenviando correo de pago: ' . $e->getMessage());
            }
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->proceedToStep2();
        } elseif ($this->step === 2) {
            $this->proceedToStep3();
        } elseif ($this->step === 3) {
            $this->proceedToStep4();
        }
    }

    public function previousStep(): void
    {
        $this->errorMessage = null;
        if ($this->step > 1 && $this->step < 5) {
            $this->step--;
        }
    }

    public function goToStep(int $targetStep): void
    {
        if ($targetStep < 1 || $targetStep >= $this->step || $this->step === 5) {
            return;
        }
        $this->step = $targetStep;
        $this->errorMessage = null;
    }

    // Final Confirmation
    public function confirmBooking(
        AppointmentBookingService $bookingService,
        AvailabilityService $availabilityService
    ): void {
        $this->errorMessage = null;

        if ($this->isManual && Auth::check() && Auth::user()->isDoctor()) {
            $this->errorMessage = 'Los usuarios con rol médico no tienen autorización para crear citas.';
            return;
        }

        if ($this->isManual) {
            $this->paymentMethod = 'PAYMENT_LINK';
            $this->validate();
        } elseif ($this->paymentMethod === 'CREDIT_CARD') {
            if (empty($this->cardNumber) || empty($this->cardHolder) || empty($this->cardExpiry) || empty($this->cardCvv)) {
                $this->errorMessage = 'Por favor completa los datos de la tarjeta de crédito o débito o utiliza el botón de datos de prueba.';
                return;
            }
        }

        try {
            DB::beginTransaction();

            $patient = null;

            if ($this->isManual) {
                // Admin/Receptionist creating appointment
                $patient = Patient::where('identification_number', $this->identificationNumber)->first();

                if (!$patient) {
                    $user = User::where('email', $this->email)->first();
                    if (!$user) {
                        $user = User::create([
                            'name' => "{$this->firstName} {$this->lastName}",
                            'email' => $this->email,
                            'password' => Hash::make($this->password ?: 'password123'),
                            'is_active' => true,
                            'email_verified_at' => now(),
                        ]);
                        $role = Role::firstOrCreate(['name' => 'PATIENT', 'guard_name' => 'web']);
                        $user->assignRole($role);
                    }

                    $patient = Patient::create([
                        'user_id' => $user->id,
                        'first_name' => $this->firstName,
                        'last_name' => $this->lastName,
                        'identification_type' => IdentificationType::tryFrom($this->identificationType) ?? IdentificationType::CEDULA,
                        'identification_number' => $this->identificationNumber,
                        'phone' => $this->phone,
                        'birth_date' => $this->birthDate ?: '1990-01-01',
                        'gender' => Gender::tryFrom($this->gender) ?? Gender::OTHER,
                        'data_consent_accepted' => true,
                        'data_consent_accepted_at' => now(),
                        'data_consent_ip' => request()->ip(),
                        'data_consent_user_agent' => request()->userAgent(),
                    ]);
                }
            } elseif (Auth::check() && Auth::user()->patient) {
                $patient = Auth::user()->patient;
            } else {
                // Public guest registration
                $patient = Patient::where('identification_number', $this->identificationNumber)->first();
                $user = $patient ? $patient->user : User::where('email', $this->email)->first();

                if ($user && $user->patient && !$patient) {
                    $patient = $user->patient;
                }

                if (!$patient) {
                    if (!$user) {
                        $user = User::create([
                            'name' => "{$this->firstName} {$this->lastName}",
                            'email' => $this->email,
                            'phone' => $this->phone,
                            'password' => Hash::make($this->password ?: 'password123'),
                            'is_active' => true,
                            'email_verified_at' => now(),
                        ]);
                        $role = Role::firstOrCreate(['name' => 'PATIENT', 'guard_name' => 'web']);
                        $user->assignRole($role);
                    }

                    $patient = Patient::create([
                        'user_id' => $user->id,
                        'first_name' => $this->firstName,
                        'last_name' => $this->lastName,
                        'identification_type' => IdentificationType::tryFrom($this->identificationType) ?? IdentificationType::CEDULA,
                        'identification_number' => $this->identificationNumber,
                        'email' => $this->email,
                        'phone' => $this->phone,
                        'birth_date' => $this->birthDate ?: '1990-01-01',
                        'gender' => Gender::tryFrom($this->gender) ?? Gender::OTHER,
                        'data_consent_accepted' => true,
                        'data_consent_accepted_at' => now(),
                        'data_consent_ip' => request()->ip(),
                        'data_consent_user_agent' => request()->userAgent(),
                    ]);
                } else {
                    $patient->update([
                        'data_consent_accepted' => true,
                        'data_consent_accepted_at' => now(),
                        'data_consent_ip' => request()->ip(),
                        'data_consent_user_agent' => request()->userAgent(),
                    ]);
                }

                if ($user) {
                    Auth::login($user);
                }
            }

            if (!$patient) {
                throw new \Exception('No se pudo procesar la ficha clínica del paciente.');
            }

            $doctor = Doctor::findOrFail($this->doctorId);
            $specialty = Specialty::findOrFail($this->specialtyId);
            $procedure = ($this->procedureId && $this->procedureId > 0) ? Procedure::find($this->procedureId) : null;

            $startAt = Carbon::parse("{$this->selectedDate} {$this->selectedTime}");
            $duration = $procedure ? $procedure->duration_minutes : ($doctor->consultation_duration_minutes ?? $specialty->default_duration_minutes ?? 30);
            $endAt = (clone $startAt)->addMinutes($duration);

            $amount = $procedure ? $procedure->price : $specialty->base_price;

            $paymentMethodValue = match ($this->paymentMethod) {
                'CREDIT_CARD' => 'CREDIT_CARD',
                'PAYMENT_LINK' => 'PAYMENT_LINK',
                'BANK_TRANSFER' => 'BANK_TRANSFER',
                'CASH' => 'CASH',
                'CARD_ONSITE' => 'CARD_ONSITE',
                default => 'CREDIT_CARD',
            };

            $notes = $this->reason ?: '';
            if ($this->paymentMethod === 'BANK_TRANSFER' && !empty($this->transferReference)) {
                $notes .= ($notes ? ' | ' : '') . "Comprobante Transferencia: {$this->transferBank} #{$this->transferReference}";
            }

            $dto = new CreateAppointmentDTO(
                patientId: $patient->id,
                doctorId: $doctor->id,
                specialtyId: $specialty->id,
                procedureId: $procedure?->id,
                startAt: $startAt,
                endAt: $endAt,
                price: (float) $amount,
                patientNotes: $notes ?: null,
                createdByUserId: Auth::id(),
                paymentMethod: $paymentMethodValue,
            );

            $appointment = $bookingService->book($dto);

            // Registrar transferencia bancaria si se adjuntó comprobante o datos de transferencia
            if ($this->transferReceipt || ($this->paymentMethod === 'BANK_TRANSFER' && !empty($this->transferReference))) {
                $payment = $appointment->payments()->first();
                if ($payment) {
                    $receiptPath = null;
                    $receiptOriginalName = null;
                    if ($this->transferReceipt) {
                        $ext = $this->transferReceipt->getClientOriginalExtension() ?: 'pdf';
                        $filename = 'receipt_' . $appointment->code . '_' . Str::random(12) . '.' . $ext;
                        $receiptPath = str_replace('\\', '/', $this->transferReceipt->storeAs('receipts', $filename, 'receipts'));
                        $receiptOriginalName = $this->transferReceipt->getClientOriginalName();
                        $this->uploadedReceiptName = $receiptOriginalName;
                    }

                    BankTransfer::create([
                        'payment_id' => $payment->id,
                        'bank_name' => $this->transferBank ?: 'Banco Pichincha',
                        'reference_number' => $this->transferReference ?: ('TRANS-' . strtoupper(Str::random(6))),
                        'receipt_path' => $receiptPath,
                        'receipt_original_name' => $receiptOriginalName,
                        'amount' => (float) $amount,
                        'transfer_date' => $this->transferDate ?: now()->format('Y-m-d'),
                        'notes' => $this->transferNotes ?: null,
                        'validation_status' => BankTransferStatus::PENDING_VALIDATION,
                    ]);
                }
            }

            DB::commit();

            $this->confirmedAppointmentId = $appointment->id;
            $this->confirmedAppointmentCode = $appointment->code;
            // Enviar correo electrónico institucional si el método de pago es LINK DE PAGO o creación por recepción
            if (!empty($this->email)) {
                if (empty($this->generatedPaymentLink) || str_contains($this->generatedPaymentLink, 'checkout')) {
                    $this->generatedPaymentLink = route('patient.appointment.show', $appointment);
                }
                try {
                    Mail::to($this->email)->send(new PaymentLinkMail(
                        appointment: $appointment,
                        paymentLink: $this->generatedPaymentLink,
                        amount: (float) $amount,
                        patientName: "{$this->firstName} {$this->lastName}"
                    ));
                    $this->paymentLinkSent = true;
                } catch (\Throwable $mailException) {
                    logger()->error('Error enviando correo de link de pago: ' . $mailException->getMessage());
                }
            }

            $this->step = 5;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render(AvailabilityService $availabilityService)
    {
        $specialties = Specialty::where('is_active', true)->withCount('doctors')->orderBy('name')->get();

        $procedures = collect();
        if ($this->specialtyId) {
            $procedures = Procedure::where('specialty_id', $this->specialtyId)->where('is_active', true)->orderBy('name')->get();
        }

        $doctors = collect();
        if ($this->specialtyId) {
            $doctors = Doctor::where('is_active', true)
                ->whereHas('specialties', fn ($q) => $q->where('specialties.id', $this->specialtyId))
                ->with(['user', 'specialties'])
                ->get();
        }

        $availableSlots = collect();
        if ($this->doctorId && !empty($this->selectedDate)) {
            $doctor = Doctor::find($this->doctorId);
            $procedure = ($this->procedureId && $this->procedureId > 0) ? Procedure::find($this->procedureId) : null;
            $date = Carbon::parse($this->selectedDate);

            if ($doctor) {
                $duration = $procedure ? $procedure->duration_minutes : null;
                $availableSlots = $availabilityService->getAvailableSlotsForDate(
                    $doctor,
                    $date,
                    $duration,
                    $this->specialtyId,
                    $this->procedureId
                );
            }
        }

        $selectedSpecialty = $this->specialtyId ? Specialty::find($this->specialtyId) : null;
        $selectedProcedure = ($this->procedureId && $this->procedureId > 0) ? Procedure::find($this->procedureId) : null;
        $selectedDoctor = $this->doctorId ? Doctor::with('user')->find($this->doctorId) : null;

        $amount = 0;
        if ($selectedProcedure) {
            $amount = $selectedProcedure->price;
        } elseif ($selectedSpecialty) {
            $amount = $selectedSpecialty->base_price;
        }

        return view('livewire.booking-wizard', [
            'specialties' => $specialties,
            'procedures' => $procedures,
            'doctors' => $doctors,
            'availableSlots' => $availableSlots,
            'selectedSpecialty' => $selectedSpecialty,
            'selectedProcedure' => $selectedProcedure,
            'selectedDoctor' => $selectedDoctor,
            'amount' => $amount,
        ]);
    }
}
