@extends('layouts.patient')

@section('title', 'Detalle de Cita #' . $appointment->code . ' — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn" x-data="{ cancelModal: false, transferModal: false }">
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
            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
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
                    {{ substr($appointment->doctor->user->name, 0, 2) }}
                </div>
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-primary">{{ $appointment->specialty->name }}</span>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-0.5">{{ $appointment->doctor->user->name }}</h1>
                    <p class="text-xs text-slate-400">Reg. {{ $appointment->doctor->license_number }}</p>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="text-xs text-slate-400 block">Total de Consulta</span>
                <span class="text-2xl font-black text-brand-primary">${{ number_format($appointment->total_amount, 0) }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-xs text-slate-400 block font-semibold">Fecha y Hora</span>
                <span class="font-bold text-slate-900 block mt-1">{{ $appointment->start_at->translatedFormat('l, d \d\e F, Y') }}</span>
                <span class="text-xs text-brand-primary font-bold">{{ $appointment->start_at->format('H:i') }} - {{ $appointment->end_at->format('H:i') }}</span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-xs text-slate-400 block font-semibold">Procedimiento</span>
                <span class="font-bold text-slate-900 block mt-1">{{ $appointment->procedure ? $appointment->procedure->name : 'Consulta Médica General' }}</span>
                <span class="text-xs text-slate-500">Duración: {{ $appointment->duration_minutes }} minutos</span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="text-xs text-slate-400 block font-semibold">Lugar de Atención</span>
                <span class="font-bold text-slate-900 block mt-1">{{ config('branding.app_name') }}</span>
                <span class="text-xs text-slate-500">{{ config('branding.contact_address') }}</span>
            </div>
        </div>

        @if($appointment->reason)
            <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100 text-xs text-blue-900">
                <span class="font-bold block mb-1">Motivo manifestado por el paciente:</span>
                <p>{{ $appointment->reason }}</p>
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
                            <span class="font-bold text-xs text-slate-800">Método: {{ $latestPayment->method->label() }}</span>
                            <span class="text-xs text-slate-400">&bull;</span>
                            <span class="text-xs font-semibold {{ $latestPayment->status->value === 'PAID' ? 'text-emerald-600' : ($latestPayment->status->value === 'REJECTED' ? 'text-rose-600' : 'text-amber-600') }}">
                                {{ $latestPayment->status->label() }}
                            </span>
                        </div>
                        @if($latestPayment->bankTransfer)
                            <p class="text-xs text-slate-500 mt-1">Ref Bancaria: {{ $latestPayment->bankTransfer->reference_number }} ({{ $latestPayment->bankTransfer->bank_name }})</p>
                            @if($latestPayment->bankTransfer->rejection_reason)
                                <p class="text-xs text-rose-600 mt-1 font-medium">Motivo rechazo: {{ $latestPayment->bankTransfer->rejection_reason }}</p>
                            @endif
                        @endif
                    </div>

                    @if($appointment->status->value === 'PENDING_PAYMENT' || ($latestPayment->bankTransfer && $latestPayment->bankTransfer->status->value === 'REJECTED'))
                        <button @click="transferModal = true" class="px-4 py-2 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs transition-colors shadow-sm">
                            Subir Comprobante de Pago
                        </button>
                    @endif
                </div>
            @else
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center justify-between">
                    <span>Pago no registrado aún para esta consulta.</span>
                    <button @click="transferModal = true" class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs transition-colors">
                        Adjuntar Transferencia
                    </button>
                </div>
            @endif
        </div>

        <!-- ACCIONES DE CITA -->
        @if(in_array($appointment->status->value, ['PENDING', 'PENDING_PAYMENT', 'CONFIRMED']))
            <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-end gap-3">
                <button @click="cancelModal = true" class="px-4 py-2.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 font-bold text-xs transition-colors">
                    Cancelar Cita
                </button>
            </div>
        @endif
    </div>

    <!-- HISTORIAL DE ESTADOS -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 mb-4">Trazabilidad de la Cita</h3>
        <div class="space-y-4">
            @foreach($appointment->histories as $history)
                <div class="flex items-start space-x-3 text-xs">
                    <div class="w-2 h-2 rounded-full bg-brand-primary mt-1.5 shrink-0"></div>
                    <div class="flex-1">
                        <span class="font-bold text-slate-800">{{ $history->new_status->label() }}</span>
                        <span class="text-slate-400 ml-1">por {{ $history->user ? $history->user->name : 'Sistema' }} &bull; {{ $history->created_at->format('d/m/Y H:i') }}</span>
                        @if($history->reason)
                            <p class="text-slate-600 mt-0.5 italic">"{{ $history->reason }}"</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- MODAL CANCELACIÓN -->
    <div x-show="cancelModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="cancelModal = false">
            <h3 class="text-lg font-bold text-slate-900">¿Deseas cancelar esta cita médica?</h3>
            <p class="text-xs text-slate-500">Por favor indícanos el motivo de la cancelación para liberar el turno para otros pacientes.</p>

            <form action="{{ route('patient.appointment.cancel', $appointment) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Motivo de Cancelación *</label>
                    <textarea name="reason" rows="3" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary focus:ring-brand-primary" placeholder="Ej: Imprevisto laboral, reprogramaré posteriormente..."></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
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
    <div x-show="transferModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="transferModal = false">
            <h3 class="text-lg font-bold text-slate-900">Adjuntar Comprobante Bancario</h3>
            
            <div class="p-3 rounded-xl bg-blue-50 text-blue-900 text-xs space-y-1">
                <span class="font-bold block">Datos de Cuenta {{ config('branding.app_name') }}:</span>
                <p>Banco: Bancolombia - Cuenta de Ahorros # 123-456789-01</p>
                <p>Titular: SurHospital S.A.S. - NIT: 900.123.456-7</p>
            </div>

            <form action="{{ route('patient.transfer.submit', $appointment) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Banco de Origen *</label>
                    <input type="text" name="bank_name" required class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary focus:ring-brand-primary" placeholder="Ej: Bancolombia / Nequi / Davivienda" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Número de Referencia / Aprobación *</label>
                    <input type="text" name="reference_number" required class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary focus:ring-brand-primary" placeholder="Ej: 987654321" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha de Transferencia *</label>
                    <input type="date" name="transfer_date" required value="{{ now()->format('Y-m-d') }}" class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary focus:ring-brand-primary" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Foto o PDF del Comprobante (Máx 5MB) *</label>
                    <input type="file" name="receipt" required accept="image/*,.pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-soft file:text-brand-primary hover:file:bg-blue-100" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Observaciones adicionales</label>
                    <input type="text" name="notes" class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary focus:ring-brand-primary" placeholder="Opcional..." />
                </div>

                <div class="flex justify-end space-x-2 pt-2">
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
