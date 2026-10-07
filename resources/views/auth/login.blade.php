@extends('layouts.app')

@section('title', 'Iniciar Sesión — ' . config('branding.app_name'))

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50/50">
    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100">
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-block mb-4">
                <x-brand.logo />
            </a>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Iniciar Sesión</h2>
            <p class="mt-1 text-xs text-slate-500">Ingresa a tu portal de paciente o panel administrativo</p>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if(session('warning'))
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold">
                {{ session('warning') }}
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-semibold">
                {{ session('info') }}
            </div>
        @endif

        @if(session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium">
                {{ session('status') }}
            </div>
        @endif

        <form class="mt-6 space-y-5" action="{{ route('login') }}" method="POST" x-data="{ showPassword: false }">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Correo Electrónico</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" class="w-full rounded-xl border-slate-200 text-sm p-3.5 focus:border-brand-500 focus:ring-brand-500 font-medium bg-white shadow-xs" placeholder="admin@surhospital.com">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Contraseña</label>
                <div class="relative">
                    <input id="password" name="password" :type="showPassword ? 'text' : 'password'" autocomplete="current-password" required class="w-full rounded-xl border-slate-200 text-sm p-3.5 pr-11 focus:border-brand-500 focus:ring-brand-500 font-medium bg-white shadow-xs" placeholder="••••••••">
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" tabindex="-1">
                        <!-- Eye Icon (Show) -->
                        <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <!-- Eye Slash Icon (Hide) -->
                        <svg x-show="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-500 focus:ring-brand-500">
                    <span class="text-slate-600 font-medium">Recordarme</span>
                </label>
                <a href="{{ route('password.request') }}" class="font-semibold text-[#0700FF] hover:underline" style="color: #0700FF;">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-[#0700FF] hover:bg-[#0500C9] text-white font-bold text-sm shadow-lg shadow-blue-500/25 transition-all cursor-pointer block text-center" style="background-color: #0700FF; color: #FFFFFF;">
                Ingresar a la Plataforma
            </button>
        </form>

        <!-- Quick Access Demo Credentials -->
        <div class="mt-6 pt-5 border-t border-slate-100">
            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2.5 text-center">Acceso Rápido de Prueba (1 Clic)</span>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button type="button" onclick="document.getElementById('email').value='admin@surhospital.com'; document.getElementById('password').value='password';" class="p-2.5 rounded-xl bg-slate-50 hover:bg-brand-soft hover:text-brand-primary border border-slate-200 text-left transition-all cursor-pointer">
                    <span class="font-bold block text-slate-800 text-[11px]">👑 Admin</span>
                    <span class="text-[10px] text-slate-500 font-mono truncate block">admin@surhospital.com</span>
                </button>
                <button type="button" onclick="document.getElementById('email').value='recepcion@surhospital.com'; document.getElementById('password').value='password';" class="p-2.5 rounded-xl bg-slate-50 hover:bg-brand-soft hover:text-brand-primary border border-slate-200 text-left transition-all cursor-pointer">
                    <span class="font-bold block text-slate-800 text-[11px]">📋 Recepción</span>
                    <span class="text-[10px] text-slate-500 font-mono truncate block">recepcion@surhospital.com</span>
                </button>
                <button type="button" onclick="document.getElementById('email').value='dr.carlos.soledispa@surhospital.net'; document.getElementById('password').value='password';" class="p-2.5 rounded-xl bg-slate-50 hover:bg-brand-soft hover:text-brand-primary border border-slate-200 text-left transition-all cursor-pointer">
                    <span class="font-bold block text-slate-800 text-[11px]">🩺 Médico</span>
                    <span class="text-[10px] text-slate-500 font-mono truncate block">dr.carlos.soledispa@...</span>
                </button>
                <button type="button" onclick="document.getElementById('email').value='paciente@surhospital.com'; document.getElementById('password').value='password';" class="p-2.5 rounded-xl bg-slate-50 hover:bg-brand-soft hover:text-brand-primary border border-slate-200 text-left transition-all cursor-pointer">
                    <span class="font-bold block text-slate-800 text-[11px]">👤 Paciente</span>
                    <span class="text-[10px] text-slate-500 font-mono truncate block">paciente@surhospital.com</span>
                </button>
            </div>
        </div>

        <div class="mt-6 pt-5 border-t border-slate-100 text-center text-xs text-slate-500">
            ¿Aún no tienes cuenta?
            <a href="{{ route('register') }}" class="font-bold text-[#0700FF] hover:underline" style="color: #0700FF;">Regístrate aquí</a>
        </div>
    </div>
</div>
@endsection
