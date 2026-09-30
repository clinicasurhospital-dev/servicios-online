@extends('layouts.patient')

@section('title', 'Detalle de Cita #' . $appointment->code . ' — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn" x-data="{
    cancelModal: false,
    transferModal: false,
    rescheduleModal: false,
    rescheduleDate: '{{ now()->addDay()->format('Y-m-d') }}',
    rescheduleTime: '',
    rescheduleReason: '',
    loadingSlots: false,
    slots: [],
    slotsError: '',
    fetchSlots() {
        if (!this.rescheduleDate) return;
        this.loadingSlots = true;
        this.slotsError = '';
        this.slots = [];
        this.rescheduleTime = '';
        fetch(`{{ route('patient.appointment.slots', $appointment) }}?date=${this.rescheduleDate}`)
            .then(res => res.json())
            .then(data => {
                this.loadingSlots = false;
                if (data.success) {
                    this.slots = data.slots;
                    if (this.slots.length === 0) {
                        this.slotsError = 'No hay turnos disponibles para esta fecha. Por favor selecciona otro día.';
                    }
                } else {
                    this.slotsError = data.message || 'Error al consultar disponibilidad.';
                }
            })
            .catch(err => {
                this.loadingSlots = false;
                this.slotsError = 'Error de conexión al obtener horarios.';
            });
    },
    openReschedule() {
        this.rescheduleModal = true;
        this.$nextTick(() => {
            if (this.slots.length === 0 && !this.loadingSlots) {
                this.fetchSlots();
            }
        });
    }
}">
    <!-- Header with Back button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('patient.appointments') }}" class="inline-flex items-center space-x-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Volver a Mis Citas</span>
        </a>

        <div class="flex items-center space-x-2">
            <span class="text-xs font-mono text-slate-400">#{{ $appointment->code }}</span>
            <x-status-badge :status="$appointment->status" />
        </div>
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

    <!-- Main Appointment Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-xl shadow-slate-200/50 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-primary to-brand-primary-dark text-white flex items-center justify-center font-bold text-2xl shadow-md shrink-0">
                    {{ substr($appointment->doctor?->user?->name ?? 'Dr', 0, 2) }}
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-primary">{{ $appointment->specialty?->name ?? 'Consulta' }}</span>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5">{{ $appointment->doctor?->user?->name ?? 'Especialista' }}</h1>
                    <p class="text-xs text-slate-400">
                        Reg. {{ $appointment->doctor?->license_number ?? 'MSP' }}
                        @if($appointment->doctor?->room_office)
                            &bull; {{ $appointment->doctor->room_office }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="text-xs text-slate-400 block">Total de Consulta</span>
                <span class="text-2xl font-black text-brand-primary">${{ number_format($appointment->total_amount, 2) }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-xs text-slate-400 block font-semibold">Fecha y Hora</span>
                <span class="font-bold text-slate-900 block mt-1">{{ $appointment->start_at->translatedFormat('l, d \d\e F, Y') }}</span>
                <span class="text-xs text-brand-primary font-bold">{{ $appointment->start_at->format('H:i') }} - {{ $appointment->end_at->format('H:i') }}</span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-xs text-slate-400 block font-semibold">Procedimiento / Servicio</span>
                <span class="font-bold text-slate-900 block mt-1">{{ $appointment->procedure ? $appointment->procedure->name : 'Consulta Médica General' }}</span>
                <span class="text-xs text-slate-500">Duración: {{ $appointment->duration_minutes }} minutos</span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-xs text-slate-400 block font-semibold">Lugar de Atención</span>
                <span class="font-bold text-slate-900 block mt-1">{{ config('branding.app_name') }}</span>
                <span class="text-xs text-slate-500">{{ config('branding.contact_address') }}</span>
            </div>
        </div>

        @if($appointment->patient_notes)
            <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100 text-xs text-blue-900">
                <span class="font-bold block mb-1">Motivo manifestado por el paciente:</span>
                <p>{{ $appointment->patient_notes }}</p>
            </div>
        @endif

        <!-- SECCIÓN: HISTORIA CLÍNICA, DIAGNÓSTICO Y RESULTADOS DE PROCEDIMIENTOS -->
        <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/60">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-brand-primary text-white flex items-center justify-center font-bold shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Historia Clínica, Diagnóstico y Resultados</h3>
                        <p class="text-[11px] text-slate-500">Documentación oficial emitida por el equipo médico de SurHospital</p>
                    </div>
                </div>
                @if($appointment->hasClinicalHistory() || $appointment->hasProcedureDocument())
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                        Documentos Listos
                    </span>
                @endif
            </div>

            @if($appointment->clinical_notes)
                <div class="p-4 rounded-2xl bg-white border border-emerald-200/70 text-xs shadow-2xs space-y-1">
                    <span class="font-bold text-emerald-800 uppercase tracking-wider text-[10px] flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <span>Diagnóstico / Notas Clínicas del Médico:</span>
                    </span>
                    <p class="text-slate-800 font-medium whitespace-pre-line leading-relaxed">{{ $appointment->clinical_notes }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
                <!-- Historia Clínica Card -->
                @if($appointment->hasClinicalHistory())
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 flex flex-col justify-between gap-3 shadow-sm hover:border-brand-primary/40 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="font-bold text-slate-900 block text-xs truncate">{{ $appointment->clinical_history_filename ?: 'Historia_Clinica.pdf' }}</span>
                                <span class="text-[11px] text-slate-500">Historia Clínica / Informe de Consulta</span>
                                @if($appointment->clinical_history_uploaded_at)
                                    <span class="text-[10px] text-slate-400 block mt-0.5">Subido: {{ $appointment->clinical_history_uploaded_at->format('d/m/Y H:i') }}</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('patient.appointment.clinical_record', [$appointment, 'historia']) }}" class="w-full py-2 px-3 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs text-center shadow-xs transition-colors flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Descargar Historia Clínica</span>
                        </a>
                    </div>
                @endif

                <!-- Resultados / Procedimiento Card -->
                @if($appointment->hasProcedureDocument())
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 flex flex-col justify-between gap-3 shadow-sm hover:border-emerald-500/40 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="font-bold text-slate-900 block text-xs truncate">{{ $appointment->procedure_document_filename ?: 'Resultados_Procedimiento.pdf' }}</span>
                                <span class="text-[11px] text-slate-500">Resultados / Procedimientos Médicos</span>
                                @if($appointment->clinical_history_uploaded_at)
                                    <span class="text-[10px] text-slate-400 block mt-0.5">Subido: {{ $appointment->clinical_history_uploaded_at->format('d/m/Y H:i') }}</span>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('patient.appointment.clinical_record', [$appointment, 'procedimiento']) }}" class="w-full py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs text-center shadow-xs transition-colors flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Descargar Resultados / Procedimiento</span>
                        </a>
                    </div>
                @endif

                @if(!$appointment->hasClinicalHistory() && !$appointment->hasProcedureDocument() && !$appointment->clinical_notes)
                    <div class="col-span-full p-4 rounded-2xl bg-white border border-slate-200 text-center text-xs text-slate-500 py-6">
                        <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="font-bold text-slate-700">Documentos Clínicos en Proceso</p>
                        <p class="text-slate-400 text-[11px] mt-0.5">La historia clínica, diagnósticos y resultados de procedimientos se publicarán aquí una vez que el especialista complete la consulta.</p>
                    </div>
                @endif
            </div>
        </div>

        @if($appointment->status->value === 'CANCELLED' && $appointment->cancelled_reason)
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-xs text-rose-900">
                <span class="font-bold block mb-1">Motivo de Cancelación:</span>
                <p>{{ $appointment->cancelled_reason }}</p>
            </div>
        @endif

        <!-- PAGO Y COMPROBANTE -->
        <div class="pt-4 border-t border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 mb-3">Información del Pago</h3>

            @php
                $latestPayment = $appointment->payments->first();
            @endphp

            @if($latestPayment)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-bold text-xs text-slate-800">Método: {{ $latestPayment->payment_method?->label() ?? 'No especificado' }}</span>
                            <span class="text-xs text-slate-400">&bull;</span>
                            <span class="text-xs font-semibold {{ $latestPayment->status?->value === 'PAID' ? 'text-emerald-600' : ($latestPayment->status?->value === 'REJECTED' ? 'text-rose-600' : 'text-amber-600') }}">
                                {{ $latestPayment->status?->label() ?? 'Pendiente' }}
                            </span>
                        </div>
                        @if($latestPayment->bankTransfer)
                            <p class="text-xs text-slate-500 mt-1">Ref Bancaria: {{ $latestPayment->bankTransfer->reference_number }} ({{ $latestPayment->bankTransfer->bank_name }})</p>
                            @if($latestPayment->bankTransfer->rejection_reason)
                                <p class="text-xs text-rose-600 mt-1 font-medium">Motivo rechazo: {{ $latestPayment->bankTransfer->rejection_reason }}</p>
                            @endif
                        @endif
                    </div>

                    @if($appointment->status->value === 'PENDING_PAYMENT' || ($latestPayment->bankTransfer && $latestPayment->bankTransfer->isRejected()))
                        <button type="button" @click="transferModal = true" class="px-4 py-2 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs transition-colors shadow-sm">
                            Subir Comprobante de Pago
                        </button>
                    @endif
                </div>
            @else
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center justify-between">
                    <span>Pago no registrado aún para esta consulta.</span>
                    <button type="button" @click="transferModal = true" class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs transition-colors">
                        Adjuntar Transferencia
                    </button>
                </div>
            @endif
        </div>

        <!-- ACCIONES DE CITA: REPROGRAMAR & CANCELAR -->
        @if($appointment->canBeRescheduled() || $appointment->canBeCancelled())
            <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-end gap-3">
                @if($appointment->canBeRescheduled())
                    <button type="button" @click="openReschedule()" class="inline-flex items-center space-x-1.5 px-4 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 transition-all transform hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Cambiar Fecha / Reprogramar</span>
                    </button>
                @endif

                @if($appointment->canBeCancelled())
                    <button type="button" @click="cancelModal = true" class="inline-flex items-center space-x-1.5 px-4 py-2.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 font-bold text-xs transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>Cancelar Cita</span>
                    </button>
                @endif
            </div>
        @endif
    </div>

    <!-- HISTORIAL DE ESTADOS / TRAZABILIDAD -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 mb-4">Trazabilidad de la Cita</h3>
        <div class="space-y-4">
            @foreach($appointment->histories as $history)
                <div class="flex items-start space-x-3 text-xs">
                    <div class="w-2.5 h-2.5 rounded-full bg-brand-primary mt-1 shrink-0"></div>
                    <div class="flex-1">
                        <span class="font-bold text-slate-800">
                            @if(is_object($history->new_status) && method_exists($history->new_status, 'label'))
                                {{ $history->new_status->label() }}
                            @elseif(is_string($history->new_status) && \App\Domain\Appointments\Enums\AppointmentStatus::tryFrom($history->new_status))
                                {{ \App\Domain\Appointments\Enums\AppointmentStatus::from($history->new_status)->label() }}
                            @else
                                {{ $history->new_status }}
                            @endif
                        </span>
                        <span class="text-slate-400 ml-1">por {{ $history->user ? $history->user->name : 'Sistema' }} &bull; {{ $history->created_at ? $history->created_at->format('d/m/Y H:i') : '' }}</span>
                        @if($history->reason)
                            <p class="text-slate-600 mt-0.5 italic">"{{ $history->reason }}"</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- MODAL REPROGRAMAR / CAMBIAR FECHA -->
    <div x-show="rescheduleModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-5" @click.away="rescheduleModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <span class="text-xs font-bold text-brand-primary uppercase tracking-wider">Reprogramación de Consulta</span>
                    <h3 class="text-lg font-black text-slate-900 mt-0.5">Cambiar Fecha y Hora</h3>
                </div>
                <button type="button" @click="rescheduleModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-3.5 rounded-2xl bg-blue-50 border border-blue-100 text-xs text-blue-900 flex items-start space-x-2.5">
                <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <p class="font-bold">Médico: {{ $appointment->doctor?->user?->name ?? 'Especialista' }}</p>
                    <p class="text-blue-700 mt-0.5">Selecciona un nuevo día hábil y horario disponible para mover tu turno sin costo adicional.</p>
                </div>
            </div>

            <form action="{{ route('patient.appointment.reschedule', $appointment) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">1. Selecciona la Nueva Fecha *</label>
                    <input 
                        type="date" 
                        name="new_date" 
                        required 
                        x-model="rescheduleDate" 
                        @change="fetchSlots()"
                        min="{{ now()->format('Y-m-d') }}" 
                        class="w-full rounded-2xl border-slate-200 text-xs p-3 font-semibold focus:border-brand-primary focus:ring-brand-primary shadow-xs" 
                    />
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">2. Selecciona el Nuevo Horario Disponible *</label>
                    
                    <!-- Loading State -->
                    <div x-show="loadingSlots" class="py-6 text-center text-xs text-slate-500 space-y-2">
                        <svg class="w-6 h-6 animate-spin mx-auto text-brand-primary" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <p>Consultando agenda del especialista...</p>
                    </div>

                    <!-- Error / No slots -->
                    <div x-show="!loadingSlots && slotsError" class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs">
                        <span x-text="slotsError"></span>
                    </div>

                    <!-- Slots Grid -->
                    <div x-show="!loadingSlots && slots.length > 0" class="grid grid-cols-2 sm:grid-cols-3 gap-2 max-h-48 overflow-y-auto p-1">
                        <template x-for="slot in slots" :key="slot.time">
                            <label class="relative flex items-center justify-center p-2.5 rounded-xl border cursor-pointer text-xs font-bold transition-all"
                                :class="rescheduleTime === slot.time ? 'bg-brand-primary text-white border-brand-primary shadow-md' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100 hover:border-slate-300'">
                                <input type="radio" name="new_time" :value="slot.time" x-model="rescheduleTime" required class="sr-only" />
                                <span x-text="slot.label"></span>
                            </label>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">3. Motivo del cambio (opcional)</label>
                    <textarea name="reason" rows="2" x-model="rescheduleReason" class="w-full rounded-2xl border-slate-200 text-xs p-3 focus:border-brand-primary focus:ring-brand-primary shadow-xs" placeholder="Ej: Cambio de turno laboral, compromiso imprevisto..."></textarea>
                </div>

                <div class="flex justify-end space-x-2.5 pt-2 border-t border-slate-100">
                    <button type="button" @click="rescheduleModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="!rescheduleTime || loadingSlots" class="px-5 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark disabled:opacity-50 text-white font-bold text-xs shadow-md shadow-brand-primary/20 transition-all">
                        Confirmar Reprogramación
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL CANCELACIÓN -->
    <div x-show="cancelModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="cancelModal = false">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">¿Deseas cancelar esta cita médica?</h3>
                <button type="button" @click="cancelModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <p class="text-xs text-slate-500">Por favor indícanos el motivo de la cancelación para liberar el turno para otros pacientes.</p>

            <form action="{{ route('patient.appointment.cancel', $appointment) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Motivo de Cancelación *</label>
                    <textarea name="reason" rows="3" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary focus:ring-brand-primary shadow-xs" placeholder="Ej: Imprevisto de salud, reprogramaré en otra fecha..."></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="cancelModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                        Volver
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm">
                        Confirmar Cancelación
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL SUBIR TRANSFERENCIA -->
    <div x-show="transferModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="transferModal = false">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Adjuntar Comprobante Bancario</h3>
                <button type="button" @click="transferModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-3.5 rounded-2xl bg-blue-50 text-blue-900 text-xs space-y-1">
                <span class="font-bold block">Datos de Cuenta {{ config('branding.app_name') }}:</span>
                <p>Banco: Banco Pichincha - Cta. Corriente # 2100849201</p>
                <p>Titular: SurHospital S.A. - RUC: 0992384756001</p>
            </div>

            <form action="{{ route('patient.transfer.submit', $appointment) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Banco de Origen *</label>
                    <input type="text" name="bank_name" required class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary focus:ring-brand-primary shadow-xs" placeholder="Ej: Banco Pichincha / Guayaquil / Pacífico" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Número de Referencia / Aprobación *</label>
                    <input type="text" name="reference_number" required class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary focus:ring-brand-primary shadow-xs" placeholder="Ej: 987654321" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha de Transferencia *</label>
                    <input type="date" name="transfer_date" required value="{{ now()->format('Y-m-d') }}" class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary focus:ring-brand-primary shadow-xs" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Foto o PDF del Comprobante (Máx 5MB) *</label>
                    <input type="file" name="receipt" required accept="image/*,.pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-soft file:text-brand-primary hover:file:bg-blue-100" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Observaciones adicionales</label>
                    <input type="text" name="notes" class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary focus:ring-brand-primary shadow-xs" placeholder="Opcional..." />
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="transferModal = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-sm">
                        Enviar Comprobante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
