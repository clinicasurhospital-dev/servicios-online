@extends('layouts.admin')

@section('title', 'Directorio de Pacientes — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn">
    <!-- Header with Search -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Directorio de Pacientes</h1>
            <p class="text-xs text-slate-500">Historial y datos de contacto de los pacientes registrados en la institución.</p>
        </div>

        <form method="GET" action="{{ route('admin.patients') }}" class="flex items-center space-x-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre, CC, teléfono o email..." class="rounded-xl border-slate-200 text-xs p-2.5 w-64 sm:w-80 focus:border-brand-primary" />
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-brand-primary text-white font-bold text-xs shadow-sm">
                Buscar
            </button>
            @if($search)
                <a href="{{ route('admin.patients') }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-500 text-xs font-bold hover:bg-slate-50">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <!-- PATIENT TABLE -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-4 px-6">Paciente</th>
                        <th class="py-4 px-6">Identificación</th>
                        <th class="py-4 px-6">Contacto</th>
                        <th class="py-4 px-6">Citas Registradas</th>
                        <th class="py-4 px-6">Fecha Registro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($patients as $p)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ substr($p->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block text-sm">{{ $p->first_name }} {{ $p->last_name }}</span>
                                        <span class="text-slate-400 text-[11px]">{{ $p->user?->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-800">{{ $p->identification_type->value ?? 'CC' }}</span>
                                <span class="text-slate-600 block text-[11px] font-mono">{{ $p->identification_number }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-slate-800 font-medium block">{{ $p->phone }}</span>
                                <span class="text-slate-400 text-[11px]">Nacimiento: {{ $p->birth_date?->format('d/m/Y') ?? 'N/D' }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-brand-soft text-brand-primary">
                                    {{ $p->appointments_count }} citas
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                {{ $p->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-400">
                                No se encontraron pacientes con los criterios de búsqueda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $patients->links() }}
        </div>
    </div>
</div>
@endsection
