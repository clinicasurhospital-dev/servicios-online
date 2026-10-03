@extends('layouts.admin')

@section('title', "Diseñador: {$template->name} — SurHospital")

@section('content')
<div class="space-y-6" x-data="clinicalTemplateBuilder({
    templateId: {{ $template->id }},
    activeVersionId: {{ $activeVersion->id }},
    isPublished: {{ $activeVersion->isPublished() ? 'true' : 'false' }},
    sections: {{ Js::from($activeVersion->sections()->with('fields.options')->get()) }},
    csrfToken: '{{ csrf_token() }}'
})">
    <!-- Top Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.clinical.templates.index') }}" class="text-xs font-semibold text-slate-500 hover:text-brand-600 flex items-center gap-1 transition">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Volver a Plantillas</span>
                </a>
                <span class="text-xs text-slate-300">•</span>
                <span class="text-xs font-semibold text-brand-600 bg-brand-50 px-2 py-0.5 rounded-full border border-brand-200">
                    {{ $template->specialty ? $template->specialty->name : 'General' }}
                </span>
            </div>
            <div class="flex items-center gap-3 mt-1">
                <h1 class="text-2xl font-bold text-slate-900">{{ $template->name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $activeVersion->status->badgeColor() }}">
                    v{{ $activeVersion->version_number }} ({{ $activeVersion->status->label() }})
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">{{ $template->description ?: 'Sin descripción detallada.' }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button
                type="button"
                @click="openPreviewModal()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition active:scale-95"
            >
                <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span>Vista Previa en Vivo</span>
            </button>

            @if($activeVersion->isPublished())
                <form method="POST" action="{{ route('admin.clinical.templates.new_version', ['template' => $template->id]) }}" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition active:scale-95"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Crear Versión v{{ $activeVersion->version_number + 1 }} en Borrador</span>
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.clinical.templates.publish', ['version' => $activeVersion->id]) }}" class="inline" onsubmit="return confirm('¿Está seguro de publicar la versión v{{ $activeVersion->version_number }}? Una vez publicada será inmutable para proteger la integridad médico-legal.')">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition active:scale-95"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Publicar Versión v{{ $activeVersion->version_number }}</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if($activeVersion->isPublished())
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-3">
            <svg class="h-5 w-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="text-xs text-amber-800">
                <span class="font-bold">Plantilla Publicada e Inmutable:</span> Esta versión está actualmente activa en atenciones asistenciales. Para realizar modificaciones estructurales, haga clic en <strong>"Crear Versión v{{ $activeVersion->version_number + 1 }} en Borrador"</strong> arriba.
            </div>
        </div>
    @endif

    <!-- 3-Column Template Builder Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- COLUMN 1: Secciones (Left Panel - 4 cols on lg) -->
        <div class="lg:col-span-4 bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">1. Secciones</h2>
                    <p class="text-[11px] text-slate-400">Páginas o módulos del formulario</p>
                </div>
                @if(!$activeVersion->isPublished())
                    <button
                        type="button"
                        @click="openAddSectionModal()"
                        class="p-1.5 bg-brand-50 hover:bg-brand-100 text-brand-600 rounded-lg text-xs font-bold transition flex items-center gap-1"
                        title="Agregar Sección"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Sección</span>
                    </button>
                @endif
            </div>

            <!-- Sections List -->
            <div class="space-y-2">
                <template x-for="(section, index) in localSections" :key="section.id">
                    <div
                        @click="selectSection(section)"
                        class="p-3.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between group"
                        :class="selectedSection?.id === section.id ? 'bg-brand-50/70 border-brand-500 ring-1 ring-brand-500 shadow-xs' : 'bg-slate-50/60 border-slate-200 hover:bg-slate-100/70'"
                    >
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="flex flex-col gap-0.5 text-slate-400">
                                <span class="text-xs font-bold" x-text="index + 1"></span>
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-slate-900 truncate" x-text="section.name"></div>
                                <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                                    <span x-text="`${(section.fields || []).length} campos`"></span>
                                    <span x-show="section.is_required" class="text-amber-600 font-semibold">• Obligatoria</span>
                                    <span x-show="section.is_repeatable" class="text-blue-600 font-semibold">• Repetible</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-1" x-show="!isPublished">
                            <button
                                type="button"
                                @click.stop="moveSectionUp(index)"
                                :disabled="index === 0"
                                class="p-1 text-slate-400 hover:text-slate-700 disabled:opacity-20"
                                title="Subir"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            </button>
                            <button
                                type="button"
                                @click.stop="moveSectionDown(index)"
                                :disabled="index === localSections.length - 1"
                                class="p-1 text-slate-400 hover:text-slate-700 disabled:opacity-20"
                                title="Bajar"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <button
                                type="button"
                                @click.stop="deleteSection(section)"
                                class="p-1 text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition"
                                title="Eliminar Sección"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                <div x-show="localSections.length === 0" class="p-6 text-center text-slate-400 text-xs border-2 border-dashed border-slate-200 rounded-xl">
                    No hay secciones aún. Haga clic en "+ Sección" para comenzar.
                </div>
            </div>
        </div>

        <!-- COLUMN 2: Campos de la Sección (Center Panel - 4 cols on lg) -->
        <div class="lg:col-span-4 bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="min-w-0">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider truncate" x-text="selectedSection ? selectedSection.name : 'Campos'"></h2>
                    <p class="text-[11px] text-slate-400">2. Campos configurados</p>
                </div>
                <template x-if="selectedSection && !isPublished">
                    <button
                        type="button"
                        @click="openAddFieldModal()"
                        class="p-1.5 bg-brand-50 hover:bg-brand-100 text-brand-600 rounded-lg text-xs font-bold transition flex items-center gap-1"
                        title="Agregar Campo"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Campo</span>
                    </button>
                </template>
            </div>

            <!-- Fields List in Selected Section -->
            <div class="space-y-2" x-show="selectedSection">
                <template x-for="(field, index) in (selectedSection?.fields || [])" :key="field.id">
                    <div
                        @click="selectField(field)"
                        class="p-3.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between group"
                        :class="selectedField?.id === field.id ? 'bg-indigo-50/70 border-indigo-500 ring-1 ring-indigo-500 shadow-xs' : 'bg-slate-50/60 border-slate-200 hover:bg-slate-100/70'"
                    >
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-bold text-slate-900 truncate" x-text="field.label"></span>
                                <span x-show="field.is_required" class="text-red-500 font-bold">*</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                <span class="px-1.5 py-0.5 bg-slate-200 text-slate-700 rounded text-[10px] font-mono font-medium" x-text="field.field_type"></span>
                                <span x-show="field.unit" class="px-1.5 py-0.5 bg-blue-50 text-blue-700 rounded text-[10px] font-semibold" x-text="field.unit"></span>
                                <span x-show="field.is_clinical_alert" class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-[10px] font-semibold">⚠️ Alerta</span>
                                <span x-show="field.is_sensitive" class="px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded text-[10px] font-semibold">🔒 Sensible</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1" x-show="!isPublished">
                            <button
                                type="button"
                                @click.stop="moveFieldUp(index)"
                                :disabled="index === 0"
                                class="p-1 text-slate-400 hover:text-slate-700 disabled:opacity-20"
                                title="Subir"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            </button>
                            <button
                                type="button"
                                @click.stop="moveFieldDown(index)"
                                :disabled="index === (selectedSection?.fields || []).length - 1"
                                class="p-1 text-slate-400 hover:text-slate-700 disabled:opacity-20"
                                title="Bajar"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <button
                                type="button"
                                @click.stop="deleteField(field)"
                                class="p-1 text-red-400 hover:text-red-600 opacity-0 group-hover:opacity-100 transition"
                                title="Eliminar Campo"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                <div x-show="!selectedSection || (selectedSection.fields || []).length === 0" class="p-6 text-center text-slate-400 text-xs border-2 border-dashed border-slate-200 rounded-xl">
                    No hay campos en esta sección. Haga clic en "+ Campo" para agregar variables clínicas.
                </div>
            </div>
        </div>

        <!-- COLUMN 3: Inspector de Propiedades de Campo (Right Panel - 4 cols on lg) -->
        <div class="lg:col-span-4 bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-4">
            <div class="pb-3 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">3. Inspector de Campo</h2>
                <p class="text-[11px] text-slate-400">Parámetros, validaciones y opciones</p>
            </div>

            <template x-if="selectedField">
                <form @submit.prevent="saveFieldProperties()" class="space-y-3.5 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Etiqueta del Campo (Label)</label>
                        <input
                            type="text"
                            x-model="selectedField.label"
                            :disabled="isPublished"
                            required
                            class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none text-xs"
                        >
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Código de Variable (Slug/Key)</label>
                        <input
                            type="text"
                            x-model="selectedField.code"
                            :disabled="isPublished"
                            required
                            class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 font-mono text-xs outline-none"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Placeholder</label>
                            <input
                                type="text"
                                x-model="selectedField.placeholder"
                                :disabled="isPublished"
                                class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none text-xs"
                            >
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Unidad (ej. mmHg, kg)</label>
                            <input
                                type="text"
                                x-model="selectedField.unit"
                                :disabled="isPublished"
                                class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none text-xs"
                            >
                        </div>
                    </div>

                    <!-- Numeric Rules -->
                    <template x-if="['INTEGER', 'DECIMAL'].includes(selectedField.field_type)">
                        <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-100 space-y-2">
                            <div class="font-bold text-blue-900 text-[11px] uppercase tracking-wider">Reglas Numéricas</div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-[11px] text-blue-700">Mínimo</label>
                                    <input
                                        type="number"
                                        x-model="selectedField.validation_rules.min"
                                        :disabled="isPublished"
                                        class="w-full px-2.5 py-1 bg-white border border-blue-200 rounded-lg text-xs"
                                    >
                                </div>
                                <div>
                                    <label class="text-[11px] text-blue-700">Máximo</label>
                                    <input
                                        type="number"
                                        x-model="selectedField.validation_rules.max"
                                        :disabled="isPublished"
                                        class="w-full px-2.5 py-1 bg-white border border-blue-200 rounded-lg text-xs"
                                    >
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Options Editor for Select, Radio, Multiselect, Checkbox -->
                    <template x-if="['SELECT', 'RADIO', 'MULTISELECT', 'CHECKBOX'].includes(selectedField.field_type)">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800 text-[11px] uppercase tracking-wider">Opciones del Desplegable</span>
                                <button
                                    type="button"
                                    @click="addOptionToField()"
                                    :disabled="isPublished"
                                    class="text-[10px] font-bold text-brand-600 hover:text-brand-800"
                                >
                                    + Opción
                                </button>
                            </div>
                            <div class="space-y-1.5 max-h-36 overflow-y-auto">
                                <template x-for="(opt, oIndex) in (selectedField.options || [])" :key="oIndex">
                                    <div class="flex items-center gap-1.5">
                                        <input
                                            type="text"
                                            x-model="opt.label"
                                            :disabled="isPublished"
                                            placeholder="Etiqueta"
                                            class="flex-1 px-2 py-1 bg-white border border-slate-200 rounded text-xs"
                                        >
                                        <input
                                            type="text"
                                            x-model="opt.value"
                                            :disabled="isPublished"
                                            placeholder="Valor"
                                            class="w-20 px-2 py-1 bg-white border border-slate-200 rounded text-xs font-mono"
                                        >
                                        <button
                                            type="button"
                                            @click="removeOptionFromField(oIndex)"
                                            :disabled="isPublished"
                                            class="text-red-400 hover:text-red-600 p-1"
                                        >
                                            &times;
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Flags & Checkboxes -->
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="selectedField.is_required" :disabled="isPublished" class="rounded text-brand-600 focus:ring-brand-500">
                            <span class="font-medium text-slate-700">Obligatorio al Finalizar</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="selectedField.is_clinical_alert" :disabled="isPublished" class="rounded text-red-600 focus:ring-red-500">
                            <span class="font-medium text-slate-700">Genera Alerta Clínica (Cabecera)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="selectedField.is_sensitive" :disabled="isPublished" class="rounded text-purple-600 focus:ring-purple-500">
                            <span class="font-medium text-slate-700">Dato Confidencial / Sensible</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="selectedField.is_patient_visible" :disabled="isPublished" class="rounded text-blue-600 focus:ring-blue-500">
                            <span class="font-medium text-slate-700">Visible en Portal del Paciente</span>
                        </label>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2" x-show="!isPublished">
                        <button
                            type="submit"
                            class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-xs transition"
                        >
                            Guardar Propiedades
                        </button>
                    </div>
                </form>
            </template>

            <div x-show="!selectedField" class="p-8 text-center text-slate-400 text-xs border-2 border-dashed border-slate-200 rounded-xl">
                Seleccione un campo del panel central para configurar sus propiedades y reglas.
            </div>
        </div>
    </div>

    <!-- Live Preview Modal -->
    <div x-show="showPreview" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <h3 class="text-base font-bold text-slate-900">Vista Previa Asistencial: {{ $template->name }}</h3>
                </div>
                <button type="button" @click="showPreview = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Preview Body with Section Tabs -->
            <div class="p-6 overflow-y-auto flex-1 space-y-6">
                <div class="flex flex-wrap gap-2 pb-3 border-b border-slate-100">
                    <template x-for="(sec, sIdx) in localSections" :key="sec.id">
                        <button
                            type="button"
                            @click="previewActiveSectionIndex = sIdx"
                            class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition"
                            :class="previewActiveSectionIndex === sIdx ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            x-text="`${sIdx + 1}. ${sec.name}`"
                        ></button>
                    </template>
                </div>

                <!-- Preview Form Fields of Active Section -->
                <template x-if="localSections[previewActiveSectionIndex]">
                    <div class="space-y-4">
                        <div class="bg-brand-50/50 p-4 rounded-xl border border-brand-100">
                            <h4 class="text-sm font-bold text-brand-900" x-text="localSections[previewActiveSectionIndex].name"></h4>
                            <p class="text-xs text-brand-700/80 mt-0.5" x-text="localSections[previewActiveSectionIndex].description || 'Complete los datos correspondientes a esta sección médica.'"></p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="field in (localSections[previewActiveSectionIndex].fields || [])" :key="field.id">
                                <div :class="['TEXTAREA', 'DIAGNOSIS', 'MEDICATION'].includes(field.field_type) ? 'md:col-span-2' : ''" class="space-y-1">
                                    <div class="flex items-center justify-between">
                                        <label class="text-xs font-semibold text-slate-800">
                                            <span x-text="field.label"></span>
                                            <span x-show="field.is_required" class="text-red-500 font-bold">*</span>
                                            <span x-show="field.unit" class="text-slate-400 font-normal" x-text="`(${field.unit})`"></span>
                                        </label>
                                        <span x-show="field.is_clinical_alert" class="text-[10px] text-red-600 font-bold">⚠️ Alerta Clínica</span>
                                    </div>

                                    <!-- Render Inputs by Type -->
                                    <template x-if="field.field_type === 'TEXT'">
                                        <input type="text" :placeholder="field.placeholder || ''" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none">
                                    </template>

                                    <template x-if="field.field_type === 'TEXTAREA'">
                                        <textarea rows="3" :placeholder="field.placeholder || ''" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none"></textarea>
                                    </template>

                                    <template x-if="['INTEGER', 'DECIMAL'].includes(field.field_type)">
                                        <input type="number" :step="field.field_type === 'DECIMAL' ? '0.01' : '1'" :placeholder="field.placeholder || ''" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none">
                                    </template>

                                    <template x-if="field.field_type === 'DATE'">
                                        <input type="date" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none">
                                    </template>

                                    <template x-if="field.field_type === 'SELECT'">
                                        <select class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none">
                                            <option value="">Seleccione una opción...</option>
                                            <template x-for="opt in (field.options || [])" :key="opt.value">
                                                <option :value="opt.value" x-text="opt.label"></option>
                                            </template>
                                        </select>
                                    </template>

                                    <template x-if="field.field_type === 'RADIO'">
                                        <div class="space-y-1.5 pt-1">
                                            <template x-for="opt in (field.options || [])" :key="opt.value">
                                                <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                                                    <input type="radio" :name="`radio_${field.id}`" class="text-brand-600 focus:ring-brand-500">
                                                    <span x-text="opt.label"></span>
                                                </label>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="field.field_type === 'BOOLEAN'">
                                        <label class="flex items-center gap-2 pt-1 cursor-pointer">
                                            <input type="checkbox" class="rounded text-brand-600 focus:ring-brand-500">
                                            <span class="text-xs font-semibold text-slate-700" x-text="field.label"></span>
                                        </label>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
            
            <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                <span class="text-xs text-slate-400">Modo de previsualización no interactivo de prueba.</span>
                <button type="button" @click="showPreview = false" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition">
                    Cerrar Previsualización
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('clinicalTemplateBuilder', (config) => ({
        templateId: config.templateId,
        activeVersionId: config.activeVersionId,
        isPublished: config.isPublished,
        localSections: config.sections,
        selectedSection: config.sections.length > 0 ? config.sections[0] : null,
        selectedField: null,
        csrfToken: config.csrfToken,
        showPreview: false,
        previewActiveSectionIndex: 0,

        init() {
            if (this.selectedSection && (this.selectedSection.fields || []).length > 0) {
                this.selectedField = this.selectedSection.fields[0];
            }
        },

        selectSection(sec) {
            this.selectedSection = sec;
            this.selectedField = (sec.fields && sec.fields.length > 0) ? sec.fields[0] : null;
        },

        selectField(f) {
            this.selectedField = f;
            if (!this.selectedField.validation_rules) {
                this.selectedField.validation_rules = {};
            }
        },

        openPreviewModal() {
            this.previewActiveSectionIndex = 0;
            this.showPreview = true;
        },

        async openAddSectionModal() {
            const name = prompt("Ingrese el nombre de la nueva sección (ej. Signos Vitales):");
            if (!name || !name.trim()) return;

            const res = await fetch(`/admin/configuracion/historia-clinica/versiones/${this.activeVersionId}/secciones`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ name: name.trim() })
            });
            const data = await res.json();
            if (data.success) {
                this.localSections.push(data.section);
                this.selectSection(data.section);
            }
        },

        async openAddFieldModal() {
            if (!this.selectedSection) return;
            const label = prompt("Nombre / Etiqueta del campo:");
            if (!label || !label.trim()) return;

            const fieldType = prompt("Tipo de campo (TEXT, TEXTAREA, INTEGER, DECIMAL, DATE, SELECT, RADIO, CHECKBOX):", "TEXT");
            if (!fieldType) return;

            const res = await fetch(`/admin/configuracion/historia-clinica/secciones/${this.selectedSection.id}/campos`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: label.trim(),
                    label: label.trim(),
                    field_type: fieldType.toUpperCase()
                })
            });
            const data = await res.json();
            if (data.success) {
                if (!this.selectedSection.fields) this.selectedSection.fields = [];
                this.selectedSection.fields.push(data.field);
                this.selectField(data.field);
            }
        },

        async saveFieldProperties() {
            if (!this.selectedField) return;
            const res = await fetch(`/admin/configuracion/historia-clinica/campos/${this.selectedField.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    label: this.selectedField.label,
                    code: this.selectedField.code,
                    placeholder: this.selectedField.placeholder,
                    unit: this.selectedField.unit,
                    is_required: this.selectedField.is_required,
                    is_repeatable: this.selectedField.is_repeatable,
                    is_sensitive: this.selectedField.is_sensitive,
                    is_clinical_alert: this.selectedField.is_clinical_alert,
                    is_patient_visible: this.selectedField.is_patient_visible,
                    help_text: this.selectedField.help_text,
                    validation_rules: this.selectedField.validation_rules,
                    options: this.selectedField.options
                })
            });
            const data = await res.json();
            if (data.success) {
                alert('Propiedades del campo guardadas correctamente.');
            } else {
                alert('Error al guardar: ' + data.message);
            }
        },

        addOptionToField() {
            if (!this.selectedField.options) this.selectedField.options = [];
            this.selectedField.options.push({ label: 'Nueva Opción', value: 'VAL_' + (this.selectedField.options.length + 1) });
        },

        removeOptionFromField(idx) {
            this.selectedField.options.splice(idx, 1);
        },

        async deleteSection(sec) {
            if (!confirm(`¿Desea eliminar la sección '${sec.name}' y todos sus campos?`)) return;
            const res = await fetch(`/admin/configuracion/historia-clinica/secciones/${sec.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success) {
                this.localSections = this.localSections.filter(s => s.id !== sec.id);
                this.selectedSection = this.localSections.length > 0 ? this.localSections[0] : null;
            }
        },

        async deleteField(f) {
            if (!confirm(`¿Desea eliminar el campo '${f.label}'?`)) return;
            const res = await fetch(`/admin/configuracion/historia-clinica/campos/${f.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
            });
            const data = await res.json();
            if (data.success) {
                this.selectedSection.fields = this.selectedSection.fields.filter(x => x.id !== f.id);
                this.selectedField = this.selectedSection.fields.length > 0 ? this.selectedSection.fields[0] : null;
            }
        },

        moveSectionUp(idx) {
            if (idx <= 0) return;
            const temp = this.localSections[idx];
            this.localSections[idx] = this.localSections[idx - 1];
            this.localSections[idx - 1] = temp;
            this.syncSectionOrder();
        },

        moveSectionDown(idx) {
            if (idx >= this.localSections.length - 1) return;
            const temp = this.localSections[idx];
            this.localSections[idx] = this.localSections[idx + 1];
            this.localSections[idx + 1] = temp;
            this.syncSectionOrder();
        },

        async syncSectionOrder() {
            const ids = this.localSections.map(s => s.id);
            await fetch(`/admin/configuracion/historia-clinica/versiones/${this.activeVersionId}/reordenar-secciones`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                body: JSON.stringify({ ordered_ids: ids })
            });
        },

        moveFieldUp(idx) {
            if (idx <= 0 || !this.selectedSection) return;
            const temp = this.selectedSection.fields[idx];
            this.selectedSection.fields[idx] = this.selectedSection.fields[idx - 1];
            this.selectedSection.fields[idx - 1] = temp;
            this.syncFieldOrder();
        },

        moveFieldDown(idx) {
            if (!this.selectedSection || idx >= this.selectedSection.fields.length - 1) return;
            const temp = this.selectedSection.fields[idx];
            this.selectedSection.fields[idx] = this.selectedSection.fields[idx + 1];
            this.selectedSection.fields[idx + 1] = temp;
            this.syncFieldOrder();
        },

        async syncFieldOrder() {
            const ids = this.selectedSection.fields.map(f => f.id);
            await fetch(`/admin/configuracion/historia-clinica/secciones/${this.selectedSection.id}/reordenar-campos`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken },
                body: JSON.stringify({ ordered_ids: ids })
            });
        }
    }));
});
</script>
@endsection
