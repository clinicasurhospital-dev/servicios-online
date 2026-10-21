@extends('layouts.patient')

@section('title', 'Mis Citas — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Mis Citas Médicas</h1>
            <p class="text-xs text-slate-500 mt-1">Revisa el estado de todas tus reservas pasadas y programadas, cambia fecha o cancela cuando lo necesites.</p>
        </div>

        @if($branding->canShowBookingButton())
            <a href="{{ route('booking') }}" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-2xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 transition-all transform hover:scale-105 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>+ Agendar Nueva Cita</span>
            </a>
        @endif
    </div>

    <!-- ALERTA AUTOMÁTICA: CONSULTA ATENDIDA PENDIENTE DE VALORACIÓN -->
    @if(isset($pendingReviewAppointment) && $pendingReviewAppointment)
        <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/70 border-l-4 border-l-amber-500 border-y border-r border-amber-200/70 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-scaleUp">
            <div class="flex items-start space-x-3.5">
                <div class="w-10 h-10 rounded-xl bg-amber-100/80 border border-amber-200/80 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200/60">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                            Tu opinión es importante
                        </span>
                        <span class="text-[11px] text-slate-400 font-medium font-mono">#{{ $pendingReviewAppointment->code }}</span>
                    </div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug">
                        ¿Cómo fue tu atención médica con {{ $pendingReviewAppointment->doctor?->user?->name ?? 'tu especialista' }}?
                    </h3>
                    <p class="text-xs text-slate-600">
                        Consulta del {{ $pendingReviewAppointment->start_at->translatedFormat('d \d\e F') }} &bull; {{ $pendingReviewAppointment->specialty?->name }}. Déjanos tu valoración y sugerencias para seguir mejorando.
                    </p>
                </div>
            </div>

            <div class="shrink-0">
                <a href="{{ route('patient.appointment.show', $pendingReviewAppointment) }}" class="inline-flex items-center justify-center space-x-2 w-full sm:w-auto px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-xs hover:shadow transition-all">
                    <span>Calificar Consulta</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
    @endif

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
                            @if($app->hasClinicalHistory() || $app->hasProcedureDocument())
                                <div class="mt-2 flex flex-wrap items-center gap-1.5">
                                    @if($app->hasClinicalHistory())
                                        <a href="{{ route('patient.appointment.clinical_record', [$app, 'historia']) }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-brand-soft text-brand-primary hover:bg-brand-primary hover:text-white transition-colors">
                                            <span>📄 Historia Clínica</span>
                                        </a>
                                    @endif
                                    @if($app->hasProcedureDocument())
                                        <a href="{{ route('patient.appointment.clinical_record', [$app, 'procedimiento']) }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white transition-colors">
                                            <span>🔬 Resultados</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between lg:justify-end gap-2.5 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                        <x-status-badge :status="$app->status" />

                        <div class="flex flex-wrap items-center space-x-2">
                            @if(in_array($app->status->value, ['PENDING', 'PENDING_PAYMENT']))
                                @php
                                    $appPendingTransfer = $app->payments->first()?->bankTransfer?->isPending();
                                @endphp
                                @if($appPendingTransfer)
                                    <a href="{{ route('patient.appointment.show', $app) }}#pago" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-all shadow-xs flex items-center gap-1.5">
                                        <span>🏦 Transferencia en Validación</span>
                                    </a>
                                @else
                                    <a href="{{ route('patient.appointment.show', $app) }}#pago" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition-all shadow-xs flex items-center gap-1.5">
                                        <span>💳 Pagar y Confirmar</span>
                                    </a>
                                @endif
                            @endif

                            @if($app->status->value === 'COMPLETED')
                                @if($app->review && $app->review->isCompleted())
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        ★ {{ $app->review->rating }}/5 Calificada
                                    </span>
                                @else
                                    <a href="{{ route('patient.appointment.show', $app) }}" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold text-xs transition-all shadow-xs flex items-center gap-1">
                                        <span>⭐</span>
                                        <span>Calificar Consulta</span>
                                    </a>
                                @endif
                            @endif

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
            description="{{ $branding->canShowBookingButton() ? 'Agenda tu primera consulta médica con nuestros especialistas en pocos clics.' : 'Para coordinar una cita médica comunícate con el área de recepción.' }}"
            :action-text="$branding->canShowBookingButton() ? 'Agendar Primera Cita' : null"
            :action-url="$branding->canShowBookingButton() ? route('booking') : null"
        />
    @endif
</div>
@endsection
