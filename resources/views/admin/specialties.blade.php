@extends('layouts.admin')

@section('title', 'Especialidades & Procedimientos — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn">
    <!-- Header -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Especialidades & Procedimientos Clínicos</h1>
            <p class="text-xs text-slate-500">Configuración de catálogo de servicios médicos, tarifas base y tiempos de atención.</p>
        </div>
    </div>

    <!-- SPECIALTIES LIST -->
    <div class="space-y-6">
        @foreach($specialties as $spec)
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold text-xl shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h2 class="text-lg font-bold text-slate-900">{{ $spec->name }}</h2>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">Activa</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1 max-w-2xl">{{ $spec->description }}</p>
                        </div>
                    </div>

                    <div class="text-left sm:text-right">
                        <span class="text-xs text-slate-400 block">Tarifa Consulta Base:</span>
                        <span class="text-xl font-black text-brand-primary">${{ number_format($spec->base_price, 0) }}</span>
                        <span class="text-[11px] text-slate-400 block">{{ $spec->default_duration_minutes }} min por turno</span>
                    </div>
                </div>

                <!-- Procedures Grid for this Specialty -->
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Procedimientos y Servicios Asociados ({{ $spec->procedures->count() }})</h3>

                    @if($spec->procedures->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($spec->procedures as $proc)
                                <div class="p-4 rounded-2xl bg-slate-50/70 border border-slate-100 flex flex-col justify-between">
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-xs">{{ $proc->name }}</h4>
                                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">{{ $proc->description ?: 'Procedimiento clínico ambulatorio' }}</p>
                                    </div>
                                    <div class="mt-3 pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px]">
                                        <span class="text-slate-500">{{ $proc->duration_minutes }} min</span>
                                        <span class="font-bold text-brand-primary">${{ number_format($proc->price, 0) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">No hay procedimientos adicionales registrados para esta especialidad.</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
