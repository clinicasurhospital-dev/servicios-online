@extends('layouts.admin')

@section('title', 'Plantillas de Historia Clínica — SurHospital')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-brand-50 text-brand-600 border border-brand-200">
                    Configuración Asistencial
                </span>
                <span class="text-xs text-slate-400">•</span>
                <span class="text-xs text-slate-500 font-medium">SurHospital EHR Engine</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 mt-1">Plantillas de Historia Clínica</h1>
            <p class="text-sm text-slate-500 mt-0.5">Diseñe y versione formularios clínicos dinámicos por especialidad médica sin alterar código.</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                type="button"
                onclick="document.getElementById('newTemplateModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-150 active:scale-95"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nueva Plantilla</span>
            </button>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.clinical.templates.index') }}" class="flex flex-wrap items-center gap-3 w-full md:w-auto flex-1">
            <div class="relative flex-1 min-w-[240px]">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Buscar por nombre o descripción..."
                    class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition"
                >
                <svg class="h-4 w-4 text-slate-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <select
                name="specialty_id"
                class="px-3 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition"
                onchange="this.form.submit()"
            >
                <option value="">Todas las Especialidades</option>
                @foreach($specialties as $sp)
                    <option value="{{ $sp->id }}" {{ request('specialty_id') == $sp->id ? 'selected' : '' }}>{{ $sp->name }}</option>
                @endforeach
            </select>

            @if(request()->anyFilled(['search', 'specialty_id']))
                <a href="{{ route('admin.clinical.templates.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 bg-slate-100 rounded-xl transition">
                    Limpiar Filtros
                </a>
            @endif
        </form>
    </div>

    <!-- Templates Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/75 border-b border-slate-100 text-slate-500 text-xs uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-4">Plantilla / Especialidad</th>
                        <th class="px-6 py-4">Versión Activa</th>
                        <th class="px-6 py-4">Secciones / Campos</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4">Última Modificación</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($templates as $template)
                        @php
                            $activeVer = $template->publishedVersion ?: $template->latestVersion;
                            $sectionsCount = $activeVer ? $activeVer->sections->count() : 0;
                            $fieldsCount = $activeVer ? $activeVer->sections->flatMap->fields->count() : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $template->name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                                    <span class="inline-block w-2 h-2 rounded-full bg-brand-500"></span>
                                    {{ $template->specialty ? $template->specialty->name : 'General / Multiespecialidad' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($activeVer)
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $activeVer->status->badgeColor() }}">
                                        <span>v{{ $activeVer->version_number }}</span>
                                        <span>({{ $activeVer->status->label() }})</span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Sin versiones</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-900 font-medium">{{ $sectionsCount }} secciones</div>
                                <div class="text-xs text-slate-500">{{ $fieldsCount }} campos configurados</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($template->is_active)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Activa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-600">
                                        Inactiva
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $template->updated_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a
                                        href="{{ route('admin.clinical.templates.builder', ['template' => $template->id]) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-700 text-xs font-semibold rounded-lg transition"
                                        title="Abrir Diseñador de Plantilla"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span>Diseñador</span>
                                    </a>

                                    <form method="POST" action="{{ route('admin.clinical.templates.duplicate', ['template' => $template->id]) }}" class="inline" onsubmit="return confirm('¿Desea duplicar esta plantilla?')">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition"
                                            title="Duplicar Plantilla"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <svg class="h-12 w-12 mx-auto text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-base font-semibold text-slate-700">No se encontraron plantillas clínicas</p>
                                <p class="text-xs text-slate-400 mt-1">Cree una nueva plantilla o modifique los filtros de búsqueda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($templates->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Nueva Plantilla -->
<div id="newTemplateModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 relative animate-in fade-in zoom-in-95 duration-200">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-900">Crear Nueva Plantilla Clínica</h3>
            <button type="button" onclick="document.getElementById('newTemplateModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.clinical.templates.store') }}" class="mt-4 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Nombre de la Plantilla <span class="text-red-500">*</span></label>
                <input
                    type="text"
                    name="name"
                    required
                    placeholder="Ej. Cardiología Ambulatoria Especializada"
                    class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition"
                >
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Especialidad Asignada</label>
                <select
                    name="specialty_id"
                    class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition"
                >
                    <option value="">General / Todas las especialidades</option>
                    @foreach($specialties as $sp)
                        <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-400 mt-1">Si no selecciona una especialidad, servirá como plantilla general por defecto.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Descripción / Objetivo Clínico</label>
                <textarea
                    name="description"
                    rows="3"
                    placeholder="Detalle el alcance de esta historia clínica y sus guías de llenado..."
                    class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition"
                ></textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button
                    type="button"
                    onclick="document.getElementById('newTemplateModal').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="px-5 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-xs transition"
                >
                    Crear y Abrir Diseñador
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
