<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Tu cuenta ha sido desactivada. Por favor contacta a soporte.']);
            }

            if ($user->isAdmin() || $user->isReception() || $user->isDoctor()) {
                return redirect('/admin');
            }

            return redirect('/paciente');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request, \App\Domain\Patients\Services\EcuadorianIdService $idService): RedirectResponse
    {
        $validated = $request->validate([
            'identification_number' => ['required', 'string', 'digits:10'],
            'first_name' => ['required', 'string', 'min:2', 'max:100'],
            'last_name' => ['required', 'string', 'min:2', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'in:MALE,FEMALE,OTHER'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'min:7', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'data_consent' => ['required', 'accepted'],
        ], [
            'identification_number.required' => 'El número de cédula es obligatorio.',
            'identification_number.digits' => 'La cédula debe contener exactamente 10 dígitos numéricos.',
            'first_name.required' => 'Los nombres son obligatorios.',
            'last_name.required' => 'Los apellidos son obligatorios.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Este correo electrónico ya se encuentra registrado.',
            'phone.required' => 'El número de teléfono móvil es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'data_consent.required' => 'Debes aceptar la Política de Protección de Datos Personales.',
            'data_consent.accepted' => 'Debes aceptar la Política de Protección de Datos Personales para continuar.',
        ]);

        // Validate Ecuadorian Cedula algorithm
        $cedulaCheck = $idService->validateCedula($validated['identification_number']);
        if (!$cedulaCheck['is_valid']) {
            return back()->withErrors([
                'identification_number' => $cedulaCheck['error'],
            ])->withInput();
        }

        // Check if cedula already has an associated user account
        $existingPatient = Patient::where('identification_number', $validated['identification_number'])->first();
        if ($existingPatient && $existingPatient->user_id) {
            return back()->withErrors([
                'identification_number' => 'Ya existe una cuenta registrada con este número de cédula. Por favor inicia sesión.',
            ])->withInput();
        }

        $user = DB::transaction(function () use ($validated, $existingPatient, $request) {
            $user = User::create([
                'name' => trim("{$validated['first_name']} {$validated['last_name']}"),
                'email' => strtolower(trim($validated['email'])),
                'phone' => trim($validated['phone']),
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $user->assignRole('PATIENT');

            if ($existingPatient) {
                $existingPatient->update([
                    'user_id' => $user->id,
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => strtolower(trim($validated['email'])),
                    'phone' => trim($validated['phone']),
                    'birth_date' => $validated['birth_date'] ?? $existingPatient->birth_date,
                    'gender' => $validated['gender'] ?? ($existingPatient->gender?->value ?? 'OTHER'),
                    'data_consent_accepted' => true,
                    'data_consent_accepted_at' => now(),
                    'data_consent_ip' => $request->ip(),
                    'data_consent_user_agent' => $request->userAgent(),
                ]);
            } else {
                Patient::create([
                    'user_id' => $user->id,
                    'identification_type' => 'CEDULA',
                    'identification_number' => $validated['identification_number'],
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => strtolower(trim($validated['email'])),
                    'phone' => trim($validated['phone']),
                    'birth_date' => $validated['birth_date'] ?? null,
                    'gender' => $validated['gender'] ?? 'OTHER',
                    'data_consent_accepted' => true,
                    'data_consent_accepted_at' => now(),
                    'data_consent_ip' => $request->ip(),
                    'data_consent_user_agent' => $request->userAgent(),
                ]);
            }

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/paciente')->with('success', '¡Bienvenido a SurHospital! Tu cuenta de paciente ha sido creada exitosamente.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('info', 'Has cerrado sesión exitosamente.');
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Por favor ingresa tu correo electrónico.',
            'email.email' => 'Ingresa un formato de correo válido.',
        ]);

        $email = strtolower(trim($request->input('email')));

        try {
            $user = User::where('email', $email)->first();

            // Si el usuario no existe en 'users' pero está registrado en 'patients' (ej: por agendamiento)
            if (!$user) {
                $patient = Patient::where('email', $email)->first();
                if ($patient) {
                    $user = DB::transaction(function () use ($patient, $email) {
                        $newUser = User::create([
                            'name' => trim("{$patient->first_name} {$patient->last_name}"),
                            'email' => $email,
                            'phone' => $patient->phone,
                            'password' => Hash::make(\Illuminate\Support\Str::random(32)),
                            'email_verified_at' => now(),
                            'is_active' => true,
                        ]);

                        $newUser->assignRole('PATIENT');

                        $patient->update(['user_id' => $newUser->id]);

                        return $newUser;
                    });
                }
            }

            if (!$user) {
                return back()->withErrors([
                    'email' => 'No pudimos encontrar una cuenta registrada con esa dirección de correo.',
                ])->onlyInput('email');
            }

            $status = \Illuminate\Support\Facades\Password::sendResetLink(
                ['email' => $email]
            );

            if ($status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
                return back()->with('status', 'Hemos enviado un enlace para restablecer tu contraseña a tu correo electrónico. Revisa tu bandeja de entrada o carpeta de spam.');
            }

            return back()->withErrors([
                'email' => 'No pudimos procesar la solicitud para esta cuenta. Por favor contacta a soporte.',
            ])->onlyInput('email');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error al enviar correo de restablecimiento: ' . $e->getMessage(), [
                'email' => $email,
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = config('app.debug')
                ? 'Error al enviar correo (SMTP): ' . $e->getMessage()
                : 'Ocurrió un inconveniente al enviar el correo. Por favor intenta nuevamente o contacta a soporte.';

            return back()->withErrors([
                'email' => $errorMessage,
            ])->onlyInput('email');
        }
    }

    public function showResetPassword(Request $request, ?string $token = null): View
    {
        return view('auth.reset-password', [
            'token' => $token ?? $request->route('token'),
            'email' => strtolower(trim($request->query('email', old('email', '')))),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'token.required' => 'El token de seguridad es requerido.',
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El correo ingresado no es válido.',
            'password.required' => 'Debes ingresar una nueva contraseña.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $email = strtolower(trim($request->input('email')));

        $credentials = [
            'token' => $request->input('token'),
            'email' => $email,
            'password' => $request->input('password'),
            'password_confirmation' => $request->input('password_confirmation'),
        ];

        $status = \Illuminate\Support\Facades\Password::reset(
            $credentials,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => \Illuminate\Support\Str::random(60),
                ])->save();

                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );

        if ($status === \Illuminate\Support\Facades\Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', '¡Tu contraseña ha sido restablecida con éxito! Ya puedes iniciar sesión con tu nueva contraseña.');
        }

        return back()->withErrors([
            'email' => 'El enlace de recuperación es inválido o ha caducado. Por favor solicita uno nuevo.',
        ])->onlyInput('email');
    }
}
