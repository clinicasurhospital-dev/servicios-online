@extends('layouts.app')

@section('title', 'Recuperar Contraseña — ' . config('branding.app_name', 'SurHospital'))

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50/50">
    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100">
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-block mb-4">
                <x-brand.logo />
            </a>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#0700FF] flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Recuperar Contraseña</h2>
            <p class="mt-2 text-xs text-slate-500 max-w-sm mx-auto">
                Ingresa el correo electrónico asociado a tu cuenta y te enviaremos las instrucciones para restablecer tu clave de acceso.
            </p>
        </div>

        @if(session('status'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <span class="font-bold block text-emerald-900 mb-0.5">¡Correo Enviado!</span>
                    {{ session('status') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
                @foreach($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <form class="mt-6 space-y-5" action="{{ route('password.email') }}" method="POST">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Correo Electrónico Registrado</label>
                <div class="relative">
                    <input id="email" name="email" type="email" autocomplete="email" required autofocus value="{{ old('email') }}" class="w-full rounded-xl border-slate-200 text-sm p-3.5 pl-11 focus:border-brand-500 focus:ring-brand-500 font-medium bg-white shadow-xs" placeholder="ejemplo@surhospital.net">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-[#0700FF] hover:bg-[#0500C9] text-white font-bold text-sm shadow-lg shadow-blue-500/25 transition-all cursor-pointer block text-center" style="background-color: #0700FF; color: #FFFFFF;">
                Enviar Enlace de Recuperación
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs space-y-2">
            <div>
                <a href="{{ route('login') }}" class="font-bold text-[#0700FF] hover:underline inline-flex items-center gap-1.5" style="color: #0700FF;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a Iniciar Sesión
                </a>
            </div>
            <div class="text-slate-400 text-[11px]">
                ¿No tienes cuenta? <a href="{{ route('register') }}" class="font-semibold text-slate-600 hover:text-slate-900 underline">Regístrate aquí</a>
            </div>
        </div>
    </div>
</div>
@endsection
