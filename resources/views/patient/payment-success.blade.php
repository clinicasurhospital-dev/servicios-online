@extends('layouts.patient')

@section('title', 'Pago Exitoso — ' . config('branding.app_name'))

@section('content')
<div class="max-w-md mx-auto my-12 bg-white p-8 rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 text-center animate-fadeIn">
    <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4 shadow-md shadow-emerald-500/20">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
    </div>

    <h1 class="text-2xl font-black text-slate-900">¡Pago Procesado con Éxito!</h1>
    <p class="text-xs text-slate-500 mt-2">Tu consulta médica ha sido confirmada inmediatamente en el sistema.</p>

    <div class="mt-6 flex flex-col gap-2">
        <a href="{{ route('patient.dashboard') }}" class="w-full py-3 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs transition-all shadow-md shadow-brand-primary/20">
            Ir a Mi Portal de Paciente
        </a>
        <a href="{{ route('home') }}" class="w-full py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all">
            Volver al Inicio
        </a>
    </div>
</div>
@endsection
