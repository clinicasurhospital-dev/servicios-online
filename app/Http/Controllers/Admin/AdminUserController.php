<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use App\Notifications\NewPatientWelcomeNotification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->query('search', ''));
        $roleFilter = $request->query('role');
        $statusFilter = $request->query('status');

        $query = User::query()
            ->with(['roles', 'patient', 'doctor.specialties'])
            ->latest('id');

        // Search Term Filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($pq) use ($search) {
                        $pq->where('identification_number', 'like', "%{$search}%");
                    })
                    ->orWhereHas('doctor', function ($dq) use ($search) {
                        $dq->where('license_number', 'like', "%{$search}%");
                    });
            });
        }

        // Role Filter
        if ($roleFilter) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $roleFilter));
        }

        // Status Filter
        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('is_active', (bool) $statusFilter);
        }

        $users = $query->paginate(15)->withQueryString();

        // Statistics for dashboard counter cards
        $stats = [
            'total' => User::count(),
            'admins' => User::role(['ADMIN', 'SUPER_ADMIN'])->count(),
            'receptionists' => User::role('RECEPTION')->count(),
            'doctors' => User::role('DOCTOR')->count(),
            'specialists' => User::role('SPECIALIST')->count(),
            'patients' => User::role('PATIENT')->count(),
            'active' => User::where('is_active', true)->count(),
        ];

        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();

        return view('admin.users', compact('users', 'stats', 'roles', 'specialties', 'search', 'roleFilter', 'statusFilter'));
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'role' => 'required|string|exists:roles,name',
            'password' => 'nullable|string|min:8',
        ];

        $role = $request->input('role');

        if ($role === 'PATIENT') {
            $rules['identification_type'] = ['required', Rule::enum(IdentificationType::class)];
            $rules['identification_number'] = 'required|string|max:30|unique:patients,identification_number';
            $rules['birth_date'] = 'nullable|date';
            $rules['gender'] = ['nullable', Rule::enum(Gender::class)];
            $rules['address'] = 'nullable|string|max:255';
        }

        if (in_array($role, ['DOCTOR', 'SPECIALIST'])) {
            $rules['license_number'] = 'required|string|max:50|unique:doctors,license_number';
            $rules['specialty_ids'] = 'required|array|min:1';
            $rules['specialty_ids.*'] = 'exists:specialties,id';
            $rules['room_office'] = 'nullable|string|max:100';
            $rules['default_consultation_price'] = 'nullable|numeric|min:0';
            $rules['default_slot_duration_minutes'] = 'required|integer|min:10|max:120';
        }

        $validated = $request->validate($rules, [
            'email.unique' => 'El correo electrónico ya se encuentra registrado en el sistema.',
            'role.required' => 'Debes seleccionar un rol o perfil para el usuario.',
            'identification_number.unique' => 'El número de identificación ya pertenece a otro paciente.',
            'license_number.unique' => 'El registro médico / MSP ya pertenece a otro profesional.',
            'specialty_ids.required' => 'Debes asignar al menos una especialidad al médico/especialista.',
        ]);

        $genericPassword = !empty($validated['password']) ? $validated['password'] : 'SurHospital2026!';

        return DB::transaction(function () use ($validated, $genericPassword, $role, $request) {
            $user = User::create([
                'name' => trim($validated['name']),
                'email' => strtolower(trim($validated['email'])),
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($genericPassword),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $user->assignRole($role);

            $roleLabel = match ($role) {
                'SUPER_ADMIN' => 'Super Administrador',
                'ADMIN' => 'Administrador',
                'RECEPTION' => 'Recepcionista',
                'DOCTOR' => 'Médico',
                'SPECIALIST' => 'Especialista',
                'PATIENT' => 'Paciente',
                default => 'Usuario',
            };

            $extraNotice = "";

            // Register linked Patient Profile
            if ($role === 'PATIENT') {
                $nameParts = explode(' ', $user->name, 2);
                $firstName = $nameParts[0];
                $lastName = $nameParts[1] ?? $nameParts[0];

                Patient::create([
                    'user_id' => $user->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'identification_type' => $validated['identification_type'],
                    'identification_number' => $validated['identification_number'],
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                    'birth_date' => $validated['birth_date'] ?? null,
                    'gender' => $validated['gender'] ?? Gender::OTHER->value,
                    'address' => $validated['address'] ?? null,
                ]);

                // Send welcome email with reset link and generic password
                try {
                    $token = Password::broker()->createToken($user);
                    $user->notify(new NewPatientWelcomeNotification($token, $genericPassword));
                    $extraNotice = " Se envió el correo de bienvenida con la clave genérica '{$genericPassword}' y el enlace seguro para generar su nueva clave.";
                } catch (\Throwable $e) {
                    Log::warning("No se pudo enviar correo de bienvenida al paciente {$user->email}: " . $e->getMessage());
                    $extraNotice = " Clave genérica generada: '{$genericPassword}'. (Aviso: No se pudo conectar al servidor de correo SMTP para el envío automático).";
                }
            }

            // Register linked Doctor Profile
            if (in_array($role, ['DOCTOR', 'SPECIALIST'])) {
                $firstSpecialty = Specialty::find($validated['specialty_ids'][0]);

                $doctor = Doctor::create([
                    'user_id' => $user->id,
                    'license_number' => $validated['license_number'],
                    'primary_specialty_name' => $firstSpecialty?->name,
                    'room_office' => $validated['room_office'] ?? null,
                    'default_consultation_price' => $validated['default_consultation_price'] ?? $firstSpecialty?->base_price ?? 35.00,
                    'default_slot_duration_minutes' => $validated['default_slot_duration_minutes'],
                    'is_active' => true,
                ]);

                $doctor->specialties()->sync($validated['specialty_ids']);

                // Create initial default schedule Mon-Fri 08:00 - 13:00
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

                $extraNotice = " Registrado en usuarios con clave genérica: '{$genericPassword}'.";
            }

            if (!in_array($role, ['PATIENT', 'DOCTOR', 'SPECIALIST'])) {
                $extraNotice = " Clave de acceso asignada: '{$genericPassword}'.";
            }

            return back()->with('success', "Usuario {$user->name} con rol '{$roleLabel}' creado exitosamente.{$extraNotice}");
        });
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:30',
            'role' => 'required|string|exists:roles,name',
            'password' => 'nullable|string|min:8',
            'is_active' => 'nullable|boolean',
        ], [
            'email.unique' => 'El correo electrónico ya pertenece a otro usuario.',
            'role.required' => 'Debes asignar un rol.',
        ]);

        return DB::transaction(function () use ($user, $validated, $request) {
            $updateData = [
                'name' => trim($validated['name']),
                'email' => strtolower(trim($validated['email'])),
                'phone' => $validated['phone'] ?? null,
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            if ($request->has('is_active')) {
                $updateData['is_active'] = (bool) $request->input('is_active');
            }

            $user->update($updateData);

            // Sync role
            $currentRole = $user->roles->first()?->name;
            $newRole = $validated['role'];

            if ($currentRole !== $newRole) {
                // If demoting the only super admin, protect
                if ($currentRole === 'SUPER_ADMIN' && User::role('SUPER_ADMIN')->count() <= 1 && $newRole !== 'SUPER_ADMIN') {
                    abort(403, 'No puedes cambiar el rol del único Super Administrador del sistema.');
                }

                $user->syncRoles([$newRole]);
            }

            // Sync patient profile name/email/phone if exists
            if ($user->patient) {
                $nameParts = explode(' ', $user->name, 2);
                $user->patient->update([
                    'first_name' => $nameParts[0],
                    'last_name' => $nameParts[1] ?? $nameParts[0],
                    'email' => $user->email,
                    'phone' => $user->phone ?? $user->patient->phone,
                ]);
            }

            // Sync doctor status if exists
            if ($user->doctor && array_key_exists('is_active', $updateData)) {
                $user->doctor->update(['is_active' => $updateData['is_active']]);
            }

            return back()->with('success', "Usuario {$user->name} actualizado exitosamente.");
        });
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta mientras estés en sesión.');
        }

        $newStatus = !$user->is_active;
        $user->update(['is_active' => $newStatus]);

        if ($user->doctor) {
            $user->doctor->update(['is_active' => $newStatus]);
        }

        $statusText = $newStatus ? 'activado' : 'desactivado';
        return back()->with('success', "Usuario {$user->name} {$statusText} correctamente.");
    }

    public function sendPasswordReset(User $user): RedirectResponse
    {
        try {
            $token = Password::broker()->createToken($user);
            $user->notify(new ResetPasswordNotification($token));

            return back()->with('success', "Enlace de restablecimiento de contraseña enviado exitosamente al correo {$user->email}.");
        } catch (\Throwable $e) {
            Log::error("Error al enviar enlace de contraseña a {$user->email}: " . $e->getMessage());

            return back()->with('error', "No se pudo enviar el correo de restablecimiento: {$e->getMessage()}");
        }
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta de usuario.');
        }

        if ($user->isSuperAdmin() && User::role('SUPER_ADMIN')->count() <= 1) {
            return back()->with('error', 'No se puede eliminar el único Super Administrador del sistema.');
        }

        // Check if user has associated medical appointments or clinical records
        $hasAppointments = false;
        if ($user->patient && $user->patient->appointments()->exists()) {
            $hasAppointments = true;
        }
        if ($user->doctor && $user->doctor->appointments()->exists()) {
            $hasAppointments = true;
        }

        if ($hasAppointments) {
            $user->update(['is_active' => false]);
            if ($user->doctor) {
                $user->doctor->update(['is_active' => false]);
            }

            return back()->with('warning', "El usuario {$user->name} tiene citas asociadas en el historial. Por integridad médica ha sido desactivado en lugar de eliminarse.");
        }

        return DB::transaction(function () use ($user) {
            $name = $user->name;

            if ($user->doctor) {
                $user->doctor->schedules()->delete();
                $user->doctor->specialties()->detach();
                $user->doctor->procedures()->detach();
                $user->doctor->delete();
            }

            if ($user->patient) {
                $user->patient->delete();
            }

            $user->delete();

            return back()->with('success', "Usuario {$name} eliminado exitosamente del sistema.");
        });
    }
}
