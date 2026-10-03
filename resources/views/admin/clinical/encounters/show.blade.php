@extends('layouts.admin')

@section('title', "Consulta Médica: {$encounter->patient->full_name} — SurHospital")

@section('content')
<div class="space-y-6" x-data="clinicalEncounterConsultation({
    encounterId: {{ $encounter->id }},
    isFinalized: {{ $encounter->isFinalized() ? 'true' : 'false' }},
    sections: {{ Js::from($encounter->templateVersion->sections()->with('fields.options')->get()) }},
    initialValues: {{ Js::from($existingValues) }},
    initialDiagnoses: {{ Js::from($encounter->diagnoses) }},
    initialVitals: {{ Js::from($encounter->vital_signs_summary ?? []) }},
    csrfToken: '{{ csrf_token() }}',
    draftUrl: '{{ route('admin.clinical.encounter.draft', ['encounter' => $encounter->id]) }}',
    finalizeUrl: '{{ route('admin.clinical.encounter.finalize', ['encounter' => $encounter->id]) }}'
})">
    <!-- Patient & Consultation Header -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        <!-- Main Bar -->
        <div class="p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-gradient-to-r from-slate-50 to-white">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-brand-600 text-white flex items-center justify-center font-bold text-xl shadow-sm shrink-0">
                    {{ substr($encounter->patient->first_name, 0, 1) }}{{ substr($encounter->patient->last_name, 0, 1) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-xl font-bold text-slate-900">{{ $encounter->patient->full_name }}</h1>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                            {{ $encounter->patient->age ? "{$encounter->patient->age} años" : 'Edad N/D' }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-brand-50 text-brand-700 border border-brand-200">
                            HC: {{ $encounter->clinicalRecord->record_number }}
                        </span>
                        @if($encounter->clinicalRecord->blood_type)
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                Sangre: {{ $encounter->clinicalRecord->blood_type }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span><strong>Especialidad:</strong> {{ $encounter->specialty->name }}</span>
                        <span><strong>Médico:</strong> Dr. {{ $encounter->doctor->name }}</span>
                        <span><strong>Plantilla:</strong> {{ $encounter->templateVersion->template->name }} (v{{ $encounter->templateVersion->version_number }})</span>
                        @if($encounter->appointment)
                            <span><strong>Cita:</strong> #{{ $encounter->appointment->code }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status Badge & Action Controls -->
            <div class="flex flex-wrap items-center gap-3">
                <span class="px-3 py-1.5 rounded-xl text-xs font-bold {{ $encounter->status->badgeColor() }}">
                    {{ $encounter->status->label() }}
                </span>

                <a
                    href="{{ route('admin.patients.clinical_record', ['patient' => $encounter->patient->id]) }}"
                    target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition"
                    title="Ver Historia Clínica Longitudinal del Paciente"
                >
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span>Historial Paciente</span>
                </a>

                @if(!$encounter->isFinalized())
                    <button
                        type="button"
                        @click="saveDraft(false)"
                        :disabled="isSaving"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl transition disabled:opacity-50"
                    >
                        <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <span x-text="isSaving ? 'Guardando...' : 'Guardar Borrador'"></span>
                    </button>

                    <button
                        type="button"
                        @click="openFinalizeModal()"
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs transition active:scale-95"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Finalizar Atención</span>
                    </button>
                @else
                    <button
                        type="button"
                        @click="showAddendumModal = true"
                        class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition active:scale-95"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Agregar Adenda Médica</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Permanent Clinical Alerts Banner (Highlighted Prominently) -->
        @if($encounter->patient->clinical_alerts->isNotEmpty())
            <div class="px-5 py-3 bg-red-50/80 border-t border-red-100 flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-1.5 text-xs font-bold text-red-800 uppercase tracking-wider">
                    <svg class="h-4 w-4 text-red-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Alertas Clínicas:</span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @foreach($encounter->patient->clinical_alerts as $alert)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold {{ $alert['badge'] }}">
                            <span>{{ $alert['title'] }}</span>
                            @if(!empty($alert['details']))
                                <span class="font-normal opacity-80">({{ $alert['details'] }})</span>
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Finalized Notice Banner (if locked) -->
    @if($encounter->isFinalized())
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                    ✓
                </div>
                <div>
                    <h3 class="text-sm font-bold text-emerald-900">Atención Médica Finalizada y Sellada</h3>
                    <p class="text-xs text-emerald-700 mt-0.5">
                        Cerrada el {{ $encounter->finalized_at?->format('d/m/Y H:i') }} por {{ $encounter->finalizedByUser?->name ?? 'Médico Asistencial' }}. Este registro es inmutable para efectos médico-legales.
                    </p>
                </div>
            </div>
            <button
                type="button"
                @click="showAddendumModal = true"
                class="px-3.5 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shrink-0"
            >
                + Adenda
            </button>
        </div>
    @endif

    <!-- Consultation Section Stepper -->
    <div class="bg-white p-3 rounded-2xl border border-slate-100 shadow-xs">
        <div class="flex items-center gap-2 overflow-x-auto pb-1">
            <template x-for="(section, sIdx) in sections" :key="section.id">
                <button
                    type="button"
                    @click="activeSectionIndex = sIdx"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap"
                    :class="activeSectionIndex === sIdx ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'"
                >
                    <span
                        class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                        :class="activeSectionIndex === sIdx ? 'bg-white text-brand-600' : 'bg-slate-200 text-slate-700'"
                        x-text="sIdx + 1"
                    ></span>
                    <span x-text="section.name"></span>
                </button>
            </template>

            <!-- Diagnoses Tab in Stepper -->
            <button
                type="button"
                @click="activeSectionIndex = 'diagnoses'"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap"
                :class="activeSectionIndex === 'diagnoses' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'"
            >
                <span
                    class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold"
                    :class="activeSectionIndex === 'diagnoses' ? 'bg-white text-brand-600' : 'bg-slate-200 text-slate-700'"
                >
                    Dx
                </span>
                <span>Diagnósticos CIE-10</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-200 text-slate-800" x-text="diagnoses.length"></span>
            </button>
        </div>
    </div>

    <!-- Active Section Form Fields -->
    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs space-y-6">
        
        <!-- Render Template Dynamic Section -->
        <template x-if="activeSectionIndex !== 'diagnoses' && sections[activeSectionIndex]">
            <div class="space-y-6">
                <!-- Section Title & Info -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900" x-text="sections[activeSectionIndex].name"></h2>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="sections[activeSectionIndex].description || 'Complete los campos clínicos correspondientes.'"></p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span x-show="sections[activeSectionIndex].is_required" class="text-xs font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200">
                            Sección Obligatoria
                        </span>
                    </div>
                </div>

                <!-- Fields Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <template x-for="field in (sections[activeSectionIndex].fields || [])" :key="field.id">
                        <div
                            :class="['TEXTAREA', 'DIAGNOSIS', 'MEDICATION'].includes(field.field_type) ? 'md:col-span-2' : ''"
                            class="space-y-1.5"
                        >
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800">
                                    <span x-text="field.label"></span>
                                    <span x-show="field.is_required" class="text-red-500">*</span>
                                    <span x-show="field.unit" class="text-slate-400 font-normal" x-text="`(${field.unit})`"></span>
                                </label>

                                <div class="flex items-center gap-1.5">
                                    <span x-show="field.is_clinical_alert" class="text-[10px] font-bold text-red-700 bg-red-50 px-2 py-0.5 rounded border border-red-200">
                                        ⚠️ Alerta
                                    </span>
                                    <span x-show="field.is_sensitive" class="text-[10px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded border border-purple-200">
                                        🔒 Sensible
                                    </span>
                                </div>
                            </div>

                            <!-- INPUT: TEXT -->
                            <template x-if="field.field_type === 'TEXT'">
                                <input
                                    type="text"
                                    x-model="formValues[field.id]"
                                    :disabled="isFinalized"
                                    :placeholder="field.placeholder || ''"
                                    class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none transition disabled:bg-slate-100 disabled:cursor-not-allowed"
                                >
                            </template>

                            <!-- INPUT: TEXTAREA -->
                            <template x-if="field.field_type === 'TEXTAREA'">
                                <textarea
                                    rows="3"
                                    x-model="formValues[field.id]"
                                    :disabled="isFinalized"
                                    :placeholder="field.placeholder || ''"
                                    class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none transition disabled:bg-slate-100 disabled:cursor-not-allowed"
                                ></textarea>
                            </template>

                            <!-- INPUT: INTEGER / DECIMAL (with auto BMI if weight & height) -->
                            <template x-if="['INTEGER', 'DECIMAL'].includes(field.field_type)">
                                <div class="relative">
                                    <input
                                        type="number"
                                        :step="field.field_type === 'DECIMAL' ? '0.01' : '1'"
                                        x-model="formValues[field.id]"
                                        @input="recalculateBMI(field.code)"
                                        :disabled="isFinalized"
                                        :placeholder="field.placeholder || ''"
                                        class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none transition disabled:bg-slate-100 disabled:cursor-not-allowed"
                                    >
                                    <span x-show="field.unit" class="absolute right-3 top-2.5 text-xs text-slate-400 font-semibold" x-text="field.unit"></span>
                                </div>
                            </template>

                            <!-- INPUT: DATE -->
                            <template x-if="field.field_type === 'DATE'">
                                <input
                                    type="date"
                                    x-model="formValues[field.id]"
                                    :disabled="isFinalized"
                                    class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none transition disabled:bg-slate-100 disabled:cursor-not-allowed"
                                >
                            </template>

                            <!-- INPUT: SELECT -->
                            <template x-if="field.field_type === 'SELECT'">
                                <select
                                    x-model="formValues[field.id]"
                                    :disabled="isFinalized"
                                    class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none transition disabled:bg-slate-100 disabled:cursor-not-allowed"
                                >
                                    <option value="">Seleccione...</option>
                                    <template x-for="opt in (field.options || [])" :key="opt.value">
                                        <option :value="opt.value" x-text="opt.label"></option>
                                    </template>
                                </select>
                            </template>

                            <!-- INPUT: RADIO -->
                            <template x-if="field.field_type === 'RADIO'">
                                <div class="space-y-1.5 pt-1">
                                    <template x-for="opt in (field.options || [])" :key="opt.value">
                                        <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                                            <input
                                                type="radio"
                                                :name="`radio_${field.id}`"
                                                :value="opt.value"
                                                x-model="formValues[field.id]"
                                                :disabled="isFinalized"
                                                class="text-brand-600 focus:ring-brand-500"
                                            >
                                            <span x-text="opt.label"></span>
                                        </label>
                                    </template>
                                </div>
                            </template>

                            <!-- INPUT: BOOLEAN -->
                            <template x-if="field.field_type === 'BOOLEAN'">
                                <label class="flex items-center gap-3 pt-1 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        x-model="formValues[field.id]"
                                        :disabled="isFinalized"
                                        class="h-4 w-4 rounded text-brand-600 focus:ring-brand-500"
                                    >
                                    <span class="text-sm font-semibold text-slate-700" x-text="formValues[field.id] ? 'Sí / Positivo' : 'No / Negativo'"></span>
                                </label>
                            </template>

                            <!-- Field Help Text -->
                            <p x-show="field.help_text" class="text-[11px] text-slate-400" x-text="field.help_text"></p>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- DIAGNOSES SECTION TAB -->
        <template x-if="activeSectionIndex === 'diagnoses'">
            <div class="space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Diagnósticos Médicos (CIE-10)</h2>
                        <p class="text-xs text-slate-500">Registre los diagnósticos principales y presuntivos de esta atención.</p>
                    </div>

                    <button
                        type="button"
                        x-show="!isFinalized"
                        @click="addDiagnosisRow()"
                        class="px-3 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-700 text-xs font-bold rounded-xl transition"
                    >
                        + Agregar Diagnóstico
                    </button>
                </div>

                <!-- Diagnoses Table / List -->
                <div class="space-y-3">
                    <template x-for="(diag, dIdx) in diagnoses" :key="dIdx">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex flex-col md:flex-row items-start md:items-center gap-3">
                            <div class="w-32 shrink-0">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">Código CIE-10</label>
                                <input
                                    type="text"
                                    x-model="diag.code"
                                    :disabled="isFinalized"
                                    placeholder="Ej. I10"
                                    class="w-full px-2.5 py-1.5 text-xs font-mono font-bold bg-white border border-slate-200 rounded-lg focus:border-brand-500 outline-none"
                                >
                            </div>

                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">Descripción / Patología</label>
                                <input
                                    type="text"
                                    x-model="diag.description"
                                    :disabled="isFinalized"
                                    placeholder="Ej. Hipertensión Esencial (Primaria)"
                                    class="w-full px-2.5 py-1.5 text-xs font-medium bg-white border border-slate-200 rounded-lg focus:border-brand-500 outline-none"
                                >
                            </div>

                            <div class="w-40 shrink-0">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase">Tipo Diagnóstico</label>
                                <select
                                    x-model="diag.type"
                                    :disabled="isFinalized"
                                    class="w-full px-2.5 py-1.5 text-xs bg-white border border-slate-200 rounded-lg focus:border-brand-500 outline-none"
                                >
                                    <option value="PRIMARY">Principal</option>
                                    <option value="SECONDARY">Secundario</option>
                                    <option value="PRESUMPTIVE">Presuntivo</option>
                                    <option value="CONFIRMED">Confirmado</option>
                                </select>
                            </div>

                            <button
                                type="button"
                                x-show="!isFinalized"
                                @click="removeDiagnosisRow(dIdx)"
                                class="p-2 text-red-500 hover:bg-red-50 rounded-lg self-end md:self-center"
                                title="Eliminar diagnóstico"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </template>

                    <div x-show="diagnoses.length === 0" class="p-6 text-center text-slate-400 text-xs border-2 border-dashed border-slate-200 rounded-xl">
                        No se han registrado diagnósticos para esta atención aún. Haga clic en "+ Agregar Diagnóstico".
                    </div>
                </div>
            </div>
        </template>

        <!-- Stepper Navigation Footer -->
        <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <button
                type="button"
                @click="prevSection()"
                :disabled="activeSectionIndex === 0"
                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition disabled:opacity-30"
            >
                &larr; Sección Anterior
            </button>

            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-400" x-text="saveStatusMessage"></span>

                @if(!$encounter->isFinalized())
                    <button
                        type="button"
                        @click="saveDraft(true)"
                        :disabled="isSaving"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl transition"
                    >
                        Guardar Borrador
                    </button>
                @endif

                <button
                    type="button"
                    @click="nextSection()"
                    class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition"
                >
                    <span x-text="activeSectionIndex === 'diagnoses' ? 'Revisar / Finalizar' : 'Siguiente Sección &rarr;'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Clinical Addendums Section (if any exist) -->
    @if($encounter->addendums->isNotEmpty())
        <div class="bg-white p-6 rounded-2xl border border-purple-100 shadow-xs space-y-4">
            <div class="flex items-center gap-2 text-purple-900 font-bold text-sm uppercase tracking-wider pb-2 border-b border-purple-100">
                <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Adendas Clínicas Registradas (Inmutables)</span>
            </div>

            <div class="space-y-3">
                @foreach($encounter->addendums as $addendum)
                    <div class="p-4 bg-purple-50/50 rounded-xl border border-purple-100 space-y-2">
                        <div class="flex items-center justify-between text-xs text-purple-800">
                            <span class="font-bold">Motivo: {{ $addendum->reason }}</span>
                            <span>{{ $addendum->created_at->format('d/m/Y H:i') }} — Dr. {{ $addendum->author->name }}</span>
                        </div>
                        <p class="text-sm text-slate-800 whitespace-pre-wrap">{{ $addendum->content }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Addendum Modal -->
    <div x-show="showAddendumModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 relative animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Emitir Adenda Médica</h3>
                <button type="button" @click="showAddendumModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.clinical.encounter.addendum', ['encounter' => $encounter->id]) }}" class="mt-4 space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1 uppercase">Motivo de la Adenda <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="reason"
                        required
                        placeholder="Ej. Recepción de informe de laboratorio complementario"
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none text-xs"
                    >
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1 uppercase">Contenido / Aclaración Clínica <span class="text-red-500">*</span></label>
                    <textarea
                        name="content"
                        rows="4"
                        required
                        placeholder="Detalle la información clínica adicional o la aclaración correspondiente..."
                        class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none text-xs"
                    ></textarea>
                </div>

                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-800 text-[11px]">
                    La adenda quedará sellada con su nombre y fecha actual, preservando intacta la historia original.
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showAddendumModal = false" class="px-3.5 py-2 text-slate-600 hover:bg-slate-100 rounded-xl font-bold transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-xs transition">
                        Firmar y Registrar Adenda
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('clinicalEncounterConsultation', (config) => ({
        encounterId: config.encounterId,
        isFinalized: config.isFinalized,
        sections: config.sections,
        formValues: config.initialValues || {},
        diagnoses: config.initialDiagnoses && config.initialDiagnoses.length > 0 ? config.initialDiagnoses : [
            { code: 'Z00.0', description: 'Examen médico general', type: 'PRIMARY' }
        ],
        vitalSummary: config.initialVitals || {},
        activeSectionIndex: 0,
        isSaving: false,
        saveStatusMessage: '',
        showAddendumModal: false,
        csrfToken: config.csrfToken,
        draftUrl: config.draftUrl,
        finalizeUrl: config.finalizeUrl,

        nextSection() {
            if (this.activeSectionIndex === 'diagnoses') {
                this.openFinalizeModal();
                return;
            }
            if (this.activeSectionIndex < this.sections.length - 1) {
                this.activeSectionIndex++;
            } else {
                this.activeSectionIndex = 'diagnoses';
            }
            this.saveDraft(false);
        },

        prevSection() {
            if (this.activeSectionIndex === 'diagnoses') {
                this.activeSectionIndex = this.sections.length - 1;
            } else if (this.activeSectionIndex > 0) {
                this.activeSectionIndex--;
            }
        },

        recalculateBMI(fieldCode) {
            // Find weight and height in sections
            let weight = null;
            let height = null;

            for (const sec of this.sections) {
                for (const f of sec.fields) {
                    if (f.code.includes('weight') || f.code.includes('peso')) {
                        weight = parseFloat(this.formValues[f.id]);
                    }
                    if (f.code.includes('height') || f.code.includes('talla')) {
                        height = parseFloat(this.formValues[f.id]);
                    }
                }
            }

            if (weight > 0 && height > 0) {
                // Height in meters if entered in cm (> 3)
                const heightM = height > 3 ? height / 100 : height;
                const bmi = (weight / (heightM * heightM)).toFixed(2);
                this.vitalSummary.bmi = parseFloat(bmi);
                this.vitalSummary.weight = weight;
                this.vitalSummary.height = height;
            }
        },

        addDiagnosisRow() {
            this.diagnoses.push({ code: '', description: '', type: 'SECONDARY' });
        },

        removeDiagnosisRow(idx) {
            this.diagnoses.splice(idx, 1);
        },

        async saveDraft(showToast = true) {
            if (this.isFinalized) return;
            this.isSaving = true;
            this.saveStatusMessage = 'Guardando borrador...';

            try {
                const res = await fetch(this.draftUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        values: this.formValues,
                        diagnoses: this.diagnoses,
                        vital_summary: this.vitalSummary
                    })
                });

                const data = await res.json();
                if (data.success) {
                    this.saveStatusMessage = `Autoguardado: ${data.last_saved_at}`;
                    if (showToast) alert('Borrador guardado exitosamente.');
                }
            } catch (e) {
                this.saveStatusMessage = 'Error al guardar borrador';
            } finally {
                this.isSaving = false;
            }
        },

        async openFinalizeModal() {
            if (this.isFinalized) return;
            if (!confirm('¿Desea FINALIZAR y SELLAR esta atención médica? Una vez finalizada, quedará bloqueada y cualquier corrección posterior requerirá una adenda.')) {
                return;
            }

            this.isSaving = true;
            try {
                const res = await fetch(this.finalizeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        values: this.formValues,
                        diagnoses: this.diagnoses,
                        vital_summary: this.vitalSummary
                    })
                });

                const data = await res.json();
                if (data.success) {
                    alert('Atención médica finalizada con éxito.');
                    window.location.reload();
                } else {
                    let errMsg = data.message || 'Error al finalizar';
                    if (data.errors) {
                        errMsg += '\n- ' + Object.values(data.errors).flat().join('\n- ');
                    }
                    alert(errMsg);
                }
            } catch (e) {
                alert('Ocurrió un error inesperado al finalizar la atención.');
            } finally {
                this.isSaving = false;
            }
        }
    }));
});
</script>
@endsection
