<?php

namespace App\Livewire;

use App\Domain\Appointments\Services\AppointmentBookingService;
use App\Domain\Availability\Services\AvailabilityService;
use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Domain\Payments\Enums\PaymentMethod;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Procedure;
use App\Models\Specialty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class BookingWizard extends Component
{
    // Wizard step: 1: Specialty, 2: Procedure, 3: Doctor, 4: Date/Time Slot, 5: Patient Info/Auth, 6: Payment, 7: Confirmation
    public int $step = 1;

    // Selections
    public ?int $specialtyId = null;
    public ?int $procedureId = null;
    public ?int $doctorId = null;
    public string $selectedDate = '';
    public string $selectedTime = '';
    public string $selectedEndTime = '';

    // Patient info (if guest)
    public string $firstName = '';
    public string $lastName = '';
    public string $identificationType = 'CC';
    public string $identificationNumber = '';
    public string $email = '';
    public string $phone = '';
    public string $birthDate = '';
    public string $gender = 'OTHER';
    public string $password = '';
    public string $reason = '';

    // Payment selection
    public string $paymentMethod = 'ONLINE'; // 'ONLINE' or 'TRANSFER'

    // Confirmation
    public ?int $confirmedAppointmentId = null;
    public ?string $confirmedAppointmentCode = null;
    public ?string $errorMessage = null;

    protected function rules(): array
    {
        if ($this->step === 5) {
            if (Auth::check()) {
                return [
                    'reason' => 'nullable|string|max:500',
                ];
            }

            return [
                'firstName' => 'required|string|min:2|max:100',
                'lastName' => 'required|string|min:2|max:100',
                'identificationType' => 'required|string|in:CC,TI,CE,PASSPORT,OTHER',
                'identificationNumber' => 'required|string|max:30',
                'email' => 'required|email|max:150',
                'phone' => 'required|string|min:7|max:25',
                'birthDate' => 'required|date|before:today',
                'gender' => 'required|string|in:MALE,FEMALE,OTHER',
                'password' => 'required|string|min:8',
                'reason' => 'nullable|string|max:500',
            ];
        }

        return [];
    }

    public function mount(): void
    {
        $this->selectedDate = now()->addDay()->format('Y-m-d');

        if (Auth::check() && Auth::user()->patient) {
            $patient = Auth::user()->patient;
            $this->firstName = $patient->first_name;
            $this->lastName = $patient->last_name;
            $this->identificationType = $patient->identification_type->value ?? 'CC';
            $this->identificationNumber = $patient->identification_number;
            $this->email = Auth::user()->email;
            $this->phone = $patient->phone;
            $this->birthDate = $patient->birth_date?->format('Y-m-d') ?? '';
            $this->gender = $patient->gender->value ?? 'OTHER';
        }
    }

    public function selectSpecialty(int $id): void
    {
        $this->specialtyId = $id;
        $this->procedureId = null;
        $this->doctorId = null;
        $this->step = 2;
        $this->errorMessage = null;
    }

    public function selectProcedure(int $id): void
    {
        $this->procedureId = $id;
        $this->doctorId = null;
        $this->step = 3;
        $this->errorMessage = null;
    }

    public function selectDoctor(int $id): void
    {
        $this->doctorId = $id;
        $this->step = 4;
        $this->errorMessage = null;
    }

    public function selectSlot(string $startTime, string $endTime): void
    {
        $this->selectedTime = $startTime;
        $this->selectedEndTime = $endTime;
        $this->errorMessage = null;
    }

    public function nextStep(): void
    {
        $this->errorMessage = null;

        if ($this->step === 4) {
            if (empty($this->selectedTime)) {
                $this->errorMessage = 'Por favor selecciona un horario de consulta disponible.';
                return;
            }
            $this->step = 5;
            return;
        }

        if ($this->step === 5) {
            $this->validate();
            $this->step = 6;
            return;
        }
    }

    public function previousStep(): void
    {
        $this->errorMessage = null;
        if ($this->step > 1 && $this->step < 7) {
            $this->step--;
        }
    }

    public function confirmBooking(
        AppointmentBookingService $bookingService,
        AvailabilityService $availabilityService
    ): void {
        $this->errorMessage = null;

        try {
            DB::beginTransaction();

            $patient = null;

            if (Auth::check()) {
                $user = Auth::user();
                $patient = $user->patient;
                if (! $patient) {
                    $patient = Patient::create([
                        'user_id' => $user->id,
                        'first_name' => $this->firstName ?: $user->name,
                        'last_name' => $this->lastName ?: '',
                        'identification_type' => IdentificationType::from($this->identificationType),
                        'identification_number' => $this->identificationNumber ?: 'TEMP-' . uniqid(),
                        'phone' => $this->phone ?: '0000000000',
                        'birth_date' => $this->birthDate ?: '1990-01-01',
                        'gender' => Gender::from($this->gender),
                    ]);
                }
            } else {
                // Register or find user
                $user = User::where('email', $this->email)->first();
                if ($user) {
                    $patient = $user->patient;
                } else {
                    $user = User::create([
                        'name' => "{$this->firstName} {$this->lastName}",
                        'email' => $this->email,
                        'password' => Hash::make($this->password),
                        'is_active' => true,
                    ]);
                    $user->assignRole('PATIENT');

                    $patient = Patient::create([
                        'user_id' => $user->id,
                        'first_name' => $this->firstName,
                        'last_name' => $this->lastName,
                        'identification_type' => IdentificationType::from($this->identificationType),
                        'identification_number' => $this->identificationNumber,
                        'phone' => $this->phone,
                        'birth_date' => $this->birthDate,
                        'gender' => Gender::from($this->gender),
                    ]);
                }

                Auth::login($user);
            }

            $doctor = Doctor::findOrFail($this->doctorId);
            $specialty = Specialty::findOrFail($this->specialtyId);
            $procedure = $this->procedureId ? Procedure::find($this->procedureId) : null;

            $startAt = Carbon::parse("{$this->selectedDate} {$this->selectedTime}");
            $duration = $procedure ? $procedure->duration_minutes : $doctor->consultation_duration_minutes;
            $endAt = (clone $startAt)->addMinutes($duration);

            $paymentMethod = PaymentMethod::from($this->paymentMethod);

            $appointment = $bookingService->book(
                patient: $patient,
                doctor: $doctor,
                specialty: $specialty,
                startAt: $startAt,
                endAt: $endAt,
                procedure: $procedure,
                reason: $this->reason ?: null,
                paymentMethod: $paymentMethod,
                bookedByUserId: Auth::id()
            );

            DB::commit();

            $this->confirmedAppointmentId = $appointment->id;
            $this->confirmedAppointmentCode = $appointment->code;
            $this->step = 7;

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
            $procedure = $this->procedureId ? Procedure::find($this->procedureId) : null;
            $date = Carbon::parse($this->selectedDate);

            if ($doctor) {
                $availableSlots = $availabilityService->getAvailableSlotsForDate($doctor, $date, $procedure);
            }
        }

        $selectedSpecialty = $this->specialtyId ? Specialty::find($this->specialtyId) : null;
        $selectedProcedure = $this->procedureId ? Procedure::find($this->procedureId) : null;
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
