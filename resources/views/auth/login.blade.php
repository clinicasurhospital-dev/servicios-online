@extends('layouts.app')

@section('title', 'Iniciar Sesión — ' . config('branding.app_name'))

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50/50">
    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100">
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-block mb-4">
                <x-brand.logo height="38" />
            </a>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Iniciar Sesión</h2>
            <p class="mt-1 text-xs text-slate-500">Ingresa a tu portal de paciente o panel administrativo</p>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium space-y-1">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if(session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium">
                {{ session('status') }}
            </div>
        @endif

        <form class="mt-6 space-y-4" action="{{ route('login') }}" method="POST">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Correo Electrónico</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary font-medium" placeholder="tu@correo.com">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Contraseña</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary font-medium" placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-primary focus:ring-brand-primary">
                    <span class="text-slate-600 font-medium">Recordarme</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-sm shadow-md shadow-brand-primary/25 transition-all">
                Ingresar a la Plataforma
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
            ¿Aún no tienes cuenta?
            <a href="{{ route('register') }}" class="font-bold text-brand-primary hover:underline">Regístrate aquí</a>
        </div>
    </div>
</div>
@endsection
