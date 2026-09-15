@extends('layouts.app')

@section('title', 'Registro de Paciente — ' . config('branding.app_name'))

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50/50">
    <div class="max-w-xl w-full space-y-6 bg-white p-8 sm:p-10 rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100">
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-block mb-4">
                <x-brand.logo height="38" />
            </a>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Crea tu Cuenta de Paciente</h2>
            <p class="mt-1 text-xs text-slate-500">Accede a tus citas, historial médico y agendamiento 24/7</p>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium space-y-1">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form class="space-y-4" action="{{ route('register') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nombres *</label>
                    <input type="text" name="first_name" required value="{{ old('first_name') }}" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary" placeholder="Ej: Carlos" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Apellidos *</label>
                    <input type="text" name="last_name" required value="{{ old('last_name') }}" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary" placeholder="Ej: Perez" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tipo de Identificación *</label>
                    <select name="identification_type" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary">
                        <option value="CC" {{ old('identification_type') === 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía (CC)</option>
                        <option value="TI" {{ old('identification_type') === 'TI' ? 'selected' : '' }}>Tarjeta de Identidad (TI)</option>
                        <option value="CE" {{ old('identification_type') === 'CE' ? 'selected' : '' }}>Cédula de Extranjería (CE)</option>
                        <option value="PASSPORT" {{ old('identification_type') === 'PASSPORT' ? 'selected' : '' }}>Pasaporte</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Número de Documento *</label>
                    <input type="text" name="identification_number" required value="{{ old('identification_number') }}" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary" placeholder="1098765432" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Correo Electrónico *</label>
                    <input type="email" name="email" required value="{{ old('email') }}" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary" placeholder="tu@correo.com" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono Móvil *</label>
                    <input type="tel" name="phone" required value="{{ old('phone') }}" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary" placeholder="3001234567" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha de Nacimiento *</label>
                    <input type="date" name="birth_date" required value="{{ old('birth_date') }}" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Género *</label>
                    <select name="gender" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary">
                        <option value="MALE">Masculino</option>
                        <option value="FEMALE">Femenino</option>
                        <option value="OTHER">Otro</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Contraseña (Mínimo 8 caracteres) *</label>
                    <input type="password" name="password" required class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary" placeholder="••••••••" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Confirmar Contraseña *</label>
                    <input type="password" name="password_confirmation" required class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary" placeholder="••••••••" />
                </div>
            </div>

            <button type="submit" class="w-full mt-4 py-3.5 px-4 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-sm shadow-md shadow-brand-primary/25 transition-all">
                Crear Mi Cuenta
            </button>
        </form>

        <div class="pt-4 text-center text-xs text-slate-500">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="font-bold text-brand-primary hover:underline">Inicia sesión</a>
        </div>
    </div>
</div>
@endsection
