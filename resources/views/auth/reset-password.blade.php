@extends('layouts.app')

@section('title', 'Restablecer Contraseña — ' . config('branding.app_name', 'SurHospital'))

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50/50">
    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100">
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-block mb-4">
                <x-brand.logo />
            </a>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#0700FF] flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Nueva Contraseña</h2>
            <p class="mt-2 text-xs text-slate-500 max-w-sm mx-auto">
                Ingresa y confirma tu nueva contraseña segura para actualizar el acceso a tu cuenta.
            </p>
        </div>

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

        <form class="mt-6 space-y-5" action="{{ route('password.update') }}" method="POST" x-data="{ showPass: false, showConfirm: false }">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', $email) }}" class="w-full rounded-xl border-slate-200 text-sm p-3.5 focus:border-brand-500 focus:ring-brand-500 font-medium bg-slate-50 text-slate-700 shadow-xs" placeholder="ejemplo@surhospital.net">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nueva Contraseña</label>
                <div class="relative">
                    <input id="password" name="password" :type="showPass ? 'text' : 'password'" autocomplete="new-password" required autofocus class="w-full rounded-xl border-slate-200 text-sm p-3.5 pr-11 focus:border-brand-500 focus:ring-brand-500 font-medium bg-white shadow-xs" placeholder="Mínimo 8 caracteres">
                    <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" tabindex="-1">
                        <svg x-show="!showPass" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showPass" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Confirmar Nueva Contraseña</label>
                <div class="relative">
                    <input id="password_confirmation" name="password_confirmation" :type="showConfirm ? 'text' : 'password'" autocomplete="new-password" required class="w-full rounded-xl border-slate-200 text-sm p-3.5 pr-11 focus:border-brand-500 focus:ring-brand-500 font-medium bg-white shadow-xs" placeholder="Repite tu nueva contraseña">
                    <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" tabindex="-1">
                        <svg x-show="!showConfirm" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showConfirm" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-[#0700FF] hover:bg-[#0500C9] text-white font-bold text-sm shadow-lg shadow-blue-500/25 transition-all cursor-pointer block text-center" style="background-color: #0700FF; color: #FFFFFF;">
                Restablecer y Guardar Contraseña
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs">
            <a href="{{ route('login') }}" class="font-bold text-[#0700FF] hover:underline inline-flex items-center gap-1.5" style="color: #0700FF;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al inicio de sesión
            </a>
        </div>
    </div>
</div>
@endsection
