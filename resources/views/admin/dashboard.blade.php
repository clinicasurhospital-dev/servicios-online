@extends('layouts.admin')

@section('title', 'Dashboard — ' . config('branding.app_name') . ' | ' . config('branding.software_name'))

@section('content')
<div class="space-y-8 animate-fadeIn">
    <!-- Header Greeting and Quick Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Panel de Control Clínico</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Monitoreo de citas, ingresos, ocupación médica y validación de pagos en tiempo real.</p>
        </div>

        <div class="flex items-center space-x-3">
            <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Sistema Operativo</span>
            </span>
            <span class="text-xs text-slate-400 font-mono">{{ now()->translatedFormat('l, d \d\e F Y') }}</span>
        </div>
    </div>

    <!-- STATS CARDS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Citas Hoy -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Citas Programadas Hoy</span>
                <span class="text-3xl font-black text-slate-900 mt-1 block">{{ $todayAppointments }}</span>
                <span class="text-xs text-emerald-600 font-semibold mt-1 block">{{ $todayConfirmed }} confirmadas</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        </div>

        <!-- Citas Pendientes -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Pendientes de Pago/Turno</span>
                <span class="text-3xl font-black text-amber-600 mt-1 block">{{ $todayPending }}</span>
                <span class="text-xs text-slate-500 font-medium mt-1 block">{{ $todayCancelled }} canceladas / {{ $todayNoShow }} no asistió</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Pagos por Validar -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Transferencias por Validar</span>
                <span class="text-3xl font-black text-brand-primary mt-1 block">{{ $pendingTransfersCount }}</span>
                <a href="{{ route('admin.transfers') }}" class="text-xs text-brand-primary font-bold hover:underline mt-1 block">Validar ahora &rarr;</a>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
        </div>

        <!-- Ingresos del Mes -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Recaudación del Mes</span>
                <span class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 block">${{ number_format($monthRevenue, 0) }}</span>
                <span class="text-xs text-slate-400 font-medium mt-1 block">Histórico: ${{ number_format($totalRevenue, 0) }}</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shadow-xs">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- MAIN TWO-COLUMN SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- UPCOMING APPOINTMENTS TODAY (8 Cols) -->
        <div class="lg:col-span-8 bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Agenda de Turnos para Hoy</h2>
                    <p class="text-xs text-slate-500">Pacientes programados para atención durante el día de hoy.</p>
                </div>
                <a href="{{ route('admin.agenda') }}" class="text-xs font-bold text-brand-primary hover:underline">Ver Agenda Completa &rarr;</a>
            </div>

            @if($upcomingToday->isNotEmpty())
                <div class="divide-y divide-slate-100">
                    @foreach($upcomingToday as $appointment)
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/50 rounded-2xl px-3 -mx-3 transition-colors">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-11 h-11 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ $appointment->start_at->format('H:i') }}
                                </div>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-bold text-slate-900 text-sm">{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</h4>
                                        <span class="text-xs text-slate-400 font-mono">#{{ $appointment->code }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Dr. {{ $appointment->doctor->user->name }} &bull; <span class="text-brand-primary font-semibold">{{ $appointment->specialty->name }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-3 self-end sm:self-auto">
                                <x-status-badge :status="$appointment->status" />
                                <a href="{{ route('admin.appointments') }}?search={{ $appointment->code }}" class="p-2 rounded-xl text-slate-400 hover:text-brand-primary hover:bg-slate-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 text-slate-400">
                    <p class="text-sm font-semibold">No hay consultas programadas para hoy.</p>
                </div>
            @endif
        </div>

        <!-- SPECIALTIES & DOCTOR METRICS (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Top Specialties -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 mb-4">Demanda por Especialidad</h3>
                <div class="space-y-4">
                    @foreach($appointmentsBySpecialty as $spec)
                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-slate-700">{{ $spec->name }}</span>
                                <span class="text-brand-primary font-bold">{{ $spec->appointments_count }} citas</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                                @php
                                    $percent = $totalAppointments = $spec->appointments_count ? min(100, ($spec->appointments_count / max(1, $appointmentsBySpecialty->first()->appointments_count)) * 100) : 0;
                                @endphp
                                <div class="h-full bg-brand-primary rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Top Doctors -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 mb-4">Citas por Especialista</h3>
                <div class="space-y-3">
                    @foreach($appointmentsByDoctor as $doc)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold text-xs">
                                    {{ substr($doc->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 leading-snug">{{ $doc->user->name }}</h4>
                                    <p class="text-[11px] text-slate-400">{{ $doc->specialties->first()?->name }}</p>
                                </div>
                            </div>
                            <span class="font-black text-brand-primary text-sm">{{ $doc->appointments_count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
