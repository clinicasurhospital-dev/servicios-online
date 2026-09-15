<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Appointments\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentStatusHistory;
use App\Models\AuditLog;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $status = $request->input('status');
        $doctorId = $request->input('doctor_id');
        $specialtyId = $request->input('specialty_id');
        $date = $request->input('date');
        $search = $request->input('search');

        $query = Appointment::with(['patient', 'doctor.user', 'specialty', 'procedure', 'payments']);

        if ($user->isDoctor() && $user->doctor) {
            $query->where('doctor_id', $user->doctor->id);
        } elseif ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($specialtyId) {
            $query->where('specialty_id', $specialtyId);
        }

        if ($date) {
            $query->whereDate('start_at', $date);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($pq) use ($search) {
                      $pq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('identification_number', 'like', "%{$search}%");
                  });
            });
        }

        $appointments = $query->orderBy('start_at', 'desc')->paginate(15);
        $doctors = Doctor::where('is_active', true)->with('user')->get();
        $specialties = Specialty::where('is_active', true)->get();

        return view('admin.appointments', compact('appointments', 'doctors', 'specialties'));
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:CONFIRMED,COMPLETED,CANCELLED,NO_SHOW'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $beforeState = $appointment->toArray();
        $previousStatus = $appointment->status->value;
        $newStatus = AppointmentStatus::from($validated['status']);

        $appointment->update([
            'status' => $newStatus,
            'clinical_notes' => $request->input('clinical_notes', $appointment->clinical_notes),
            'cancelled_reason' => $newStatus === AppointmentStatus::CANCELLED ? ($validated['reason'] ?? 'Cancelado por recepción') : null,
            'cancelled_at' => $newStatus === AppointmentStatus::CANCELLED ? now() : null,
            'cancelled_by' => $newStatus === AppointmentStatus::CANCELLED ? Auth::id() : null,
        ]);

        AppointmentStatusHistory::create([
            'appointment_id' => $appointment->id,
            'previous_status' => $previousStatus,
            'new_status' => $newStatus->value,
            'changed_by_user_id' => Auth::id(),
            'reason' => $validated['reason'] ?? "Cambio de estado a {$newStatus->label()}",
            'created_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'AppointmentStatusChanged',
            'auditable_type' => Appointment::class,
            'auditable_id' => (string) $appointment->id,
            'before_state' => $beforeState,
            'after_state' => $appointment->fresh()->toArray(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', "Estado de la cita {$appointment->code} actualizado a {$newStatus->label()}.");
    }
}
