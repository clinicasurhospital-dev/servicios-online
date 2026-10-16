@extends('layouts.admin')

@section('title', 'Especialidades & Procedimientos — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn" x-data="{ 
    searchQuery: '',
    newSpecialtyModal: false, 
    editSpecialtyModal: false,
    newProcedureModal: false, 
    editProcedureModal: false,
    newSubprocedureModal: false,
    editSubprocedureModal: false,
    selectedSpecialtyId: null, 
    selectedSpecialtyName: '',
    selectedProcedureId: null,
    selectedProcedureName: '',
    editSpecialtyData: { id: null, name: '', description: '', base_price: 40, default_duration_minutes: 30, has_procedures: false },
    editProcedureData: { id: null, specialty_id: null, name: '', description: '', price: 60, duration_minutes: 45, has_subprocedures: false },
    editSubprocedureData: { id: null, procedure_id: null, name: '', description: '', price: 60, duration_minutes: 45 },
    openProcedureModal(id, name) {
        this.selectedSpecialtyId = id;
        this.selectedSpecialtyName = name;
        this.newProcedureModal = true;
    },
    openEditSpecialtyModal(spec) {
        this.editSpecialtyData = {
            id: spec.id,
            name: spec.name || '',
            description: spec.description || '',
            base_price: spec.base_price || 40,
            default_duration_minutes: spec.default_duration_minutes || 30,
            has_procedures: !!spec.has_procedures
        };
        this.editSpecialtyModal = true;
    },
    openEditProcedureModal(proc) {
        this.editProcedureData = {
            id: proc.id,
            specialty_id: proc.specialty_id,
            name: proc.name || '',
            description: proc.description || '',
            price: proc.price || 50,
            duration_minutes: proc.duration_minutes || 30,
            has_subprocedures: !!proc.has_subprocedures
        };
        this.editProcedureModal = true;
    },
    openSubprocedureModal(id, name) {
        this.selectedProcedureId = id;
        this.selectedProcedureName = name;
        this.newSubprocedureModal = true;
    },
    openEditSubprocedureModal(subproc) {
        this.editSubprocedureData = {
            id: subproc.id,
            procedure_id: subproc.procedure_id,
            name: subproc.name || '',
            description: subproc.description || '',
            price: subproc.price || 50,
            duration_minutes: subproc.duration_minutes || 30
        };
        this.editSubprocedureModal = true;
    }
}">
    <!-- Header -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex-1">
            <h1 class="text-xl font-bold text-slate-900">Especialidades & Procedimientos Clínicos</h1>
            <p class="text-xs text-slate-500">Configuración de catálogo de servicios médicos, tarifas base y tiempos de atención.</p>
        </div>

        <div class="flex-1 max-w-sm w-full relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" x-model="searchQuery" placeholder="Buscar especialidad..." class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary transition-all">
        </div>

        <button type="button" @click="newSpecialtyModal = true" class="px-4 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 flex items-center space-x-2 transition-all cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            <span>+ Nueva Especialidad</span>
        </button>
    </div>

    <!-- SPECIALTIES LIST -->
    <div class="space-y-6">
        @foreach($specialties as $spec)
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-sm space-y-6" x-show="searchQuery === '' || '{{ strtolower(addslashes($spec->name)) }}'.includes(searchQuery.toLowerCase())">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold text-xl shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h2 class="text-lg font-bold text-slate-900">{{ $spec->name }}</h2>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $spec->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $spec->is_active ? 'Activa' : 'Inactiva' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1 max-w-2xl">{{ $spec->description ?: 'Atención especializada en Clínica Sur Hospital.' }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="text-left sm:text-right">
                            @if(!$spec->has_procedures)
                            <span class="text-xs text-slate-400 block">Tarifa Consulta Base:</span>
                            <span class="text-xl font-black text-brand-primary">${{ number_format($spec->base_price, 2) }}</span>
                            @else
                            <span class="text-xs text-slate-400 block mb-1">Precio en sub-nivel</span>
                            @endif
                            <span class="text-[11px] text-slate-400 block">{{ $spec->default_duration_minutes }} min por turno</span>
                        </div>

                        <div class="flex flex-wrap items-center gap-1.5">
                            <button type="button" @click="openEditSpecialtyModal({{ json_encode($spec) }})" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-brand-soft hover:text-brand-primary text-slate-700 font-bold text-xs transition-colors cursor-pointer">
                                Editar
                            </button>
                            <form method="POST" action="{{ route('admin.specialties.toggle', $spec) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-2 rounded-xl {{ $spec->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} font-bold text-xs transition-colors cursor-pointer">
                                    {{ $spec->is_active ? 'Pausar' : 'Activar' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.specialties.destroy', $spec) }}" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta especialidad?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer" title="Eliminar especialidad">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                            @if($spec->has_procedures)
                            <button type="button" @click="openProcedureModal({{ $spec->id }}, '{{ addslashes($spec->name) }}')" class="px-3.5 py-2 rounded-xl bg-brand-soft text-brand-primary font-bold text-xs hover:bg-brand-100 transition-colors flex items-center space-x-1.5 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                <span>+ Procedimiento</span>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Procedures Grid for this Specialty -->
                @if($spec->has_procedures)
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Procedimientos y Servicios Asociados ({{ $spec->allProcedures->count() }})</h3>

                    @if($spec->allProcedures->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($spec->allProcedures as $proc)
                                <div class="p-5 rounded-2xl {{ $proc->is_active ? 'bg-slate-50/70 border-slate-100' : 'bg-rose-50/30 border-rose-100 opacity-75' }} border flex flex-col">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                                {{ $proc->name }}
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $proc->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                    {{ $proc->is_active ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </h4>
                                            <p class="text-[11px] text-slate-500 mt-1 line-clamp-2">{{ $proc->description ?: 'Procedimiento clínico' }}</p>
                                        </div>
                                        <div class="flex items-center space-x-1 shrink-0">
                                            @if($proc->has_subprocedures)
                                            <button type="button" @click="openSubprocedureModal({{ $proc->id }}, '{{ addslashes($proc->name) }}')" class="p-1.5 bg-brand-soft text-brand-primary hover:bg-brand-primary hover:text-white rounded-lg transition-colors cursor-pointer" title="Agregar subprocedimiento">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </button>
                                            @endif
                                            <button type="button" @click="openEditProcedureModal({{ json_encode($proc) }})" class="p-1.5 text-slate-500 hover:bg-slate-200 rounded-lg transition-colors cursor-pointer" title="Editar">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>
                                            <form method="POST" action="{{ route('admin.procedures.destroy', $proc) }}" class="inline" onsubmit="return confirm('¿Eliminar procedimiento?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-100 rounded-lg transition-colors cursor-pointer" title="Eliminar">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px] font-medium">
                                        <div class="flex items-center space-x-2 text-slate-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>{{ $proc->duration_minutes }} min</span>
                                        </div>
                                        @if(!$proc->has_subprocedures)
                                        <div class="font-bold text-brand-primary text-sm">${{ number_format($proc->price, 2) }}</div>
                                        @else
                                        <div class="text-slate-400 text-[10px]">Precio en sub-nivel</div>
                                        @endif
                                    </div>

                                    @if($proc->has_subprocedures && $proc->subprocedures->isNotEmpty())
                                    <div class="mt-3 pt-3 border-t border-slate-200 space-y-2">
                                        @foreach($proc->subprocedures as $subproc)
                                        <div class="flex items-center justify-between p-2 rounded-xl bg-white border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                                            <div>
                                                <h5 class="text-xs font-bold text-slate-800">{{ $subproc->name }}</h5>
                                                <span class="text-[10px] text-slate-400">{{ $subproc->duration_minutes }} min</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-brand-primary text-xs">${{ number_format($subproc->price, 2) }}</span>
                                                <button type="button" @click="openEditSubprocedureModal({{ json_encode($subproc) }})" class="p-1 text-slate-400 hover:text-brand-primary transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                <form method="POST" action="{{ route('admin.subprocedures.destroy', $subproc) }}" class="inline" onsubmit="return confirm('¿Eliminar subprocedimiento?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1 text-rose-300 hover:text-rose-500 transition-colors">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-400 italic">No hay procedimientos adicionales registrados para esta especialidad.</p>
                    @endif
                </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- MODAL: NUEVA ESPECIALIDAD -->
    <div x-show="newSpecialtyModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
        <div @click.away="newSpecialtyModal = false" class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-slate-100 space-y-5 animate-scaleUp">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">Crear Nueva Especialidad Médica</h3>
                </div>
                <button type="button" @click="newSpecialtyModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.specialties.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nombre de la Especialidad *</label>
                    <input type="text" name="name" required placeholder="Ej. Neurología, Oftalmología..." class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Descripción</label>
                    <textarea name="description" rows="2" placeholder="Breve descripción de las atenciones clínicas comprendidas..." class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Precio Consulta Base ($) <span x-show="!$refs.has_proc_checkbox.checked">*</span></label>
                        <input type="number" step="0.5" name="base_price" :required="!$refs.has_proc_checkbox.checked" value="40.00" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary disabled:bg-slate-50 disabled:text-slate-400" :disabled="$refs.has_proc_checkbox.checked" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Duración (Minutos) *</label>
                        <input type="number" name="default_duration_minutes" required value="30" min="10" step="5" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <input type="checkbox" name="has_procedures" id="has_procedures_new" value="1" x-ref="has_proc_checkbox" class="w-4 h-4 text-brand-primary rounded border-slate-300 focus:ring-brand-primary" @change="$dispatch('input')">
                    <label for="has_procedures_new" class="text-xs font-medium text-slate-700">Esta especialidad tiene procedimientos/servicios asociados</label>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2">
                    <button type="button" @click="newSpecialtyModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary text-white font-bold text-xs shadow-md shadow-brand-primary/20 hover:bg-brand-primary-dark cursor-pointer">Guardar Especialidad</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDITAR ESPECIALIDAD -->
    <div x-show="editSpecialtyModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
        <div @click.away="editSpecialtyModal = false" class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-slate-100 space-y-5 animate-scaleUp">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">Editar Especialidad Médica</h3>
                </div>
                <button type="button" @click="editSpecialtyModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" :action="'/admin/especialidades/' + editSpecialtyData.id" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nombre de la Especialidad *</label>
                    <input type="text" name="name" x-model="editSpecialtyData.name" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Descripción</label>
                    <textarea name="description" x-model="editSpecialtyData.description" rows="2" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Precio Consulta Base ($) <span x-show="!editSpecialtyData.has_procedures">*</span></label>
                        <input type="number" step="0.5" name="base_price" x-model="editSpecialtyData.base_price" :required="!editSpecialtyData.has_procedures" :disabled="editSpecialtyData.has_procedures" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary disabled:bg-slate-50 disabled:text-slate-400" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Duración (Minutos) *</label>
                        <input type="number" name="default_duration_minutes" x-model="editSpecialtyData.default_duration_minutes" required min="10" step="5" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <input type="checkbox" name="has_procedures" id="has_procedures_edit" value="1" x-model="editSpecialtyData.has_procedures" class="w-4 h-4 text-brand-primary rounded border-slate-300 focus:ring-brand-primary">
                    <label for="has_procedures_edit" class="text-xs font-medium text-slate-700">Esta especialidad tiene procedimientos/servicios asociados</label>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2">
                    <button type="button" @click="editSpecialtyModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary text-white font-bold text-xs shadow-md shadow-brand-primary/20 hover:bg-brand-primary-dark cursor-pointer">Actualizar Especialidad</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: NUEVO PROCEDIMIENTO -->
    <div x-show="newProcedureModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
        <div @click.away="newProcedureModal = false" class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-slate-100 space-y-5 animate-scaleUp">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Nuevo Procedimiento Clínico</h3>
                        <p class="text-xs text-slate-500">Para: <span class="font-bold text-brand-primary" x-text="selectedSpecialtyName"></span></p>
                    </div>
                </div>
                <button type="button" @click="newProcedureModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" :action="'/admin/especialidades/' + selectedSpecialtyId + '/procedimientos'" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nombre del Procedimiento *</label>
                    <input type="text" name="name" required placeholder="Ej. Electrocardiograma, Endoscopía..." class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Descripción</label>
                    <textarea name="description" rows="2" placeholder="Detalle del procedimiento clínico o servicio..." class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Precio Total ($) <span x-show="!$refs.has_subproc_checkbox.checked">*</span></label>
                        <input type="number" step="0.5" name="price" :required="!$refs.has_subproc_checkbox.checked" :disabled="$refs.has_subproc_checkbox.checked" value="60.00" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary disabled:bg-slate-50 disabled:text-slate-400" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Duración (Minutos) *</label>
                        <input type="number" name="duration_minutes" required value="45" min="10" step="5" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <input type="checkbox" name="has_subprocedures" id="has_subprocedures_new" value="1" x-ref="has_subproc_checkbox" class="w-4 h-4 text-brand-primary rounded border-slate-300 focus:ring-brand-primary" @change="$dispatch('input')">
                    <label for="has_subprocedures_new" class="text-xs font-medium text-slate-700">Este procedimiento tiene subprocedimientos</label>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2">
                    <button type="button" @click="newProcedureModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary text-white font-bold text-xs shadow-md shadow-brand-primary/20 hover:bg-brand-primary-dark cursor-pointer">Guardar Procedimiento</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDITAR PROCEDIMIENTO -->
    <div x-show="editProcedureModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
        <div @click.away="editProcedureModal = false" class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-slate-100 space-y-5 animate-scaleUp">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Editar Procedimiento Clínico</h3>
                        <p class="text-xs text-slate-500" x-text="editProcedureData.name"></p>
                    </div>
                </div>
                <button type="button" @click="editProcedureModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" :action="'/admin/procedimientos/' + editProcedureData.id" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nombre del Procedimiento *</label>
                    <input type="text" name="name" x-model="editProcedureData.name" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Descripción</label>
                    <textarea name="description" x-model="editProcedureData.description" rows="2" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Precio Total ($) <span x-show="!editProcedureData.has_subprocedures">*</span></label>
                        <input type="number" step="0.5" name="price" x-model="editProcedureData.price" :required="!editProcedureData.has_subprocedures" :disabled="editProcedureData.has_subprocedures" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary disabled:bg-slate-50 disabled:text-slate-400" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Duración (Minutos) *</label>
                        <input type="number" name="duration_minutes" x-model="editProcedureData.duration_minutes" required min="10" step="5" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <div class="flex items-center space-x-2 pt-2">
                    <input type="checkbox" name="has_subprocedures" id="has_subprocedures_edit" value="1" x-model="editProcedureData.has_subprocedures" class="w-4 h-4 text-brand-primary rounded border-slate-300 focus:ring-brand-primary">
                    <label for="has_subprocedures_edit" class="text-xs font-medium text-slate-700">Este procedimiento tiene subprocedimientos</label>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2">
                    <button type="button" @click="editProcedureModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary text-white font-bold text-xs shadow-md shadow-brand-primary/20 hover:bg-brand-primary-dark cursor-pointer">Actualizar Procedimiento</button>
                </div>
            </form>
        </div>
    </div>
    <!-- MODAL: NUEVO SUBPROCEDIMIENTO -->
    <div x-show="newSubprocedureModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
        <div @click.away="newSubprocedureModal = false" class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-slate-100 space-y-5 animate-scaleUp">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Nuevo Subprocedimiento</h3>
                        <p class="text-xs text-slate-500">Para: <span class="font-bold text-brand-primary" x-text="selectedProcedureName"></span></p>
                    </div>
                </div>
                <button type="button" @click="newSubprocedureModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" :action="'/admin/procedimientos/' + selectedProcedureId + '/subprocedimientos'" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nombre del Subprocedimiento *</label>
                    <input type="text" name="name" required placeholder="Ej. Variante A, con contraste..." class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Descripción</label>
                    <textarea name="description" rows="2" placeholder="Detalle del subprocedimiento..." class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Precio Total ($) *</label>
                        <input type="number" step="0.5" name="price" required value="40.00" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Duración (Minutos) *</label>
                        <input type="number" name="duration_minutes" required value="30" min="10" step="5" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2">
                    <button type="button" @click="newSubprocedureModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary text-white font-bold text-xs shadow-md shadow-brand-primary/20 hover:bg-brand-primary-dark cursor-pointer">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDITAR SUBPROCEDIMIENTO -->
    <div x-show="editSubprocedureModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
        <div @click.away="editSubprocedureModal = false" class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-slate-100 space-y-5 animate-scaleUp">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Editar Subprocedimiento</h3>
                        <p class="text-xs text-slate-500" x-text="editSubprocedureData.name"></p>
                    </div>
                </div>
                <button type="button" @click="editSubprocedureModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" :action="'/admin/subprocedimientos/' + editSubprocedureData.id" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nombre del Subprocedimiento *</label>
                    <input type="text" name="name" x-model="editSubprocedureData.name" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Descripción</label>
                    <textarea name="description" x-model="editSubprocedureData.description" rows="2" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Precio Total ($) *</label>
                        <input type="number" step="0.5" name="price" x-model="editSubprocedureData.price" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Duración (Minutos) *</label>
                        <input type="number" name="duration_minutes" x-model="editSubprocedureData.duration_minutes" required min="10" step="5" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2">
                    <button type="button" @click="editSubprocedureModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary text-white font-bold text-xs shadow-md shadow-brand-primary/20 hover:bg-brand-primary-dark cursor-pointer">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
