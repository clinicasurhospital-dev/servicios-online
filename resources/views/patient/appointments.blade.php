@extends('layouts.patient')

@section('title', 'Mis Citas — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Mis Citas Médicas</h1>
            <p class="text-xs text-slate-500 mt-1">Revisa el estado de todas tus reservas pasadas y programadas.</p>
        </div>

        <a href="{{ route('booking') }}" class="px-4 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 transition-all">
            + Agendar Cita
        </a>
    </div>

    @if($appointments->isNotEmpty())
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm divide-y divide-slate-100 overflow-hidden">
            @foreach($appointments as $app)
                <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/70 transition-colors">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold text-base shrink-0">
                            {{ substr($app->doctor->user->name, 0, 2) }}
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xs font-bold text-brand-primary">{{ $app->specialty->name }}</span>
                                <span class="text-xs text-slate-300">&bull;</span>
                                <span class="text-xs text-slate-400 font-mono">#{{ $app->code }}</span>
                            </div>
                            <h3 class="font-bold text-slate-900 text-base mt-0.5">{{ $app->doctor->user->name }}</h3>
                            <p class="text-xs text-slate-500 mt-1 flex items-center space-x-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>{{ $app->start_at->translatedFormat('d \d\e F, Y - H:i') }} ({{ $app->duration_minutes }} min)</span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end space-x-4 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                        <x-status-badge :status="$app->status" />
                        <a href="{{ route('patient.appointment.show', $app) }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                            Ver Detalles
                        </a>
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
