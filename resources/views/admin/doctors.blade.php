@extends('layouts.admin')

@section('title', 'Cuerpo Médico & Turnos — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn">
    <!-- Header with Filters -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Especialistas Médicos</h1>
            <p class="text-xs text-slate-500">Gestión de profesionales de la salud, registros médicos y asignación de horarios.</p>
        </div>

        <form method="GET" action="{{ route('admin.doctors') }}" class="flex flex-wrap items-center gap-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre o registro..." class="rounded-xl border-slate-200 text-xs p-2.5 w-48 sm:w-60 focus:border-brand-primary" />
            <select name="specialty_id" class="rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary">
                <option value="">Todas las Especialidades</option>
                @foreach($specialties as $s)
                    <option value="{{ $s->id }}" {{ $specialtyId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-brand-primary text-white font-bold text-xs shadow-sm">
                Filtrar
            </button>
        </form>
    </div>

    <!-- DOCTORS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($doctors as $doc)
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-primary to-brand-primary-dark text-white flex items-center justify-center font-bold text-lg shadow-md shrink-0">
                                {{ substr($doc->user->name, 0, 2) }}
                            </div>
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-brand-primary">
                                    {{ $doc->specialties->first()?->name ?? 'General' }}
                                </span>
                                <h3 class="font-bold text-slate-900 text-base">{{ $doc->user->name }}</h3>
                                <p class="text-xs text-slate-400 font-mono">Reg. {{ $doc->license_number }}</p>
                            </div>
                        </div>

                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $doc->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            {{ $doc->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-500 line-clamp-2 mb-4 leading-relaxed">
                        {{ $doc->biography ?: 'Especialista clínico comprometido con el cuidado y la salud integral de nuestros pacientes.' }}
                    </p>

                    <!-- Weekly Schedules Badges -->
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 mb-4">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Días de Atención:</span>
                        @if($doc->schedules->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5">
                                @php
                                    $dayNames = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 0 => 'Dom'];
                                @endphp
                                @foreach($doc->schedules as $sch)
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-white text-slate-700 border border-slate-200">
                                        {{ $dayNames[$sch->day_of_week] ?? 'Día' }}: {{ substr($sch->start_time, 0, 5) }}-{{ substr($sch->end_time, 0, 5) }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-xs text-slate-400 italic">Sin horarios configurados</span>
                        @endif
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500 font-medium">Turnos: {{ $doc->consultation_duration_minutes }} min</span>
                    <span class="font-bold text-slate-900">{{ $doc->appointments_count }} citas atendidas</span>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 text-slate-400">
                No se encontraron especialistas con los filtros ingresados.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $doctors->links() }}
    </div>
</div>
@endsection
