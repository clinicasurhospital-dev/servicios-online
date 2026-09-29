@extends('layouts.patient')

@section('title', 'Mis Citas — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Mis Citas Médicas</h1>
            <p class="text-xs text-slate-500 mt-1">Revisa el estado de todas tus reservas pasadas y programadas, cambia fecha o cancela cuando lo necesites.</p>
        </div>

        <a href="{{ route('booking') }}" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-2xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 transition-all transform hover:scale-105 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span>+ Agendar Nueva Cita</span>
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center space-x-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if($appointments->isNotEmpty())
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm divide-y divide-slate-100 overflow-hidden">
            @foreach($appointments as $app)
                <div class="p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4 hover:bg-slate-50/70 transition-colors">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-primary to-brand-primary-dark text-white flex items-center justify-center font-bold text-base shadow-sm shrink-0">
                            {{ substr($app->doctor?->user?->name ?? 'Dr', 0, 2) }}
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-bold text-brand-primary">{{ $app->specialty?->name ?? 'Consulta' }}</span>
                                <span class="text-xs text-slate-300">&bull;</span>
                                <span class="text-xs text-slate-400 font-mono">#{{ $app->code }}</span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-base mt-0.5">{{ $app->doctor?->user?->name ?? 'Especialista' }}</h3>
                            <p class="text-xs text-slate-500 mt-1 flex items-center space-x-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span class="font-semibold text-slate-700">{{ $app->start_at->translatedFormat('d \d\e F, Y - H:i') }}</span>
                                <span>({{ $app->duration_minutes }} min)</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between lg:justify-end gap-2.5 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                        <x-status-badge :status="$app->status" />

                        <div class="flex items-center space-x-2">
                            <a href="{{ route('patient.appointment.show', $app) }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors flex items-center space-x-1">
                                <span>Ver Detalles</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>

                            @if($app->canBeRescheduled())
                                <a href="{{ route('patient.appointment.show', $app) }}" class="px-3.5 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-brand-primary font-bold text-xs transition-colors">
                                    Cambiar Fecha
                                </a>
                            @endif

                            @if($app->canBeCancelled())
                                <a href="{{ route('patient.appointment.show', $app) }}" class="px-3.5 py-2 rounded-xl border border-rose-200 hover:bg-rose-50 text-rose-600 font-bold text-xs transition-colors">
                                    Cancelar
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $appointments->links() }}
        </div>
    @else
        <x-empty-state
            title="Aún no tienes citas registradas"
            description="Agenda tu primera consulta médica con nuestros especialistas en pocos clics."
            action-text="Agendar Primera Cita"
            action-url="{{ route('booking') }}"
        />
    @endif
</div>
@endsection
