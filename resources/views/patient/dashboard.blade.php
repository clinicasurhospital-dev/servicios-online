@extends('layouts.patient')

@section('title', 'Mi Portal de Paciente — ' . config('branding.app_name'))

@section('content')
<div class="space-y-8 animate-fadeIn">
    <!-- Patient Header Greeting -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-brand-primary to-brand-primary-dark text-white p-6 sm:p-8 rounded-3xl shadow-xl shadow-brand-primary/20">
        <div>
            <span class="text-xs font-semibold text-blue-200 uppercase tracking-wider">Portal del Paciente</span>
            <h1 class="text-2xl sm:text-3xl font-black mt-1">Hola, {{ $patient->first_name }} 👋</h1>
            <p class="text-blue-100 text-xs sm:text-sm mt-1">Gestiona tus consultas, historial médico y estados de pago fácilmente.</p>
        </div>

        <a href="{{ route('booking') }}" class="inline-flex items-center justify-center space-x-2 px-6 py-3.5 rounded-2xl bg-white text-brand-primary font-bold text-sm shadow-lg hover:bg-slate-50 transition-all transform hover:scale-105 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span>Agendar Nueva Cita</span>
        </a>
    </div>

    <!-- ALERTA AUTOMÁTICA: CONSULTA ATENDIDA PENDIENTE DE VALORACIÓN -->
    @if(isset($pendingReviewAppointment) && $pendingReviewAppointment)
        <div class="p-6 sm:p-7 rounded-3xl bg-gradient-to-r from-brand-primary via-brand-primary-dark to-slate-900 text-white shadow-xl shadow-brand-primary/20 relative overflow-hidden flex flex-col sm:flex-row sm:items-center justify-between gap-5 border border-blue-400/30 animate-scaleUp">
            <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-blue-400/10 rounded-full blur-2xl"></div>
            
            <div class="flex items-start space-x-4 relative z-10">
                <div class="w-13 h-13 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center font-black text-2xl shadow-md shadow-amber-400/20 shrink-0">
                    ⭐
                </div>
                <div class="space-y-1">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-400/20 text-amber-300 border border-amber-400/30 inline-block">
                        Tu opinión es importante
                    </span>
                    <h3 class="text-base sm:text-lg font-black text-white leading-snug">
                        ¿Cómo fue tu consulta con el {{ $pendingReviewAppointment->doctor?->user?->name }}?
                    </h3>
                    <p class="text-xs text-blue-100">
                        Atención del {{ $pendingReviewAppointment->start_at->translatedFormat('d \d\e F') }} &bull; {{ $pendingReviewAppointment->specialty?->name }} (#{{ $pendingReviewAppointment->code }}). Déjanos tus sugerencias para mejorar tu servicio de salud.
                    </p>
                </div>
            </div>

            <div class="relative z-10 shrink-0">
                <a href="{{ route('patient.appointment.show', $pendingReviewAppointment) }}" class="inline-flex items-center justify-center space-x-2 w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs shadow-lg shadow-amber-400/25 transition-all transform hover:scale-105">
                    <span>⭐ Calificar Atención & Sugerencias</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
    @endif

    <!-- PRÓXIMA CITA CARD -->
    <div>
        <h2 class="text-lg font-bold text-slate-900 mb-3">Tu Próxima Cita</h2>

        @if($nextAppointment)
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-xl shadow-slate-200/50 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-brand-soft rounded-bl-full -z-0 opacity-50"></div>

                <div class="relative z-10">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary">
                            {{ $nextAppointment->specialty->name }}
                        </span>
                        <x-status-badge :status="$nextAppointment->status" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 items-center">
                        <div class="flex items-center space-x-4 sm:col-span-2">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-primary to-brand-primary-dark text-white flex items-center justify-center font-bold text-xl shadow-md shrink-0">
                                {{ substr($nextAppointment->doctor->user->name, 0, 2) }}
                            </div>
                            <div>
                                <h3 class="font-extrabold text-slate-900 text-lg sm:text-xl">{{ $nextAppointment->doctor->user->name }}</h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    {{ $nextAppointment->procedure ? $nextAppointment->procedure->name : 'Consulta Médica General' }}
                                </p>
                                <div class="mt-2 flex items-center space-x-2 text-xs font-semibold text-brand-primary">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span>{{ $nextAppointment->start_at->translatedFormat('l, d \d\e F \a \l\a\s H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-end gap-2 pt-4 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                            <a href="{{ route('patient.appointment.show', $nextAppointment) }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs text-center shadow-md shadow-brand-primary/20 transition-all">
                                Ver Detalles
                            </a>
                            @if($nextAppointment->canBeRescheduled())
                                <a href="{{ route('patient.appointment.show', $nextAppointment) }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-brand-primary font-bold text-xs text-center transition-colors">
                                    Cambiar Fecha
                                </a>
                            @endif
                            @if($nextAppointment->canBeCancelled())
                                <a href="{{ route('patient.appointment.show', $nextAppointment) }}" class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-rose-200 hover:bg-rose-50 text-rose-600 font-bold text-xs text-center transition-colors">
                                    Cancelar
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <x-empty-state
                title="No tienes citas próximas programadas"
                description="Agenda una cita médica con nuestros especialistas cuando lo necesites."
                action-text="Agendar Cita Ahora"
                action-url="{{ route('booking') }}"
            />
        @endif
    </div>

    <!-- RECIENTES / HISTORIAL -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-slate-900">Historial Reciente de Consultas</h2>
            <a href="{{ route('patient.appointments') }}" class="text-xs font-bold text-brand-primary hover:underline">Ver todas</a>
        </div>

        @if($recentAppointments->isNotEmpty())
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm divide-y divide-slate-100 overflow-hidden">
                @foreach($recentAppointments as $app)
                    <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/70 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-sm shrink-0">
                                {{ substr($app->doctor->user->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ $app->doctor->user->name }}</h4>
                                <p class="text-xs text-slate-500">{{ $app->specialty->name }} &bull; {{ $app->start_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between sm:justify-end gap-2.5">
                            <x-status-badge :status="$app->status" />

                            @if($app->status->value === 'COMPLETED')
                                @if($app->review && $app->review->isCompleted())
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        ★ {{ $app->review->rating }}/5 Calificada
                                    </span>
                                @else
                                    <a href="{{ route('patient.appointment.show', $app) }}" class="px-3 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold text-xs transition-all shadow-xs flex items-center gap-1">
                                        <span>⭐</span>
                                        <span>Calificar Cita</span>
                                    </a>
                                @endif
                            @endif

                            <a href="{{ route('patient.appointment.show', $app) }}" class="text-xs font-bold text-slate-600 hover:text-brand-primary p-1.5 rounded-lg hover:bg-slate-100">
                                Ver &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400">Aún no registras historial previo de consultas.</p>
        @endif
    </div>
</div>
@endsection
