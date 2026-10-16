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

    <!-- ALERTA AUTOMÁTICA: CITA PENDIENTE DE PAGO O TRANSFERENCIA EN VALIDACIÓN -->
    @if(isset($pendingPaymentAppointment) && $pendingPaymentAppointment)
        @php
            $transferInValidation = $pendingPaymentAppointment->payments->first()?->bankTransfer?->isPending();
        @endphp
        <div class="p-4 sm:p-5 rounded-2xl {{ $transferInValidation ? 'bg-blue-50/80 border-l-4 border-l-blue-600 border-y border-r border-blue-200/80' : 'bg-amber-50/75 border-l-4 border-l-amber-500 border-y border-r border-amber-200/80' }} shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-scaleUp">
            <div class="flex items-start space-x-3.5">
                <div class="w-10 h-10 rounded-xl {{ $transferInValidation ? 'bg-blue-100 border border-blue-200 text-blue-700' : 'bg-amber-100/90 border border-amber-200/80 text-amber-700' }} flex items-center justify-center shrink-0 mt-0.5">
                    @if($transferInValidation)
                        <span class="text-lg">🏦</span>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    @endif
                </div>
                <div class="space-y-1">
                    <div class="flex items-center space-x-2">
                        @if($transferInValidation)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-900 border border-blue-300/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-1.5 animate-pulse"></span>
                                Transferencia en Validación
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-200/70 text-amber-900 border border-amber-300/60">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600 mr-1.5 animate-pulse"></span>
                                Pago Pendiente
                            </span>
                        @endif
                        <span class="text-[11px] text-slate-400 font-medium font-mono">#{{ $pendingPaymentAppointment->code }}</span>
                    </div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug">
                        @if($transferInValidation)
                            Comprobante de transferencia en revisión para tu cita con {{ $pendingPaymentAppointment->doctor?->user?->name ?? 'el especialista' }}
                        @else
                            Tienes una cita médica pendiente de pago con {{ $pendingPaymentAppointment->doctor?->user?->name ?? 'el especialista' }}
                        @endif
                    </h3>
                    <p class="text-xs text-slate-600">
                        @if($transferInValidation)
                            Turno para el {{ $pendingPaymentAppointment->start_at->translatedFormat('d \d\e F \a \l\a\s H:i') }} &bull; {{ $pendingPaymentAppointment->specialty?->name }}. Recepción está validando tu comprobante de pago.
                        @else
                            Turno para el {{ $pendingPaymentAppointment->start_at->translatedFormat('d \d\e F \a \l\a\s H:i') }} &bull; {{ $pendingPaymentAppointment->specialty?->name }} &bull; Total: <strong>${{ number_format($pendingPaymentAppointment->total_amount, 2) }} USD</strong>. Completa tu pago para formalizar y confirmar la reserva.
                        @endif
                    </p>
                </div>
            </div>

            <div class="shrink-0">
                <a href="{{ route('patient.appointment.show', $pendingPaymentAppointment) }}" class="inline-flex items-center justify-center space-x-2 w-full sm:w-auto px-4 py-2.5 rounded-xl {{ $transferInValidation ? 'bg-blue-600 hover:bg-blue-700' : 'bg-amber-500 hover:bg-amber-600' }} text-white font-bold text-xs shadow-xs hover:shadow transition-all">
                    <span>{{ $transferInValidation ? '🏦 Ver Estado' : '💳 Pagar y Confirmar' }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
    @endif

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
                    <span>Calificar Atención</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
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
