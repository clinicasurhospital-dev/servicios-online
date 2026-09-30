<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminDoctorController extends Controller
{
    public function index(Request $request): View
    {
        $user = auth()->user();
        if ($user->isDoctor()) {
            abort(403, 'El perfil médico no tiene acceso al módulo de administración de médicos y horarios.');
        }

        $search = $request->query('search');
        $specialtyId = $request->query('specialty_id');

        $query = Doctor::query()
            ->with(['user', 'specialties', 'schedules'])
            ->withCount('appointments');

        $query->when($search, function ($query, $term) {
            $query->whereHas('user', function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%");
            })->orWhere('license_number', 'like', "%{$term}%");
        })
        ->when($specialtyId, function ($query, $id) {
            $query->whereHas('specialties', fn ($q) => $q->where('specialties.id', $id));
        });

        $doctors = $query->orderBy('is_active', 'desc')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();

        return view('admin.doctors', compact('doctors', 'specialties', 'search', 'specialtyId'));
    }

    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->isDoctor()) {
            abort(403, 'No tienes autorización para registrar nuevos médicos.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:8',
            'license_number' => 'required|string|max:50|unique:doctors,license_number',
            'room_office' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'default_consultation_price' => 'nullable|numeric|min:0',
            'default_slot_duration_minutes' => 'required|integer|min:10|max:120',
            'specialty_ids' => 'required|array|min:1',
            'specialty_ids.*' => 'exists:specialties,id',
        ], [
            'email.unique' => 'El correo electrónico ya está registrado para otro usuario.',
            'license_number.unique' => 'El número de registro médico / MSP ya se encuentra registrado.',
            'specialty_ids.required' => 'Debes asignar al menos una especialidad al médico.',
        ]);

        return DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password'] ?: 'password123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $user->assignRole('DOCTOR');

            $firstSpecialty = Specialty::find($validated['specialty_ids'][0]);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'license_number' => $validated['license_number'],
                'primary_specialty_name' => $firstSpecialty?->name,
                'room_office' => $validated['room_office'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'default_consultation_price' => $validated['default_consultation_price'] ?? $firstSpecialty?->base_price ?? 35.00,
                'default_slot_duration_minutes' => $validated['default_slot_duration_minutes'],
                'is_active' => true,
            ]);

            $doctor->specialties()->sync($validated['specialty_ids']);

            // Create default schedule Mon-Fri 08:00 - 13:00
            for ($day = 1; $day <= 5; $day++) {
                DoctorSchedule::create([
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day,
                    'start_time' => '08:00:00',
                    'end_time' => '13:00:00',
                    'slot_duration_minutes' => $validated['default_slot_duration_minutes'],
                    'is_active' => true,
                ]);
            }

            return back()->with('success', "Médico Dr(a). {$user->name} registrado exitosamente con horario inicial de Lun a Vie.");
        });
    }

    public function update(Request $request, Doctor $doctor): RedirectResponse
    {
        $currentUser = auth()->user();
        $isDoctor = $currentUser->isDoctor();

        if ($isDoctor && $doctor->user_id !== $currentUser->id) {
            abort(403, 'No tienes permiso para modificar la información de otros especialistas.');
        }

        $rules = [
            'name' => 'required|string|max:150',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($doctor->user_id)],
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:8',
            'license_number' => ['required', 'string', 'max:50', Rule::unique('doctors', 'license_number')->ignore($doctor->id)],
            'room_office' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'default_slot_duration_minutes' => 'required|integer|min:10|max:120',
            'specialty_ids' => 'required|array|min:1',
            'specialty_ids.*' => 'exists:specialties,id',
        ];

        if (!$isDoctor) {
            $rules['default_consultation_price'] = 'nullable|numeric|min:0';
        }

        $validated = $request->validate($rules, [
            'email.unique' => 'El correo electrónico ya está en uso por otro usuario.',
            'license_number.unique' => 'El registro médico ya pertenece a otro especialista.',
            'specialty_ids.required' => 'Debes asignar al menos una especialidad.',
        ]);

        return DB::transaction(function () use ($doctor, $validated, $isDoctor) {
            $userUpdateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ];

            if (!empty($validated['password'])) {
                $userUpdateData['password'] = Hash::make($validated['password']);
            }

            $doctor->user->update($userUpdateData);

            $firstSpecialty = Specialty::find($validated['specialty_ids'][0]);

            $doctorUpdateData = [
                'license_number' => $validated['license_number'],
                'primary_specialty_name' => $firstSpecialty?->name,
                'room_office' => $validated['room_office'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'default_slot_duration_minutes' => $validated['default_slot_duration_minutes'],
            ];

            if (!$isDoctor && array_key_exists('default_consultation_price', $validated)) {
                $doctorUpdateData['default_consultation_price'] = $validated['default_consultation_price'] ?? $doctor->default_consultation_price;
            }

            $doctor->update($doctorUpdateData);

            $doctor->specialties()->sync($validated['specialty_ids']);

            return back()->with('success', "Datos del Dr(a). {$doctor->user->name} actualizados exitosamente.");
        });
    }

    public function toggleActive(Doctor $doctor): RedirectResponse
    {
        if (auth()->user()->isDoctor()) {
            abort(403, 'Acción no autorizada.');
        }

        $newStatus = !$doctor->is_active;
        $doctor->update(['is_active' => $newStatus]);
        $doctor->user->update(['is_active' => $newStatus]);

        $statusText = $newStatus ? 'activado' : 'desactivado';
        return back()->with('success', "Médico Dr(a). {$doctor->user->name} {$statusText} correctamente.");
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        if (auth()->user()->isDoctor()) {
            abort(403, 'Acción no autorizada.');
        }

        if ($doctor->appointments()->exists()) {
            $doctor->update(['is_active' => false]);
            $doctor->user->update(['is_active' => false]);
            return back()->with('warning', "El Dr(a). {$doctor->user->name} tiene citas asociadas. Ha sido desactivado en lugar de eliminarse.");
        }

        return DB::transaction(function () use ($doctor) {
            $doctorName = $doctor->user->name;
            $doctor->schedules()->delete();
            $doctor->specialties()->detach();
            $doctor->procedures()->detach();
            $user = $doctor->user;
            $doctor->delete();
            $user->delete();

            return back()->with('success', "Médico Dr(a). {$doctorName} eliminado correctamente del sistema.");
        });
    }

    public function updateSchedules(Request $request, Doctor $doctor): RedirectResponse
    {
        $currentUser = auth()->user();
        if ($currentUser->isDoctor() && $doctor->user_id !== $currentUser->id) {
            abort(403, 'No tienes permiso para configurar los horarios de otros médicos.');
        }

        $validated = $request->validate([
            'schedules' => 'nullable|array',
            'schedules.*.day_of_week' => 'required|integer|between:0,6',
            'schedules.*.start_time' => 'required|string',
            'schedules.*.end_time' => 'required|string',
            'schedules.*.slot_duration_minutes' => 'nullable|integer|min:10|max:120',
            'schedules.*.enabled' => 'nullable',
            'schedules.*.is_active' => 'nullable',
        ]);

        return DB::transaction(function () use ($doctor, $validated) {
            // Delete existing schedules and re-create all configured active intervals
            $doctor->schedules()->delete();

            if (!empty($validated['schedules'])) {
                foreach ($validated['schedules'] as $sch) {
                    $enabled = !empty($sch['enabled']) || !empty($sch['is_active']);
                    $startTime = trim($sch['start_time'] ?? '');
                    $endTime = trim($sch['end_time'] ?? '');

                    if ($enabled && $startTime && $endTime && $startTime < $endTime) {
                        DoctorSchedule::create([
                            'doctor_id' => $doctor->id,
                            'day_of_week' => (int) $sch['day_of_week'],
                            'start_time' => strlen($startTime) === 5 ? "{$startTime}:00" : $startTime,
                            'end_time' => strlen($endTime) === 5 ? "{$endTime}:00" : $endTime,
                            'slot_duration_minutes' => $sch['slot_duration_minutes'] ?? $doctor->default_slot_duration_minutes ?? 30,
                            'is_active' => true,
                        ]);
                    }
                }
            }

            return back()->with('success', "Horarios semanales del Dr(a). {$doctor->user->name} actualizados correctamente.");
        });
    }
}
