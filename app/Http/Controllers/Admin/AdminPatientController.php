<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Patients\Enums\Gender;
use App\Domain\Patients\Enums\IdentificationType;
use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPatientController extends Controller
{
    protected function ensureNotDoctor(): void
    {
        if (auth()->check() && auth()->user()->isDoctor()) {
            abort(403, 'El perfil médico no tiene acceso a la administración ni registro directo de pacientes. El paciente debe ser registrado previamente por recepción.');
        }
    }

    public function index(Request $request): View
    {
        $this->ensureNotDoctor();
        $search = $request->query('search');

        $patients = Patient::query()
            ->with(['user'])
            ->withCount('appointments')
            ->when($search, function ($query, $term) {
                $query->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('identification_number', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhereHas('user', fn ($q) => $q->where('email', 'like', "%{$term}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.patients', compact('patients', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureNotDoctor();
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'identification_type' => ['required', Rule::enum(IdentificationType::class)],
            'identification_number' => 'required|string|max:30|unique:patients,identification_number',
            'email' => 'required|email|max:150|unique:users,email',
            'phone' => 'required|string|max:30',
            'birth_date' => 'nullable|date',
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'address' => 'nullable|string|max:255',
            'medical_notes' => 'nullable|string|max:1000',
        ], [
            'identification_number.unique' => 'El número de identificación ya está registrado en el sistema.',
            'email.unique' => 'El correo electrónico ya pertenece a otro usuario.',
            'phone.required' => 'El número de teléfono móvil es obligatorio.',
        ]);

        return DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $user->assignRole('PATIENT');

            Patient::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'identification_type' => $validated['identification_type'],
                'identification_number' => $validated['identification_number'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'birth_date' => $validated['birth_date'] ?? null,
                'gender' => $validated['gender'] ?? Gender::OTHER->value,
                'address' => $validated['address'] ?? null,
                'medical_notes' => $validated['medical_notes'] ?? null,
            ]);

            return back()->with('success', "Paciente {$user->name} registrado exitosamente.");
        });
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $this->ensureNotDoctor();
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'identification_type' => ['required', Rule::enum(IdentificationType::class)],
            'identification_number' => ['required', 'string', 'max:30', Rule::unique('patients', 'identification_number')->ignore($patient->id)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($patient->user_id)],
            'phone' => 'required|string|max:30',
            'birth_date' => 'nullable|date',
            'gender' => ['nullable', Rule::enum(Gender::class)],
            'address' => 'nullable|string|max:255',
            'medical_notes' => 'nullable|string|max:1000',
        ], [
            'identification_number.unique' => 'El número de identificación ya está registrado para otro paciente.',
            'email.unique' => 'El correo electrónico ya pertenece a otro usuario.',
        ]);

        return DB::transaction(function () use ($patient, $validated) {
            $fullName = trim($validated['first_name'] . ' ' . $validated['last_name']);

            if ($patient->user) {
                $patient->user->update([
                    'name' => $fullName,
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                ]);
            }

            $patient->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'identification_type' => $validated['identification_type'],
                'identification_number' => $validated['identification_number'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'birth_date' => $validated['birth_date'] ?? null,
                'gender' => $validated['gender'] ?? Gender::OTHER->value,
                'address' => $validated['address'] ?? null,
                'medical_notes' => $validated['medical_notes'] ?? null,
            ]);

            return back()->with('success', "Datos del paciente {$fullName} actualizados correctamente.");
        });
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $this->ensureNotDoctor();
        if ($patient->appointments()->exists()) {
            return back()->with('warning', "No se puede eliminar al paciente {$patient->full_name} porque tiene citas registradas en el historial.");
        }

        return DB::transaction(function () use ($patient) {
            $name = $patient->full_name;
            $user = $patient->user;
            $patient->delete();
            if ($user) {
                $user->delete();
            }

            return back()->with('success', "Paciente {$name} eliminado exitosamente.");
        });
    }
}
