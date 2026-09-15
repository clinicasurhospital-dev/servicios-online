<?php

namespace App\Http\Controllers\Patient;

use App\Domain\Appointments\Services\AppointmentCancellationService;
use App\Domain\Payments\Services\BankTransferService;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\BankTransfer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PatientPortalController extends Controller
{
    public function __construct(
        protected AppointmentCancellationService $cancellationService,
        protected BankTransferService $transferService
    ) {}

    public function dashboard(): View
    {
        $user = Auth::user();
        $patient = $user->patient;

        if (!$patient) {
            abort(403, 'Perfil de paciente no asociado.');
        }

        $nextAppointment = Appointment::where('patient_id', $patient->id)
            ->where('start_at', '>=', now())
            ->whereIn('status', ['PENDING', 'PENDING_PAYMENT', 'CONFIRMED'])
            ->with(['doctor.user', 'specialty', 'procedure', 'payments'])
            ->orderBy('start_at', 'asc')
            ->first();

        $recentAppointments = Appointment::where('patient_id', $patient->id)
            ->with(['doctor.user', 'specialty', 'procedure', 'payments'])
            ->orderBy('start_at', 'desc')
            ->take(5)
            ->get();

        return view('patient.dashboard', compact('patient', 'nextAppointment', 'recentAppointments'));
    }

    public function appointments(): View
    {
        $patient = Auth::user()->patient;
        if (!$patient) {
            abort(403);
        }

        $appointments = Appointment::where('patient_id', $patient->id)
            ->with(['doctor.user', 'specialty', 'procedure', 'payments'])
            ->orderBy('start_at', 'desc')
            ->paginate(10);

        return view('patient.appointments', compact('patient', 'appointments'));
    }

    public function showAppointment(Appointment $appointment): View
    {
        $this->authorize('view', $appointment);

        $appointment->load(['doctor.user', 'specialty', 'procedure', 'payments.bankTransfer', 'histories.user']);

        return view('patient.appointment-show', compact('appointment'));
    }

    public function cancelAppointment(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('cancel', $appointment);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $this->cancellationService->cancel($appointment, $validated['reason'], Auth::id());

        return back()->with('success', 'La cita ha sido cancelada correctamente.');
    }

    public function submitTransfer(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('view', $appointment);

        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'reference_number' => ['required', 'string', 'max:50'],
            'transfer_date' => ['required', 'date'],
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'], // 5MB max
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->transferService->submitTransfer(
            appointment: $appointment,
            data: $validated,
            receiptFile: $request->file('receipt')
        );

        return back()->with('success', 'Comprobante de transferencia enviado. Nuestro equipo de recepción lo validará a la brevedad.');
    }

    public function paymentSuccess(): View
    {
        return view('patient.payment-success');
    }
}
