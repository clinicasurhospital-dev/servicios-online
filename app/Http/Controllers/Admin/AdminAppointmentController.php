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
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        $query = Appointment::with(['patient', 'doctor.user', 'specialty', 'procedure', 'payments', 'review']);

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
        $user = Auth::user();
        if ($user->isDoctor() && $user->doctor && $appointment->doctor_id !== $user->doctor->id) {
            abort(403, 'No tienes permiso para gestionar citas de otros médicos.');
        }

        // Citas atendidas o canceladas no pueden cambiar de estado
        if (in_array($appointment->status->value, ['COMPLETED', 'CANCELLED'])) {
            return back()->with('error', "La cita #{$appointment->code} ya se encuentra {$appointment->status->label()} y su estado no puede ser modificado.");
        }

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

        if ($newStatus === AppointmentStatus::COMPLETED) {
            app(\App\Domain\Doctors\Services\DoctorReviewService::class)->getOrCreateReviewForAppointment($appointment);
        }

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

    public function uploadClinicalRecord(Request $request, Appointment $appointment): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isDoctor() && $user->doctor && $appointment->doctor_id !== $user->doctor->id) {
            abort(403, 'No tienes permiso para adjuntar documentos a citas de otros médicos.');
        }

        // Si la cita está cancelada, no permitir adjuntar historia clínica
        if ($appointment->status->value === 'CANCELLED') {
            return back()->with('error', 'No se pueden registrar ni adjuntar documentos clínicos a una cita que ha sido cancelada.');
        }

        $validated = $request->validate([
            'clinical_history' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'procedure_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
            'clinical_notes' => 'nullable|string|max:3000',
            'status' => 'nullable|string|in:CONFIRMED,COMPLETED,CANCELLED,NO_SHOW',
        ], [
            'clinical_history.mimes' => 'La historia clínica debe ser un archivo PDF, Imagen o Word.',
            'clinical_history.max' => 'El archivo de historia clínica no debe superar los 10MB.',
            'procedure_document.mimes' => 'El documento de procedimiento debe ser un archivo PDF, Imagen o Word.',
            'procedure_document.max' => 'El archivo de procedimiento no debe superar los 10MB.',
        ]);

        $beforeState = $appointment->toArray();
        $updates = [];

        if ($request->hasFile('clinical_history')) {
            if ($appointment->clinical_history_path && Storage::disk('medical_records')->exists($appointment->clinical_history_path)) {
                Storage::disk('medical_records')->delete($appointment->clinical_history_path);
            }
            $file = $request->file('clinical_history');
            $path = str_replace('\\', '/', Storage::disk('medical_records')->putFile('historia_clinica', $file));
            $updates['clinical_history_path'] = $path;
            $updates['clinical_history_filename'] = $file->getClientOriginalName();
            $updates['clinical_history_uploaded_at'] = now();
        }

        if ($request->hasFile('procedure_document')) {
            if ($appointment->procedure_document_path && Storage::disk('medical_records')->exists($appointment->procedure_document_path)) {
                Storage::disk('medical_records')->delete($appointment->procedure_document_path);
            }
            $file = $request->file('procedure_document');
            $path = str_replace('\\', '/', Storage::disk('medical_records')->putFile('procedimientos', $file));
            $updates['procedure_document_path'] = $path;
            $updates['procedure_document_filename'] = $file->getClientOriginalName();
            $updates['clinical_history_uploaded_at'] = now();
        }

        if ($request->filled('clinical_notes')) {
            $updates['clinical_notes'] = $validated['clinical_notes'];
        }

        // Si ya está atendida (COMPLETED), mantener su estado atendido
        if ($appointment->status->value === 'COMPLETED') {
            $updates['status'] = AppointmentStatus::COMPLETED;
        } elseif (!empty($validated['status']) && $validated['status'] !== $appointment->status->value) {
            $previousStatus = $appointment->status->value;
            $newStatus = AppointmentStatus::from($validated['status']);
            $updates['status'] = $newStatus;

            AppointmentStatusHistory::create([
                'appointment_id' => $appointment->id,
                'previous_status' => $previousStatus,
                'new_status' => $newStatus->value,
                'changed_by_user_id' => Auth::id(),
                'reason' => 'Actualización con registro clínico / procedimiento',
                'created_at' => now(),
            ]);
        }

        if (!empty($updates)) {
            $appointment->update($updates);

            if ($appointment->fresh()->status === AppointmentStatus::COMPLETED) {
                app(\App\Domain\Doctors\Services\DoctorReviewService::class)->getOrCreateReviewForAppointment($appointment);
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'ClinicalRecordUploaded',
                'auditable_type' => Appointment::class,
                'auditable_id' => (string) $appointment->id,
                'before_state' => $beforeState,
                'after_state' => $appointment->fresh()->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        }

        return back()->with('success', "Historia clínica / documento de procedimiento adjuntado correctamente a la cita #{$appointment->code}.");
    }

    public function downloadClinicalRecord(Appointment $appointment, string $type): StreamedResponse|RedirectResponse
    {
        $user = Auth::user();
        if ($user->isDoctor() && $user->doctor && $appointment->doctor_id !== $user->doctor->id) {
            abort(403, 'No tienes acceso a los documentos de citas pertenecientes a otros especialistas.');
        }

        $path = $type === 'procedimiento' ? $appointment->procedure_document_path : $appointment->clinical_history_path;
        $name = $type === 'procedimiento' ? ($appointment->procedure_document_filename ?: "procedimiento_{$appointment->code}.pdf") : ($appointment->clinical_history_filename ?: "historia_clinica_{$appointment->code}.pdf");

        if (! $path || ! Storage::disk('medical_records')->exists($path)) {
            return back()->with('error', 'El documento clínico solicitado no fue encontrado en el almacenamiento seguro.');
        }

        return Storage::disk('medical_records')->download($path, $name);
    }

    public function deleteClinicalRecord(Appointment $appointment, string $type): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isDoctor() && $user->doctor && $appointment->doctor_id !== $user->doctor->id) {
            abort(403, 'No tienes permiso para eliminar documentos de esta cita.');
        }

        if ($type === 'procedimiento') {
            if ($appointment->procedure_document_path && Storage::disk('medical_records')->exists($appointment->procedure_document_path)) {
                Storage::disk('medical_records')->delete($appointment->procedure_document_path);
            }
            $appointment->update([
                'procedure_document_path' => null,
                'procedure_document_filename' => null,
            ]);
            $label = 'Documento de procedimiento';
        } else {
            if ($appointment->clinical_history_path && Storage::disk('medical_records')->exists($appointment->clinical_history_path)) {
                Storage::disk('medical_records')->delete($appointment->clinical_history_path);
            }
            $appointment->update([
                'clinical_history_path' => null,
                'clinical_history_filename' => null,
            ]);
            $label = 'Historia clínica';
        }

        return back()->with('success', "{$label} eliminada de la cita #{$appointment->code}.");
    }
}
