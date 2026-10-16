@extends('layouts.admin')

@section('title', "Diseñador: {$template->name} — SurHospital")

@section('content')
<div class="space-y-6" x-data="clinicalTemplateBuilder({
    templateId: {{ $template->id }},
    activeVersionId: {{ $activeVersion->id }},
    isPublished: {{ $activeVersion->isPublished() ? 'true' : 'false' }},
    sections: {{ Js::from($activeVersion->sections()->with('fields.options')->orderBy('order')->get()) }},
    csrfToken: '{{ csrf_token() }}'
})" x-cloak>

    <!-- Top Action Bar -->
    <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.clinical.templates.index') }}" class="text-xs font-bold text-slate-500 hover:text-brand-primary flex items-center gap-1.5 transition">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Volver al listado de plantillas</span>
                </a>
                <span class="text-slate-300">•</span>
                <span class="text-xs font-bold text-brand-primary bg-brand-soft px-3 py-0.5 rounded-full border border-blue-200">
                    {{ $template->specialty ? $template->specialty->name : 'General / Multiespecialidad' }}
                </span>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 mt-2">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900">{{ $template->name }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-black tracking-wide {{ $activeVersion->status->badgeColor() }}">
                    v{{ $activeVersion->version_number }} ({{ $activeVersion->status->label() }})
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1 max-w-2xl">{{ $template->description ?: 'Plantilla clínica configurable para atención asistencial.' }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Botón Ayuda / Guía rápida -->
            <button
                type="button"
                @click="showHelp = !showHelp"
                class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer"
            >
                <span>💡</span>
                <span x-text="showHelp ? 'Ocultar Guía' : '¿Cómo funciona?'"></span>
            </button>

            <!-- Botón Vista Previa en Vivo -->
            <button
                type="button"
                @click="openPreviewModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 hover:bg-blue-100 text-brand-primary text-xs font-bold rounded-xl border border-blue-200 transition cursor-pointer"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span>Vista Previa de Consulta</span>
            </button>

            @if($activeVersion->isPublished())
                <form method="POST" action="{{ route('admin.clinical.templates.new_version', ['template' => $template->id]) }}" class="inline">
                    @csrf
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-primary hover:bg-brand-primary-dark text-white text-xs font-bold rounded-xl shadow-md shadow-brand-primary/20 transition cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Crear Nueva Versión (v{{ $activeVersion->version_number + 1 }})</span>
                    </button>
                </form>
            @else
                <button
                    type="button"
                    @click="showPublishConfirm = true"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition cursor-pointer"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Publicar Plantilla</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Guía Rápida / Onboarding Banner (Colapsable) -->
    <div x-show="showHelp" x-transition class="p-5 bg-gradient-to-r from-blue-50 via-indigo-50/50 to-white rounded-3xl border border-blue-200 shadow-xs space-y-3">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-black text-brand-primary flex items-center gap-2">
                <span>📘</span>
                <span>Guía Rápida: ¿Cómo configurar tu Historia Clínica?</span>
            </h3>
            <button type="button" @click="showHelp = false" class="text-slate-400 hover:text-slate-600 text-xs font-bold">Cerrar</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 text-xs text-slate-700 pt-1">
            <div class="bg-white p-3.5 rounded-2xl border border-blue-100 space-y-1">
                <span class="font-bold text-brand-primary block">1. Secciones (Pestañas)</span>
                <p class="text-slate-500 text-[11px]">Crea los bloques principales de la consulta, por ejemplo: <em>Signos Vitales</em>, <em>Motivo de Consulta</em>, <em>Examen Físico</em> o <em>Plan</em>.</p>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-blue-100 space-y-1">
                <span class="font-bold text-brand-primary block">2. Agregar Campos Visuales</span>
                <p class="text-slate-500 text-[11px]">Haz clic en <strong>+ Agregar Campo</strong> y elige si es un texto, medición numérica (con unidad kg, mmHg), fecha o lista desplegable.</p>
            </div>
            <div class="bg-white p-3.5 rounded-2xl border border-blue-100 space-y-1">
                <span class="font-bold text-brand-primary block">3. Revisa y Publica</span>
                <p class="text-slate-500 text-[11px]">Usa <strong>Vista Previa</strong> para ver cómo la verá el médico. Cuando esté lista, haz clic en <strong>Publicar Plantilla</strong>.</p>
            </div>
        </div>
    </div>

    <!-- Alerta de Versión Publicada e Inmutable -->
    @if($activeVersion->isPublished())
        <div class="p-4 bg-amber-50/90 border border-amber-200 rounded-2xl flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-base shrink-0">
                🔒
            </div>
            <div class="text-xs text-amber-900">
                <span class="font-bold">Plantilla Publicada e Inmutable:</span> Esta versión está activa para las consultas médicas. Para modificarla, presiona el botón superior <strong>"Crear Nueva Versión (v{{ $activeVersion->version_number + 1 }})"</strong>.
            </div>
        </div>
    @endif

    <!-- WORKSPACE: DISEÑO EN 2 PANELES FLUIDOS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- PANEL IZQUIERDO: SECCIONES (4 COLS) -->
        <div class="lg:col-span-4 bg-white p-5 rounded-3xl border border-slate-100 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span>📑</span>
                        <span>Secciones de la Consulta</span>
                    </h2>
                    <span class="text-[11px] text-slate-400" x-text="`${localSections.length} sección(es) configuradas`"></span>
                </div>

                @if(!$activeVersion->isPublished())
                    <button
                        type="button"
                        @click="openAddSectionModal()"
                        class="px-3 py-1.5 bg-brand-primary hover:bg-brand-primary-dark text-white rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-xs cursor-pointer"
                    >
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Nueva Sección</span>
                    </button>
                @endif
            </div>

            <!-- Lista de Secciones -->
            <div class="space-y-2">
                <template x-for="(section, index) in localSections" :key="section.id">
                    <div
                        @click="selectSection(section)"
                        class="p-3.5 rounded-2xl border transition-all cursor-pointer flex items-center justify-between group"
                        :class="selectedSection?.id === section.id 
                            ? 'bg-gradient-to-r from-blue-50/90 to-indigo-50/40 border-brand-primary ring-2 ring-brand-primary/20 shadow-xs' 
                            : 'bg-slate-50/70 border-slate-200/80 hover:bg-slate-100/80'"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <div 
                                class="w-7 h-7 rounded-xl flex items-center justify-center font-black text-xs shrink-0"
                                :class="selectedSection?.id === section.id ? 'bg-brand-primary text-white' : 'bg-slate-200 text-slate-700'"
                                x-text="index + 1"
                            ></div>
                            <div class="min-w-0">
                                <div class="text-xs font-bold text-slate-900 truncate" x-text="section.name"></div>
                                <div class="text-[11px] text-slate-500 flex flex-wrap items-center gap-1.5 mt-0.5">
                                    <span class="font-medium" x-text="`${(section.fields || []).length} campo(s)`"></span>
                                    <span x-show="section.is_required" class="text-rose-600 font-bold">• Obligatoria</span>
                                    <span x-show="section.is_repeatable" class="text-indigo-600 font-bold">• Repetible</span>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones de Sección -->
                        <div class="flex items-center gap-1" x-show="!isPublished">
                            <button
                                type="button"
                                @click.stop="openEditSectionModal(section)"
                                class="p-1.5 text-slate-400 hover:text-brand-primary hover:bg-white rounded-lg transition"
                                title="Editar configuración de sección"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </button>
                            <button
                                type="button"
                                @click.stop="moveSectionUp(index)"
                                :disabled="index === 0"
                                class="p-1.5 text-slate-400 hover:text-slate-700 disabled:opacity-20 hover:bg-white rounded-lg transition"
                                title="Subir orden"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            </button>
                            <button
                                type="button"
                                @click.stop="moveSectionDown(index)"
                                :disabled="index === localSections.length - 1"
                                class="p-1.5 text-slate-400 hover:text-slate-700 disabled:opacity-20 hover:bg-white rounded-lg transition"
                                title="Bajar orden"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                            <button
                                type="button"
                                @click.stop="confirmDeleteSection(section)"
                                class="p-1.5 text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                                title="Eliminar sección"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                <div x-show="localSections.length === 0" class="p-8 text-center text-slate-400 text-xs border-2 border-dashed border-slate-200 rounded-3xl space-y-2">
                    <span class="text-3xl block">📋</span>
                    <p class="font-bold text-slate-700">No hay secciones todavía</p>
                    <p class="text-slate-400 text-[11px]">Haz clic en <strong>"+ Nueva Sección"</strong> arriba para comenzar.</p>
                </div>
            </div>
        </div>

        <!-- PANEL DERECHO: CAMPOS DE LA SECCIÓN SELECCIONADA (8 COLS) -->
        <div class="lg:col-span-8 bg-white p-5 sm:p-6 rounded-3xl border border-slate-100 shadow-sm space-y-5">
            <template x-if="selectedSection">
                <div class="space-y-5">
                    <!-- Cabecera de la Sección Activa -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-black uppercase tracking-wider bg-blue-100 text-brand-primary px-2.5 py-0.5 rounded-full">
                                    Sección Activa
                                </span>
                                <span class="text-xs text-slate-400">•</span>
                                <span class="text-xs text-slate-500 font-medium" x-text="`${(selectedSection.fields || []).length} campos registrados`"></span>
                            </div>
                            <h2 class="text-lg sm:text-xl font-black text-slate-900 mt-1" x-text="selectedSection.name"></h2>
                            <p class="text-xs text-slate-500 mt-0.5" x-text="selectedSection.description ?: 'Sin descripción adicional.'"></p>
                        </div>

                        @if(!$activeVersion->isPublished())
                            <button
                                type="button"
                                @click="openFieldPaletteModal()"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-brand-primary hover:bg-brand-primary-dark text-white rounded-xl text-xs font-bold transition shadow-md shadow-brand-primary/20 cursor-pointer shrink-0"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>+ Agregar Campo a esta Sección</span>
                            </button>
                        @endif
                    </div>

                    <!-- Lista Visual de Campos -->
                    <div class="space-y-3">
                        <template x-for="(field, fIndex) in (selectedSection.fields || [])" :key="field.id">
                            <div class="p-4 rounded-2xl border border-slate-200/80 bg-slate-50/50 hover:bg-white hover:border-brand-primary/50 hover:shadow-xs transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3 group">
                                <div class="flex items-start gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-2xl bg-white border border-slate-200 text-slate-700 flex items-center justify-center text-base shrink-0 shadow-2xs">
                                        <span x-text="getFieldIcon(field.field_type)"></span>
                                    </div>

                                    <div class="min-w-0 space-y-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="text-xs sm:text-sm font-black text-slate-900 truncate" x-text="field.label"></h4>
                                            <span x-show="field.is_required" class="text-rose-600 font-bold text-xs" title="Campo Obligatorio">* Requerido</span>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <span class="px-2 py-0.5 bg-slate-200/80 text-slate-700 rounded-lg text-[10px] font-bold" x-text="getFieldTypeFriendlyName(field.field_type)"></span>
                                            
                                            <span x-show="field.unit" class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-lg text-[10px] font-bold" x-text="`Unidad: ${field.unit}`"></span>

                                            <span x-show="field.is_clinical_alert" class="px-2 py-0.5 bg-rose-100 text-rose-800 rounded-lg text-[10px] font-bold">
                                                ⚠️ Alerta Clínica
                                            </span>

                                            <span x-show="field.is_sensitive" class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded-lg text-[10px] font-bold">
                                                🔒 Confidencial
                                            </span>

                                            <span x-show="['SELECT', 'RADIO', 'MULTISELECT', 'CHECKBOX'].includes(field.field_type)" class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-lg text-[10px] font-bold" x-text="`${(field.options || []).length} opciones`"></span>
                                        </div>

                                        <p x-show="field.placeholder || field.help_text" class="text-[11px] text-slate-400 italic truncate" x-text="field.placeholder || field.help_text"></p>
                                    </div>
                                </div>

                                <!-- Botones de Acción para el Campo -->
                                <div class="flex items-center gap-1.5 shrink-0 self-end sm:self-center">
                                    <button
                                        type="button"
                                        @click="openEditFieldModal(field)"
                                        class="px-3 py-1.5 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 shadow-2xs transition flex items-center gap-1 cursor-pointer"
                                    >
                                        <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span>Editar</span>
                                    </button>

                                    @if(!$activeVersion->isPublished())
                                        <button
                                            type="button"
                                            @click="moveFieldUp(fIndex)"
                                            :disabled="fIndex === 0"
                                            class="p-1.5 bg-white text-slate-400 hover:text-slate-700 disabled:opacity-20 rounded-xl border border-slate-200 transition"
                                            title="Subir"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            @click="moveFieldDown(fIndex)"
                                            :disabled="fIndex === (selectedSection.fields || []).length - 1"
                                            class="p-1.5 bg-white text-slate-400 hover:text-slate-700 disabled:opacity-20 rounded-xl border border-slate-200 transition"
                                            title="Bajar"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            @click="confirmDeleteField(field)"
                                            class="p-1.5 bg-white text-rose-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl border border-slate-200 transition"
                                            title="Eliminar campo"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </template>

                        <!-- Empty State para campos -->
                        <div x-show="!selectedSection.fields || selectedSection.fields.length === 0" class="p-12 text-center border-2 border-dashed border-slate-200 rounded-3xl space-y-3">
                            <span class="text-4xl block">📝</span>
                            <h3 class="text-sm font-black text-slate-800">Esta sección aún no tiene campos</h3>
                            <p class="text-xs text-slate-400 max-w-md mx-auto">
                                Agrega las preguntas, signos vitales o campos clínicos que el médico deberá registrar en esta sección.
                            </p>
                            @if(!$activeVersion->isPublished())
                                <button
                                    type="button"
                                    @click="openFieldPaletteModal()"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-primary hover:bg-brand-primary-dark text-white rounded-xl text-xs font-bold transition shadow-md shadow-brand-primary/20 cursor-pointer"
                                >
                                    <span>+ Agregar Primer Campo</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </template>

            <div x-show="!selectedSection" class="p-12 text-center text-slate-400 text-xs">
                Selecciona una sección en el panel izquierdo para visualizar y configurar sus campos.
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 1: AGREGAR SECCIÓN CON PRESETS RÁPIDOS                            -->
    <!-- ======================================================================= -->
    <div x-show="showAddSectionModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 relative animate-scaleUp space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-xl">📑</span>
                    <h3 class="text-base font-black text-slate-900">Nueva Sección de Consulta</h3>
                </div>
                <button type="button" @click="showAddSectionModal = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <!-- Sugerencias Rápidas -->
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Sugerencias habituales:</label>
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="preset in ['Motivo de Consulta', 'Signos Vitales', 'Antecedentes Médicos', 'Examen Físico', 'Evolución Clínica', 'Diagnóstico y Plan', 'Procedimiento Quirúrgico']" :key="preset">
                        <button
                            type="button"
                            @click="newSectionForm.name = preset"
                            class="px-2.5 py-1 bg-slate-100 hover:bg-blue-50 hover:text-brand-primary text-slate-700 text-xs font-semibold rounded-lg transition"
                            x-text="preset"
                        ></button>
                    </template>
                </div>
            </div>

            <form @submit.prevent="submitAddSection()" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nombre de la Sección *</label>
                    <input
                        type="text"
                        x-model="newSectionForm.name"
                        required
                        placeholder="Ej. Signos Vitales y Medidas"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-primary text-xs outline-none"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Descripción / Instrucciones (opcional)</label>
                    <input
                        type="text"
                        x-model="newSectionForm.description"
                        placeholder="Guía breve para el profesional médico..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-primary text-xs outline-none"
                    >
                </div>

                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2 text-xs">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="newSectionForm.is_required" class="rounded text-brand-primary focus:ring-brand-primary">
                        <span class="font-bold text-slate-800">Sección obligatoria para finalizar la atención</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="newSectionForm.is_repeatable" class="rounded text-brand-primary focus:ring-brand-primary">
                        <span class="font-bold text-slate-800">Permitir repetir esta sección (múltiples registros)</span>
                    </label>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button
                        type="button"
                        @click="showAddSectionModal = false"
                        class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="px-5 py-2.5 bg-brand-primary hover:bg-brand-primary-dark text-white text-xs font-bold rounded-xl shadow-xs transition disabled:opacity-50"
                    >
                        <span x-text="isSubmitting ? 'Guardando...' : 'Crear Sección'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 2: EDITAR SECCIÓN                                                 -->
    <!-- ======================================================================= -->
    <div x-show="showEditSectionModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 relative animate-scaleUp space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-xl">✏️</span>
                    <h3 class="text-base font-black text-slate-900">Editar Sección</h3>
                </div>
                <button type="button" @click="showEditSectionModal = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <form @submit.prevent="submitEditSection()" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nombre de la Sección *</label>
                    <input
                        type="text"
                        x-model="editSectionForm.name"
                        required
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-primary text-xs outline-none"
                    >
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Descripción / Instrucciones</label>
                    <input
                        type="text"
                        x-model="editSectionForm.description"
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-primary text-xs outline-none"
                    >
                </div>

                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2 text-xs">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="editSectionForm.is_required" class="rounded text-brand-primary focus:ring-brand-primary">
                        <span class="font-bold text-slate-800">Sección obligatoria para finalizar</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="editSectionForm.is_repeatable" class="rounded text-brand-primary focus:ring-brand-primary">
                        <span class="font-bold text-slate-800">Permitir repetir esta sección</span>
                    </label>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button
                        type="button"
                        @click="showEditSectionModal = false"
                        class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="px-5 py-2.5 bg-brand-primary hover:bg-brand-primary-dark text-white text-xs font-bold rounded-xl shadow-xs transition disabled:opacity-50"
                    >
                        <span x-text="isSubmitting ? 'Guardando...' : 'Actualizar Sección'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 3: PALETA VISUAL DE CAMPOS CLÍNICOS (Visual Field Card Picker)   -->
    <!-- ======================================================================= -->
    <div x-show="showFieldPaletteModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-scaleUp">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                <div>
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <span>✨</span>
                        <span>Selecciona el Tipo de Campo Clínico</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Haz clic en el tipo de dato que deseas agregar a la sección actual.</p>
                </div>
                <button type="button" @click="showFieldPaletteModal = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <div class="p-6 overflow-y-auto space-y-6 flex-1">
                <!-- Categoría: Texto -->
                <div class="space-y-2.5">
                    <span class="text-[11px] font-black text-slate-400 uppercase tracking-wider">Texto y Redacción</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button
                            type="button"
                            @click="selectFieldTypeToCreate('TEXT', 'Texto Corto')"
                            class="p-4 rounded-2xl border border-slate-200/80 hover:border-brand-primary hover:bg-blue-50/40 text-left transition flex items-start gap-3 group cursor-pointer"
                        >
                            <span class="w-9 h-9 rounded-xl bg-blue-100 text-brand-primary flex items-center justify-center text-lg shrink-0">📝</span>
                            <div>
                                <span class="font-bold text-xs text-slate-900 block group-hover:text-brand-primary">Texto Corto (1 Línea)</span>
                                <span class="text-[11px] text-slate-500">Para nombres, motivos breves, referencias.</span>
                            </div>
                        </button>

                        <button
                            type="button"
                            @click="selectFieldTypeToCreate('TEXTAREA', 'Párrafo / Texto Largo')"
                            class="p-4 rounded-2xl border border-slate-200/80 hover:border-brand-primary hover:bg-blue-50/40 text-left transition flex items-start gap-3 group cursor-pointer"
                        >
                            <span class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-lg shrink-0">📄</span>
                            <div>
                                <span class="font-bold text-xs text-slate-900 block group-hover:text-brand-primary">Párrafo / Texto Largo</span>
                                <span class="text-[11px] text-slate-500">Para evolución médica, hallazgos, descripción amplia.</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Categoría: Mediciones y Números -->
                <div class="space-y-2.5">
                    <span class="text-[11px] font-black text-slate-400 uppercase tracking-wider">Mediciones, Signos Vitales y Números</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <button
                            type="button"
                            @click="selectFieldTypeToCreate('DECIMAL', 'Medición / Decimal', 'kg')"
                            class="p-4 rounded-2xl border border-slate-200/80 hover:border-brand-primary hover:bg-blue-50/40 text-left transition flex items-start gap-3 group cursor-pointer"
                        >
                            <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg shrink-0">⚖️</span>
                            <div>
                                <span class="font-bold text-xs text-slate-900 block group-hover:text-brand-primary">Medición Decimal (con Unidades)</span>
                                <span class="text-[11px] text-slate-500">Para Peso (kg), Talla (cm), Temperatura (°C), etc.</span>
                            </div>
                        </button>

                        <button
                            type="button"
                            @click="selectFieldTypeToCreate('INTEGER', 'Número Entero', 'bpm')"
                            class="p-4 rounded-2xl border border-slate-200/80 hover:border-brand-primary hover:bg-blue-50/40 text-left transition flex items-start gap-3 group cursor-pointer"
                        >
                            <span class="w-9 h-9 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-lg shrink-0">🔢</span>
                            <div>
                                <span class="font-bold text-xs text-slate-900 block group-hover:text-brand-primary">Número Entero</span>
                                <span class="text-[11px] text-slate-500">Para Frecuencia Cardíaca (bpm), edad, días de evolución.</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Categoría: Opciones y Listas -->
                <div class="space-y-2.5">
                    <span class="text-[11px] font-black text-slate-400 uppercase tracking-wider">Listas de Opciones</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <button
                            type="button"
                            @click="selectFieldTypeToCreate('SELECT', 'Lista Desplegable')"
                            class="p-3.5 rounded-2xl border border-slate-200/80 hover:border-brand-primary hover:bg-blue-50/40 text-left transition flex flex-col gap-2 group cursor-pointer"
                        >
                            <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-base">🔘</span>
                            <div>
                                <span class="font-bold text-xs text-slate-900 block group-hover:text-brand-primary">Lista Desplegable</span>
                                <span class="text-[10px] text-slate-500">Menú desplegable de 1 opción.</span>
                            </div>
                        </button>

                        <button
                            type="button"
                            @click="selectFieldTypeToCreate('RADIO', 'Opciones de Radio (Una sola)')"
                            class="p-3.5 rounded-2xl border border-slate-200/80 hover:border-brand-primary hover:bg-blue-50/40 text-left transition flex flex-col gap-2 group cursor-pointer"
                        >
                            <span class="w-8 h-8 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center text-base">🔘</span>
                            <div>
                                <span class="font-bold text-xs text-slate-900 block group-hover:text-brand-primary">Botones de Opción</span>
                                <span class="text-[10px] text-slate-500">Opciones visibles excluyentes.</span>
                            </div>
                        </button>

                        <button
                            type="button"
                            @click="selectFieldTypeToCreate('CHECKBOX', 'Casillas Múltiples')"
                            class="p-3.5 rounded-2xl border border-slate-200/80 hover:border-brand-primary hover:bg-blue-50/40 text-left transition flex flex-col gap-2 group cursor-pointer"
                        >
                            <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-base">☑️</span>
                            <div>
                                <span class="font-bold text-xs text-slate-900 block group-hover:text-brand-primary">Casillas Múltiples</span>
                                <span class="text-[10px] text-slate-500">Seleccionar varias opciones.</span>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Categoría: Módulos Médicos y Fechas -->
                <div class="space-y-2.5">
                    <span class="text-[11px] font-black text-slate-400 uppercase tracking-wider">Fechas, Diagnósticos y Otros</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <button
                            type="button"
                            @click="selectFieldTypeToCreate('DATE', 'Fecha')"
                            class="p-3.5 rounded-2xl border border-slate-200/80 hover:border-brand-primary hover:bg-blue-50/40 text-left transition flex flex-col gap-2 group cursor-pointer"
                        >
                            <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-base">📅</span>
                            <div>
                                <span class="font-bold text-xs text-slate-900 block group-hover:text-brand-primary">Fecha</span>
                                <span class="text-[10px] text-slate-500">Selector de fecha en calendario.</span>
                            </div>
                        </button>

                        <button
                            type="button"
                            @click="selectFieldTypeToCreate('BOOLEAN', 'Interruptor Sí / No')"
                            class="p-3.5 rounded-2xl border border-slate-200/80 hover:border-brand-primary hover:bg-blue-50/40 text-left transition flex flex-col gap-2 group cursor-pointer"
                        >
                            <span class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center text-base">🔄</span>
                            <div>
                                <span class="font-bold text-xs text-slate-900 block group-hover:text-brand-primary">Interruptor Sí / No</span>
                                <span class="text-[10px] text-slate-500">Para preguntas de confirmación.</span>
                            </div>
                        </button>

                        <button
                            type="button"
                            @click="selectFieldTypeToCreate('DIAGNOSIS', 'Diagnóstico CIE-10')"
                            class="p-3.5 rounded-2xl border border-slate-200/80 hover:border-brand-primary hover:bg-blue-50/40 text-left transition flex flex-col gap-2 group cursor-pointer"
                        >
                            <span class="w-8 h-8 rounded-xl bg-violet-100 text-violet-700 flex items-center justify-center text-base">🩺</span>
                            <div>
                                <span class="font-bold text-xs text-slate-900 block group-hover:text-brand-primary">Diagnóstico CIE-10</span>
                                <span class="text-[10px] text-slate-500">Módulo de codificación clínica.</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 4: CONFIGURACIÓN Y EDICIÓN DE CAMPO (Friendly Field Editor)       -->
    <!-- ======================================================================= -->
    <div x-show="showFieldEditModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl max-w-xl w-full p-6 relative animate-scaleUp space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-xl" x-text="getFieldIcon(fieldModalForm.field_type)"></span>
                    <div>
                        <h3 class="text-base font-black text-slate-900" x-text="isCreatingField ? 'Nuevo Campo Clínico' : 'Editar Propiedades del Campo'"></h3>
                        <span class="text-[11px] text-brand-primary font-bold" x-text="getFieldTypeFriendlyName(fieldModalForm.field_type)"></span>
                    </div>
                </div>
                <button type="button" @click="showFieldEditModal = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <form @submit.prevent="submitFieldModalForm()" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nombre / Pregunta que verá el médico *</label>
                    <input
                        type="text"
                        x-model="fieldModalForm.label"
                        required
                        placeholder="Ej. Presión Arterial Sistólica, Motivo Principal..."
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-primary text-xs font-medium outline-none"
                    >
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Texto de ayuda / Placeholder (opcional)</label>
                        <input
                            type="text"
                            x-model="fieldModalForm.placeholder"
                            placeholder="Ej. Ingrese el valor en reposo..."
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-primary text-xs outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Unidad de Medida (opcional)</label>
                        <input
                            type="text"
                            x-model="fieldModalForm.unit"
                            placeholder="Ej. mmHg, kg, cm, °C, bpm"
                            class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-primary text-xs outline-none"
                        >
                    </div>
                </div>

                <!-- Sugerencias de Unidades si es numérico -->
                <template x-if="['DECIMAL', 'INTEGER'].includes(fieldModalForm.field_type)">
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <span class="text-[10px] text-slate-400 font-bold">Unidades sugeridas:</span>
                        <template x-for="unitPreset in ['kg', 'cm', 'mmHg', '°C', 'bpm', 'mg/dL', 'lpm', '%']" :key="unitPreset">
                            <button
                                type="button"
                                @click="fieldModalForm.unit = unitPreset"
                                class="px-2 py-0.5 bg-slate-100 hover:bg-blue-50 text-slate-700 text-[10px] font-bold rounded-md"
                                x-text="unitPreset"
                            ></button>
                        </template>
                    </div>
                </template>

                <!-- Editor de Opciones (Si es SELECT, RADIO, MULTISELECT, CHECKBOX) -->
                <template x-if="['SELECT', 'RADIO', 'MULTISELECT', 'CHECKBOX'].includes(fieldModalForm.field_type)">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-black text-slate-800 text-xs uppercase tracking-wider">Opciones de la Lista</span>
                            <button
                                type="button"
                                @click="addOptionToModalForm()"
                                class="px-2.5 py-1 bg-brand-primary hover:bg-brand-primary-dark text-white text-[11px] font-bold rounded-lg transition"
                            >
                                + Agregar Opción
                            </button>
                        </div>

                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            <template x-for="(opt, oIdx) in (fieldModalForm.options || [])" :key="oIdx">
                                <div class="flex items-center gap-2">
                                    <input
                                        type="text"
                                        x-model="opt.label"
                                        required
                                        placeholder="Nombre de la opción (ej. Normal, Positivo)"
                                        class="flex-1 px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs"
                                    >
                                    <button
                                        type="button"
                                        @click="removeOptionFromModalForm(oIdx)"
                                        class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg text-sm"
                                        title="Eliminar"
                                    >
                                        ✕
                                    </button>
                                </div>
                            </template>

                            <div x-show="!fieldModalForm.options || fieldModalForm.options.length === 0" class="text-slate-400 text-xs italic py-2">
                                Presiona "+ Agregar Opción" para definir las opciones disponibles.
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Reglas y Ajustes Clave -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2.5 text-xs">
                    <span class="font-black text-slate-800 uppercase tracking-wider block text-[10px]">Ajustes y Reglas del Campo</span>
                    
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="fieldModalForm.is_required" class="rounded text-brand-primary focus:ring-brand-primary">
                        <span class="font-bold text-slate-800">⭐ Campo Obligatorio (El médico debe llenarlo para cerrar la consulta)</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="fieldModalForm.is_clinical_alert" class="rounded text-rose-600 focus:ring-rose-500">
                        <span class="font-bold text-rose-900">⚠️ Generar Alerta Clínica (Destacar en rojo en la cabecera médica del paciente)</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="fieldModalForm.is_patient_visible" class="rounded text-brand-primary focus:ring-brand-primary">
                        <span class="font-bold text-slate-800">👁️ Visible en el Portal del Paciente (Descarga de informe)</span>
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" x-model="fieldModalForm.is_sensitive" class="rounded text-purple-600 focus:ring-purple-500">
                        <span class="font-bold text-purple-900">🔒 Información Confidencial (Solo visible para médico tratante)</span>
                    </label>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button
                        type="button"
                        @click="showFieldEditModal = false"
                        class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="px-5 py-2.5 bg-brand-primary hover:bg-brand-primary-dark text-white text-xs font-bold rounded-xl shadow-xs transition disabled:opacity-50"
                    >
                        <span x-text="isSubmitting ? 'Guardando...' : (isCreatingField ? 'Crear Campo' : 'Guardar Cambios')"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 5: CONFIRMACIÓN DE ELIMINACIÓN                                    -->
    <!-- ======================================================================= -->
    <div x-show="showDeleteConfirm" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 relative animate-scaleUp space-y-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-2xl mx-auto">
                🗑️
            </div>
            <div class="text-center space-y-1">
                <h3 class="text-base font-black text-slate-900" x-text="deleteConfirmTitle"></h3>
                <p class="text-xs text-slate-500" x-text="deleteConfirmMessage"></p>
            </div>
            <div class="pt-3 flex items-center justify-center gap-3">
                <button
                    type="button"
                    @click="showDeleteConfirm = false"
                    class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition"
                >
                    Cancelar
                </button>
                <button
                    type="button"
                    @click="executeDeleteAction()"
                    :disabled="isSubmitting"
                    class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-xs transition"
                >
                    <span x-text="isSubmitting ? 'Eliminando...' : 'Sí, Eliminar'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 6: CONFIRMACIÓN DE PUBLICACIÓN DE PLANTILLA                       -->
    <!-- ======================================================================= -->
    <div x-show="showPublishConfirm" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 relative animate-scaleUp space-y-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl mx-auto">
                🚀
            </div>
            <div class="text-center space-y-1.5">
                <h3 class="text-base font-black text-slate-900">¿Publicar Versión v{{ $activeVersion->version_number }}?</h3>
                <p class="text-xs text-slate-500">
                    Al publicar, esta versión quedará <strong>activa</strong> para que los doctores atiendan consultas médicas. Por razones médico-legales, una vez publicada será inmutable.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.clinical.templates.publish', ['version' => $activeVersion->id]) }}" class="pt-3 flex items-center justify-center gap-3">
                @csrf
                <button
                    type="button"
                    @click="showPublishConfirm = false"
                    class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition"
                >
                    Seguir Editando
                </button>
                <button
                    type="submit"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-600/20 transition"
                >
                    Publicar Ahora
                </button>
            </form>
        </div>
    </div>

    <!-- ======================================================================= -->
    <!-- MODAL 7: VISTA PREVIA INTERACTIVA DE CONSULTA MÉDICA                   -->
    <!-- ======================================================================= -->
    <div x-show="showPreview" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden animate-scaleUp">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                    <div>
                        <h3 class="text-base font-black text-slate-900">Vista Previa Asistencial: {{ $template->name }}</h3>
                        <span class="text-[11px] text-slate-400">Así es como el médico interactúa con este formulario en su consulta</span>
                    </div>
                </div>
                <button type="button" @click="showPreview = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
            </div>

            <div class="p-6 overflow-y-auto flex-1 space-y-6">
                <!-- Tabs de Secciones en la Vista Previa -->
                <div class="flex flex-wrap gap-2 pb-3 border-b border-slate-100">
                    <template x-for="(sec, sIdx) in localSections" :key="sec.id">
                        <button
                            type="button"
                            @click="previewActiveSectionIndex = sIdx"
                            class="px-4 py-2 text-xs font-bold rounded-xl transition cursor-pointer"
                            :class="previewActiveSectionIndex === sIdx ? 'bg-brand-primary text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            x-text="sec.name"
                        ></button>
                    </template>
                </div>

                <!-- Formulario de la Sección en Vista Previa -->
                <template x-for="(sec, sIdx) in localSections" :key="sec.id">
                    <div x-show="previewActiveSectionIndex === sIdx" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <template x-for="field in (sec.fields || [])" :key="field.id">
                                <div class="space-y-1.5" :class="field.field_type === 'TEXTAREA' ? 'sm:col-span-2' : ''">
                                    <label class="block text-xs font-bold text-slate-700">
                                        <span x-text="field.label"></span>
                                        <span x-show="field.is_required" class="text-rose-600 font-bold">*</span>
                                        <span x-show="field.unit" class="text-slate-400 font-normal" x-text="`(${field.unit})`"></span>
                                    </label>

                                    <template x-if="field.field_type === 'TEXT'">
                                        <input type="text" :placeholder="field.placeholder || ''" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none">
                                    </template>

                                    <template x-if="field.field_type === 'TEXTAREA'">
                                        <textarea rows="3" :placeholder="field.placeholder || ''" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none"></textarea>
                                    </template>

                                    <template x-if="['INTEGER', 'DECIMAL'].includes(field.field_type)">
                                        <div class="relative">
                                            <input type="number" :placeholder="field.placeholder || '0.00'" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none">
                                            <span x-show="field.unit" class="absolute right-3.5 top-2.5 text-xs text-slate-400 font-bold" x-text="field.unit"></span>
                                        </div>
                                    </template>

                                    <template x-if="field.field_type === 'DATE'">
                                        <input type="date" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none">
                                    </template>

                                    <template x-if="['SELECT', 'MULTISELECT'].includes(field.field_type)">
                                        <select class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none">
                                            <option value="">Seleccione una opción...</option>
                                            <template x-for="opt in (field.options || [])" :key="opt.value">
                                                <option :value="opt.value" x-text="opt.label"></option>
                                            </template>
                                        </select>
                                    </template>

                                    <template x-if="field.field_type === 'RADIO'">
                                        <div class="space-y-1 pt-1">
                                            <template x-for="opt in (field.options || [])" :key="opt.value">
                                                <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                                                    <input type="radio" :name="`p_radio_${field.id}`" class="text-brand-primary focus:ring-brand-primary">
                                                    <span x-text="opt.label"></span>
                                                </label>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="field.field_type === 'CHECKBOX'">
                                        <div class="space-y-1 pt-1">
                                            <template x-for="opt in (field.options || [])" :key="opt.value">
                                                <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                                                    <input type="checkbox" class="rounded text-brand-primary focus:ring-brand-primary">
                                                    <span x-text="opt.label"></span>
                                                </label>
                                            </template>
                                        </div>
                                    </template>

                                    <template x-if="field.field_type === 'BOOLEAN'">
                                        <label class="flex items-center gap-2 pt-1 cursor-pointer">
                                            <input type="checkbox" class="rounded text-brand-primary focus:ring-brand-primary">
                                            <span class="text-xs font-semibold text-slate-700" x-text="field.label"></span>
                                        </label>
                                    </template>

                                    <template x-if="field.field_type === 'DIAGNOSIS'">
                                        <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-100 text-xs text-blue-900">
                                            🩺 Búsqueda rápida CIE-10 (Buscador integrado con catálogo de diagnósticos)
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
            
            <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center justify-between">
                <span class="text-xs text-slate-400">Modo de simulación para verificar experiencia médica.</span>
                <button type="button" @click="showPreview = false" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition cursor-pointer">
                    Cerrar Vista Previa
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATIONS (Reemplazo de alert()) -->
    <div
        x-show="toast.show"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-2 px-4 py-3 rounded-2xl shadow-xl border text-xs font-bold"
        :class="toast.type === 'success' ? 'bg-emerald-500 text-white border-emerald-600 shadow-emerald-500/20' : 'bg-rose-500 text-white border-rose-600 shadow-rose-500/20'"
        x-cloak
    >
        <span x-text="toast.type === 'success' ? '✓' : '⚠️'"></span>
        <span x-text="toast.message"></span>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('clinicalTemplateBuilder', (config) => ({
        templateId: config.templateId,
        activeVersionId: config.activeVersionId,
        isPublished: config.isPublished,
        localSections: config.sections || [],
        selectedSection: (config.sections && config.sections.length > 0) ? config.sections[0] : null,
        csrfToken: config.csrfToken,
        showHelp: false,
        showPreview: false,
        showPublishConfirm: false,
        showAddSectionModal: false,
        showEditSectionModal: false,
        showFieldPaletteModal: false,
        showFieldEditModal: false,
        showDeleteConfirm: false,
        isSubmitting: false,
        isCreatingField: false,
        previewActiveSectionIndex: 0,
        
        // Forms State
        newSectionForm: { name: '', description: '', is_required: false, is_repeatable: false },
        editSectionForm: { id: null, name: '', description: '', is_required: false, is_repeatable: false },
        fieldModalForm: { id: null, section_id: null, label: '', field_type: 'TEXT', placeholder: '', unit: '', is_required: false, is_clinical_alert: false, is_sensitive: false, is_patient_visible: true, options: [] },
        
        // Delete Confirmation State
        deleteConfirmTitle: '',
        deleteConfirmMessage: '',
        deleteActionCallback: null,

        // Toast State
        toast: { show: false, message: '', type: 'success' },

        showToast(message, type = 'success') {
            this.toast.message = message;
            this.toast.type = type;
            this.toast.show = true;
            setTimeout(() => { this.toast.show = false; }, 3500);
        },

        init() {
            if (this.localSections.length > 0 && !this.selectedSection) {
                this.selectedSection = this.localSections[0];
            }
        },

        selectSection(sec) {
            this.selectedSection = sec;
        },

        openPreviewModal() {
            this.previewActiveSectionIndex = 0;
            this.showPreview = true;
        },

        getFieldIcon(type) {
            const icons = {
                'TEXT': '📝',
                'TEXTAREA': '📄',
                'INTEGER': '🔢',
                'DECIMAL': '⚖️',
                'DATE': '📅',
                'DATETIME': '📆',
                'TIME': '🕒',
                'BOOLEAN': '🔄',
                'SELECT': '🔘',
                'MULTISELECT': '☑️',
                'RADIO': '🔘',
                'CHECKBOX': '☑️',
                'FILE': '📎',
                'IMAGE': '🖼️',
                'DIAGNOSIS': '🩺',
                'MEDICATION': '💊'
            };
            return icons[type] || '📌';
        },

        getFieldTypeFriendlyName(type) {
            const names = {
                'TEXT': 'Texto Corto',
                'TEXTAREA': 'Párrafo / Texto Largo',
                'INTEGER': 'Número Entero',
                'DECIMAL': 'Medición / Decimal',
                'DATE': 'Fecha',
                'DATETIME': 'Fecha y Hora',
                'TIME': 'Hora',
                'BOOLEAN': 'Sí / No',
                'SELECT': 'Lista Desplegable',
                'MULTISELECT': 'Selección Múltiple',
                'RADIO': 'Opciones Radio',
                'CHECKBOX': 'Casillas Múltiples',
                'FILE': 'Archivo PDF',
                'IMAGE': 'Imagen',
                'DIAGNOSIS': 'Diagnóstico CIE-10',
                'MEDICATION': 'Medicamento'
            };
            return names[type] || type;
        },

        // --- SECCIONES ---
        openAddSectionModal() {
            this.newSectionForm = { name: '', description: '', is_required: false, is_repeatable: false };
            this.showAddSectionModal = true;
        },

        async submitAddSection() {
            if (!this.newSectionForm.name.trim()) return;
            this.isSubmitting = true;
            try {
                const res = await fetch(`/admin/configuracion/historia-clinica/versiones/${this.activeVersionId}/secciones`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify(this.newSectionForm)
                });
                const data = await res.json();
                if (data.success) {
                    this.localSections.push(data.section);
                    this.selectSection(data.section);
                    this.showAddSectionModal = false;
                    this.showToast('Sección creada exitosamente.');
                } else {
                    this.showToast(data.message || 'Error al crear la sección', 'error');
                }
            } catch (e) {
                this.showToast('Error de conexión con el servidor', 'error');
            } finally {
                this.isSubmitting = false;
            }
        },

        openEditSectionModal(sec) {
            this.editSectionForm = {
                id: sec.id,
                name: sec.name,
                description: sec.description || '',
                is_required: !!sec.is_required,
                is_repeatable: !!sec.is_repeatable
            };
            this.showEditSectionModal = true;
        },

        async submitEditSection() {
            this.isSubmitting = true;
            try {
                const res = await fetch(`/admin/configuracion/historia-clinica/secciones/${this.editSectionForm.id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify(this.editSectionForm)
                });
                const data = await res.json();
                if (data.success) {
                    const idx = this.localSections.findIndex(s => s.id === this.editSectionForm.id);
                    if (idx !== -1) {
                        this.localSections[idx].name = data.section.name;
                        this.localSections[idx].description = data.section.description;
                        this.localSections[idx].is_required = data.section.is_required;
                        this.localSections[idx].is_repeatable = data.section.is_repeatable;
                    }
                    this.showEditSectionModal = false;
                    this.showToast('Sección actualizada exitosamente.');
                } else {
                    this.showToast(data.message || 'Error al actualizar', 'error');
                }
            } catch (e) {
                this.showToast('Error al actualizar sección', 'error');
            } finally {
                this.isSubmitting = false;
            }
        },

        confirmDeleteSection(sec) {
            this.deleteConfirmTitle = `¿Eliminar sección "${sec.name}"?`;
            this.deleteConfirmMessage = 'Se eliminarán también todos los campos clínicos que pertenezcan a esta sección.';
            this.deleteActionCallback = async () => {
                const res = await fetch(`/admin/configuracion/historia-clinica/secciones/${sec.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.localSections = this.localSections.filter(s => s.id !== sec.id);
                    this.selectedSection = this.localSections.length > 0 ? this.localSections[0] : null;
                    this.showToast('Sección eliminada exitosamente.');
                } else {
                    this.showToast(data.message || 'Error al eliminar', 'error');
                }
            };
            this.showDeleteConfirm = true;
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

        // --- CAMPOS ---
        openFieldPaletteModal() {
            if (!this.selectedSection) {
                this.showToast('Seleccione o cree una sección primero.', 'error');
                return;
            }
            this.showFieldPaletteModal = true;
        },

        selectFieldTypeToCreate(type, defaultLabel, defaultUnit = '') {
            this.showFieldPaletteModal = false;
            this.isCreatingField = true;
            this.fieldModalForm = {
                id: null,
                section_id: this.selectedSection.id,
                label: defaultLabel || '',
                field_type: type,
                placeholder: '',
                unit: defaultUnit,
                is_required: false,
                is_clinical_alert: false,
                is_sensitive: false,
                is_patient_visible: true,
                options: ['SELECT', 'RADIO', 'MULTISELECT', 'CHECKBOX'].includes(type) ? [{ label: 'Opción 1', value: '1' }, { label: 'Opción 2', value: '2' }] : []
            };
            this.showFieldEditModal = true;
        },

        openEditFieldModal(field) {
            this.isCreatingField = false;
            this.fieldModalForm = {
                id: field.id,
                section_id: field.section_id || this.selectedSection.id,
                label: field.label,
                field_type: field.field_type,
                placeholder: field.placeholder || '',
                unit: field.unit || '',
                is_required: !!field.is_required,
                is_clinical_alert: !!field.is_clinical_alert,
                is_sensitive: !!field.is_sensitive,
                is_patient_visible: field.is_patient_visible !== undefined ? !!field.is_patient_visible : true,
                options: field.options ? JSON.parse(JSON.stringify(field.options)) : []
            };
            this.showFieldEditModal = true;
        },

        addOptionToModalForm() {
            if (!this.fieldModalForm.options) this.fieldModalForm.options = [];
            const count = this.fieldModalForm.options.length + 1;
            this.fieldModalForm.options.push({ label: `Opción ${count}`, value: `${count}` });
        },

        removeOptionFromModalForm(idx) {
            this.fieldModalForm.options.splice(idx, 1);
        },

        async submitFieldModalForm() {
            if (!this.fieldModalForm.label.trim()) return;
            this.isSubmitting = true;

            try {
                if (this.isCreatingField) {
                    // Create Field
                    const res = await fetch(`/admin/configuracion/historia-clinica/secciones/${this.selectedSection.id}/campos`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify({
                            name: this.fieldModalForm.label.trim(),
                            label: this.fieldModalForm.label.trim(),
                            field_type: this.fieldModalForm.field_type,
                            placeholder: this.fieldModalForm.placeholder,
                            unit: this.fieldModalForm.unit,
                            is_required: this.fieldModalForm.is_required,
                            is_clinical_alert: this.fieldModalForm.is_clinical_alert,
                            is_sensitive: this.fieldModalForm.is_sensitive,
                            is_patient_visible: this.fieldModalForm.is_patient_visible,
                            options: this.fieldModalForm.options
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        if (!this.selectedSection.fields) this.selectedSection.fields = [];
                        this.selectedSection.fields.push(data.field);
                        this.showFieldEditModal = false;
                        this.showToast('Campo clínico agregado exitosamente.');
                    } else {
                        this.showToast(data.message || 'Error al agregar campo', 'error');
                    }
                } else {
                    // Update Field
                    const res = await fetch(`/admin/configuracion/historia-clinica/campos/${this.fieldModalForm.id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify({
                            label: this.fieldModalForm.label,
                            code: this.fieldModalForm.label.toLowerCase().replace(/[^a-z0-9]+/g, '_').substring(0, 50),
                            placeholder: this.fieldModalForm.placeholder,
                            unit: this.fieldModalForm.unit,
                            is_required: this.fieldModalForm.is_required,
                            is_clinical_alert: this.fieldModalForm.is_clinical_alert,
                            is_sensitive: this.fieldModalForm.is_sensitive,
                            is_patient_visible: this.fieldModalForm.is_patient_visible,
                            options: this.fieldModalForm.options
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        const fIdx = this.selectedSection.fields.findIndex(f => f.id === this.fieldModalForm.id);
                        if (fIdx !== -1) {
                            this.selectedSection.fields[fIdx] = data.field;
                        }
                        this.showFieldEditModal = false;
                        this.showToast('Campo clínico actualizado.');
                    } else {
                        this.showToast(data.message || 'Error al actualizar', 'error');
                    }
                }
            } catch (e) {
                this.showToast('Error de conexión', 'error');
            } finally {
                this.isSubmitting = false;
            }
        },

        confirmDeleteField(field) {
            this.deleteConfirmTitle = `¿Eliminar el campo "${field.label}"?`;
            this.deleteConfirmMessage = 'Este campo será removido de la plantilla clínica.';
            this.deleteActionCallback = async () => {
                const res = await fetch(`/admin/configuracion/historia-clinica/campos/${field.id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.selectedSection.fields = this.selectedSection.fields.filter(f => f.id !== field.id);
                    this.showToast('Campo eliminado.');
                } else {
                    this.showToast(data.message || 'Error al eliminar', 'error');
                }
            };
            this.showDeleteConfirm = true;
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
        },

        async executeDeleteAction() {
            if (!this.deleteActionCallback) return;
            this.isSubmitting = true;
            try {
                await this.deleteActionCallback();
            } finally {
                this.isSubmitting = false;
                this.showDeleteConfirm = false;
                this.deleteActionCallback = null;
            }
        }
    }));
});
</script>
@endsection
