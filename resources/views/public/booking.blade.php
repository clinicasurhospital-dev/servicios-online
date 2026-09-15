@extends('layouts.app')

@section('title', 'Agendar Cita Médica Online — ' . config('branding.app_name'))
@section('meta_description', 'Agenda tu consulta médica especializada en línea de forma rápida y segura.')

@section('content')
<div class="min-h-screen bg-slate-50/50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-8">
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary">Agendamiento Digital</span>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 mt-2">Agenda tu Cita Médica</h1>
            <p class="text-slate-500 text-sm mt-2">Selecciona la especialidad, tu médico de preferencia y el horario que mejor se adapte a tu día.</p>
        </div>

        <livewire:booking-wizard />
    </div>
</div>
@endsection
