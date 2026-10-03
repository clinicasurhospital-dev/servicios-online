@extends('layouts.admin')

@section('title', "Historia Clínica: {$patient->full_name} — SurHospital")

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'summary', showAddAllergy: false, showAddCondition: false, showAddMed: false, showAddBackground: false, showUploadDoc: false }">
    <!-- Patient Identity Card & Clinical Alerts Banner -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 bg-gradient-to-r from-slate-50 to-white">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-2xl bg-brand-600 text-white flex items-center justify-center font-bold text-2xl shadow-sm shrink-0">
                    {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-2xl font-bold text-slate-900">{{ $patient->full_name }}</h1>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200">
                            HC: {{ $record->record_number }}
                        </span>
                        @if($record->blood_type)
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                Grupo Sanguíneo: {{ $record->blood_type }}
                            </span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-500 mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span><strong>Cédula/ID:</strong> {{ $patient->identification_number }}</span>
                        <span><strong>Edad:</strong> {{ $patient->age ? "{$patient->age} años" : 'N/D' }}</span>
                        <span><strong>Género:</strong> {{ $patient->gender?->label() ?? 'N/D' }}</span>
                        <span><strong>Teléfono:</strong> {{ $patient->phone }}</span>
                        <span><strong>Email:</strong> {{ $patient->email }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                @if(!$isReception)
                    <button
                        type="button"
                        @click="showAddAllergy = true"
                        class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-xl border border-red-200 transition"
                    >
                        + Alergia
                    </button>
                    <button
                        type="button"
                        @click="showAddCondition = true"
                        class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold rounded-xl border border-amber-200 transition"
                    >
                        + Condición
                    </button>
                    <button
                        type="button"
                        @click="showAddMed = true"
                        class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-xl border border-blue-200 transition"
                    >
                        + Medicación
                    </button>
                    <button
                        type="button"
                        @click="showUploadDoc = true"
                        class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition"
                    >
                        + Documento
                    </button>
                @endif
            </div>
        </div>

        <!-- PERMANENT CLINICAL ALERTS BANNER -->
        @if($patient->clinical_alerts->isNotEmpty())
            <div class="px-6 py-3 bg-red-50 border-t border-red-100 flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-1.5 text-xs font-bold text-red-800 uppercase tracking-wider">
                    <svg class="h-4 w-4 text-red-600 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Información Clínica Relevante:</span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @foreach($patient->clinical_alerts as $alert)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold {{ $alert['badge'] }}">
                            <span>{{ $alert['title'] }}</span>
                            @if(!empty($alert['details']))
                                <span class="font-normal opacity-85">({{ $alert['details'] }})</span>
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white p-2 rounded-2xl border border-slate-100 shadow-xs flex items-center gap-2 overflow-x-auto">
        <button
            type="button"
            @click="activeTab = 'summary'"
            class="px-4 py-2 text-xs font-bold rounded-xl transition whitespace-nowrap"
            :class="activeTab === 'summary' ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
        >
            Resumen Clínico
        </button>
        <button
            type="button"
            @click="activeTab = 'backgrounds'"
            class="px-4 py-2 text-xs font-bold rounded-xl transition whitespace-nowrap"
            :class="activeTab === 'backgrounds' ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
        >
            Antecedentes ({{ $record->backgrounds->count() }})
        </button>
        <button
            type="button"
            @click="activeTab = 'timeline'"
            class="px-4 py-2 text-xs font-bold rounded-xl transition whitespace-nowrap"
            :class="activeTab === 'timeline' ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
        >
            Consultas / Timeline ({{ $record->encounters->count() }})
        </button>
        <button
            type="button"
            @click="activeTab = 'diagnoses'"
            class="px-4 py-2 text-xs font-bold rounded-xl transition whitespace-nowrap"
            :class="activeTab === 'diagnoses' ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
        >
            Diagnósticos ({{ $record->diagnoses->count() }})
        </button>
        <button
            type="button"
            @click="activeTab = 'documents'"
            class="px-4 py-2 text-xs font-bold rounded-xl transition whitespace-nowrap"
            :class="activeTab === 'documents' ? 'bg-brand-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'"
        >
            Documentos Clínicos ({{ $record->documents->count() }})
        </button>
    </div>

    <!-- TAB 1: RESUMEN CLÍNICO -->
    <div x-show="activeTab === 'summary'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Card: Alergias -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        Alergias Registradas
                    </h3>
                    @if(!$isReception)
                        <button type="button" @click="showAddAllergy = true" class="text-xs font-bold text-brand-600 hover:underline">+ Agregar</button>
                    @endif
                </div>
                <div class="space-y-2">
                    @forelse($record->allergies as $allergy)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-start justify-between gap-2">
                            <div>
                                <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <span>{{ $allergy->substance }}</span>
                                    @if($allergy->is_critical)
                                        <span class="text-[10px] font-bold px-1.5 py-0.2 bg-red-100 text-red-800 rounded">Crítica</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $allergy->allergy_type->label() }} • {{ $allergy->severity->label() }}
                                    @if($allergy->reaction)
                                        — Reacción: {{ $allergy->reaction }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400 italic">No se han registrado alergias.</div>
                    @endforelse
                </div>
            </div>

            <!-- Card: Condiciones / Enfermedades Crónicas -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        Condiciones Crónicas
                    </h3>
                    @if(!$isReception)
                        <button type="button" @click="showAddCondition = true" class="text-xs font-bold text-brand-600 hover:underline">+ Agregar</button>
                    @endif
                </div>
                <div class="space-y-2">
                    @forelse($record->conditions as $cond)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-start justify-between gap-2">
                            <div>
                                <div class="text-xs font-bold text-slate-900">{{ $cond->condition_name }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    Estado: <span class="font-semibold text-slate-700">{{ $cond->status->label() }}</span>
                                    @if($cond->icd10_code)
                                        • CIE-10: {{ $cond->icd10_code }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400 italic">No se han registrado condiciones crónicas.</div>
                    @endforelse
                </div>
            </div>

            <!-- Card: Medicación Habitual -->
            <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Medicación Habitual
                    </h3>
                    @if(!$isReception)
                        <button type="button" @click="showAddMed = true" class="text-xs font-bold text-brand-600 hover:underline">+ Agregar</button>
                    @endif
                </div>
                <div class="space-y-2">
                    @forelse($record->medications as $med)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-start justify-between gap-2">
                            <div>
                                <div class="text-xs font-bold text-slate-900">{{ $med->drug_name }} {{ $med->concentration }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $med->dose }} {{ $med->frequency }} (Vía {{ $med->route }})
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400 italic">No se ha registrado medicación habitual.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Próxima Cita & Última Atención -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-5 bg-brand-50/60 rounded-2xl border border-brand-100 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-brand-800 uppercase tracking-wider">Próxima Cita Programada</span>
                    @if($summary['next_appointment'])
                        <div class="text-sm font-bold text-slate-900 mt-1">
                            {{ $summary['next_appointment']->start_at->format('d/m/Y H:i') }} — {{ $summary['next_appointment']->specialty->name }}
                        </div>
                        <div class="text-xs text-slate-500">Dr. {{ $summary['next_appointment']->doctor->name }}</div>
                    @else
                        <div class="text-xs text-slate-500 mt-1">Sin citas futuras agendadas.</div>
                    @endif
                </div>
            </div>

            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">Última Atención Médica</span>
                    @if($record->encounters->first())
                        @php $lastEnc = $record->encounters->first(); @endphp
                        <div class="text-sm font-bold text-slate-900 mt-1">
                            {{ $lastEnc->created_at->format('d/m/Y H:i') }} — {{ $lastEnc->specialty->name }}
                        </div>
                        <div class="text-xs text-slate-500">Dr. {{ $lastEnc->doctor->name }} ({{ $lastEnc->status->label() }})</div>
                    @else
                        <div class="text-xs text-slate-500 mt-1">Sin atenciones previas registradas.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: ANTECEDENTES ESTRUCTURADOS -->
    <div x-show="activeTab === 'backgrounds'" class="space-y-6" x-cloak>
        <div class="flex items-center justify-between bg-white p-4 rounded-2xl border border-slate-100 shadow-xs">
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Historial de Antecedentes Médicos</h2>
                <p class="text-xs text-slate-400">Patológicos, familiares, quirúrgicos, hábitos y gineco-obstétricos.</p>
            </div>
            @if(!$isReception)
                <button
                    type="button"
                    @click="showAddBackground = true"
                    class="px-3.5 py-1.5 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl transition"
                >
                    + Agregar Antecedente
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                $categories = [
                    'PERSONAL' => ['title' => 'Antecedentes Personales / Patológicos', 'color' => 'brand'],
                    'FAMILY' => ['title' => 'Antecedentes Familiares / Hereditarios', 'color' => 'amber'],
                    'SURGICAL' => ['title' => 'Antecedentes Quirúrgicos / Traumáticos', 'color' => 'purple'],
                    'HABIT' => ['title' => 'Hábitos y Estilo de Vida', 'color' => 'blue'],
                    'GYNECO_OBSTETRIC' => ['title' => 'Antecedentes Gineco-Obstétricos', 'color' => 'pink'],
                    'PEDIATRIC' => ['title' => 'Antecedentes Perinatales / Pediátricos', 'color' => 'emerald'],
                ];
            @endphp

            @foreach($categories as $catKey => $catInfo)
                @php
                    $items = $record->backgrounds->where('category.value', $catKey);
                @endphp
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100">
                        {{ $catInfo['title'] }} ({{ $items->count() }})
                    </h3>
                    <div class="space-y-2">
                        @forelse($items as $item)
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                                <div class="text-xs font-bold text-slate-900 flex items-center justify-between">
                                    <span>{{ $item->title }}</span>
                                    @if($item->event_year)
                                        <span class="text-[10px] text-slate-500 font-normal">Año: {{ $item->event_year }}</span>
                                    @endif
                                </div>
                                @if($item->relationship)
                                    <div class="text-[11px] text-slate-600 mt-0.5">Parentesco: <strong>{{ $item->relationship }}</strong></div>
                                @endif
                                @if($item->institution)
                                    <div class="text-[11px] text-slate-600 mt-0.5">Institución: {{ $item->institution }}</div>
                                @endif
                                @if($item->details)
                                    <p class="text-xs text-slate-700 mt-1">{{ $item->details }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="p-3 text-xs text-slate-400 italic">No hay registros en esta categoría.</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 3: TIMELINE DE CONSULTAS MÉDICAS -->
    <div x-show="activeTab === 'timeline'" class="space-y-6" x-cloak>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-xs space-y-6">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Línea de Tiempo de Atenciones Médicas</h2>

            <div class="relative border-l-2 border-brand-200 ml-4 space-y-8">
                @forelse($record->encounters as $enc)
                    <div class="relative pl-6">
                        <!-- Node Circle -->
                        <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-brand-600 ring-4 ring-white"></div>

                        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div>
                                    <span class="text-xs font-bold text-brand-600">{{ $enc->created_at->format('d/m/Y H:i') }}</span>
                                    <h3 class="text-sm font-bold text-slate-900 mt-0.5">{{ $enc->specialty->name }} — Dr. {{ $enc->doctor->name }}</h3>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $enc->status->badgeColor() }}">
                                        {{ $enc->status->label() }}
                                    </span>
                                    <a
                                        href="{{ route('admin.clinical.encounter.show', ['encounter' => $enc->id]) }}"
                                        class="px-3 py-1 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-lg transition"
                                    >
                                        Ver Consulta
                                    </a>
                                </div>
                            </div>

                            @if(!$isReception && $enc->reason_for_consultation)
                                <div class="text-xs text-slate-600">
                                    <strong>Motivo:</strong> {{ $enc->reason_for_consultation }}
                                </div>
                            @endif

                            @if(!$isReception && $enc->diagnoses->isNotEmpty())
                                <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-200">
                                    <span class="text-[11px] font-bold text-slate-500 uppercase">Diagnósticos:</span>
                                    @foreach($enc->diagnoses as $diag)
                                        <span class="px-2 py-0.5 rounded text-xs font-bold {{ $diag->type->badgeColor() }}">
                                            {{ $diag->code }} — {{ $diag->description }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            @if($enc->addendums->isNotEmpty())
                                <div class="pt-2 text-[11px] text-purple-700 font-bold flex items-center gap-1">
                                    <span>⚠️ Contiene {{ $enc->addendums->count() }} adenda(s) médica(s) registrada(s).</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="pl-6 text-xs text-slate-400 italic">No hay atenciones médicas registradas aún para este paciente.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- TAB 4: DIAGNÓSTICOS HISTÓRICOS -->
    <div x-show="activeTab === 'diagnoses'" class="bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden" x-cloak>
        <div class="p-5 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Historial Acumulado de Diagnósticos (CIE-10)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-3">Código</th>
                        <th class="px-6 py-3">Descripción</th>
                        <th class="px-6 py-3">Clasificación</th>
                        <th class="px-6 py-3">Fecha de Registro</th>
                        <th class="px-6 py-3">Consulta Asistencial</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($record->diagnoses as $diag)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-3 font-mono font-bold text-brand-700">{{ $diag->code }}</td>
                            <td class="px-6 py-3 font-medium text-slate-900">{{ $diag->description }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $diag->type->badgeColor() }}">
                                    {{ $diag->type->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-slate-500">{{ $diag->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3">
                                <a href="{{ route('admin.clinical.encounter.show', ['encounter' => $diag->encounter_id]) }}" class="text-brand-600 hover:underline font-bold">
                                    Ver Atención #{{ $diag->encounter_id }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 italic">No hay diagnósticos registrados en el historial.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 5: DOCUMENTOS CLÍNICOS SEGUROS -->
    <div x-show="activeTab === 'documents'" class="bg-white rounded-2xl border border-slate-100 shadow-xs p-6 space-y-4" x-cloak>
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Documentos y Estudios Clínicos</h2>
                <p class="text-xs text-slate-400">Informes de laboratorio, imágenes, reportes quirúrgicos almacenados en storage privado.</p>
            </div>
            @if(!$isReception)
                <button
                    type="button"
                    @click="showUploadDoc = true"
                    class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl shadow-xs transition"
                >
                    + Adjuntar Documento
                </button>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($record->documents as $doc)
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col justify-between space-y-3">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-200">
                                {{ $doc->document_type->label() }}
                            </span>
                            @if($doc->is_sensitive)
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-purple-100 text-purple-700">🔒 Confidencial</span>
                            @endif
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 mt-2 truncate" title="{{ $doc->file_name }}">{{ $doc->file_name }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $doc->formatted_size }} • {{ $doc->created_at->format('d/m/Y H:i') }}</p>
                        @if($doc->description)
                            <p class="text-xs text-slate-700 mt-1.5 line-clamp-2">{{ $doc->description }}</p>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-slate-200/80 flex items-center justify-between">
                        <span class="text-[10px] text-slate-400">Por: {{ $doc->uploader->name }}</span>
                        <a
                            href="{{ route('admin.clinical.documents.download', ['document' => $doc->id]) }}"
                            class="inline-flex items-center gap-1 text-xs font-bold text-brand-600 hover:text-brand-800"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span>Descargar</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 p-8 text-center text-xs text-slate-400 italic">No se han adjuntado documentos clínicos para este paciente.</div>
            @endforelse
        </div>
    </div>

    <!-- MODAL: AGREGAR ALERGIA -->
    <div x-show="showAddAllergy" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative">
            <h3 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Registrar Alergia</h3>
            <form method="POST" action="{{ route('admin.patients.clinical_record.allergy', ['patient' => $patient->id]) }}" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Sustancia / Medicamento / Alimento <span class="text-red-500">*</span></label>
                    <input type="text" name="substance" required placeholder="Ej. Penicilina, Mariscos, Látex" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tipo</label>
                        <select name="allergy_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                            <option value="MEDICATION">Medicamento</option>
                            <option value="FOOD">Alimento</option>
                            <option value="ENVIRONMENTAL">Ambiental</option>
                            <option value="OTHER">Otro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Severidad</label>
                        <select name="severity" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                            <option value="MILD">Leve</option>
                            <option value="MODERATE">Moderada</option>
                            <option value="SEVERE">Severa</option>
                            <option value="LIFE_THREATENING">Mortal (Anafilaxia)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Reacción / Síntomas</label>
                    <input type="text" name="reaction" placeholder="Ej. Urticaria generalizada, edema de glotis" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                </div>
                <label class="flex items-center gap-2 cursor-pointer pt-1">
                    <input type="checkbox" name="is_critical" value="1" class="rounded text-red-600 focus:ring-red-500">
                    <span class="font-bold text-red-700">Disparar Alerta Clínica Prominente</span>
                </label>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showAddAllergy = false" class="px-3.5 py-1.5 text-slate-600 hover:bg-slate-100 rounded-xl font-bold">Cancelar</button>
                    <button type="submit" class="px-4 py-1.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-xs">Guardar Alergia</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: AGREGAR CONDICIÓN -->
    <div x-show="showAddCondition" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative">
            <h3 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Registrar Condición Crónica</h3>
            <form method="POST" action="{{ route('admin.patients.clinical_record.condition', ['patient' => $patient->id]) }}" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Condición / Patología <span class="text-red-500">*</span></label>
                    <input type="text" name="condition_name" required placeholder="Ej. Hipertensión Arterial Primaria" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Código CIE-10</label>
                        <input type="text" name="icd10_code" placeholder="Ej. I10" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Estado</label>
                        <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                            <option value="ACTIVE">Activa</option>
                            <option value="CONTROLLED">Controlada</option>
                            <option value="REMISSION">En Remisión</option>
                            <option value="RESOLVED">Resuelta</option>
                        </select>
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer pt-1">
                    <input type="checkbox" name="is_clinical_alert" value="1" class="rounded text-amber-600 focus:ring-amber-500">
                    <span class="font-bold text-amber-800">Mostrar como Alerta Clínica en Cabecera</span>
                </label>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showAddCondition = false" class="px-3.5 py-1.5 text-slate-600 hover:bg-slate-100 rounded-xl font-bold">Cancelar</button>
                    <button type="submit" class="px-4 py-1.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-xs">Guardar Condición</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: AGREGAR MEDICAMENTO -->
    <div x-show="showAddMed" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative">
            <h3 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Registrar Medicación Habitual</h3>
            <form method="POST" action="{{ route('admin.patients.clinical_record.medication', ['patient' => $patient->id]) }}" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Fármaco <span class="text-red-500">*</span></label>
                    <input type="text" name="drug_name" required placeholder="Ej. Losartán Potásico" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Concentración</label>
                        <input type="text" name="concentration" placeholder="Ej. 50 mg" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Dosis / Cantidad</label>
                        <input type="text" name="dose" placeholder="Ej. 1 tableta" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Frecuencia</label>
                        <input type="text" name="frequency" placeholder="Ej. Cada 12 horas" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Vía de Administración</label>
                        <input type="text" name="route" value="Oral" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showAddMed = false" class="px-3.5 py-1.5 text-slate-600 hover:bg-slate-100 rounded-xl font-bold">Cancelar</button>
                    <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-xs">Guardar Medicamento</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: AGREGAR ANTECEDENTE -->
    <div x-show="showAddBackground" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative">
            <h3 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Registrar Antecedente Médico</h3>
            <form method="POST" action="{{ route('admin.patients.clinical_record.background', ['patient' => $patient->id]) }}" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Categoría</label>
                    <select name="category" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        <option value="PERSONAL">Personal / Patológico</option>
                        <option value="FAMILY">Familiar / Hereditario</option>
                        <option value="SURGICAL">Quirúrgico / Traumático</option>
                        <option value="HABIT">Hábitos / Estilo de Vida</option>
                        <option value="GYNECO_OBSTETRIC">Gineco-Obstétrico</option>
                        <option value="PEDIATRIC">Perinatal / Pediátrico</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Título / Procedimiento <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="Ej. Apendicectomía, Padre con Infarto, Tabaquismo" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-brand-500 outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Parentesco (si aplica)</label>
                        <input type="text" name="relationship" placeholder="Ej. Padre, Madre, Hermano" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Año / Fecha</label>
                        <input type="text" name="event_year" placeholder="Ej. 2018" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                    </div>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Detalles / Observaciones</label>
                    <textarea name="details" rows="2" placeholder="Detalles clínicos relevantes..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl"></textarea>
                </div>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showAddBackground = false" class="px-3.5 py-1.5 text-slate-600 hover:bg-slate-100 rounded-xl font-bold">Cancelar</button>
                    <button type="submit" class="px-4 py-1.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-xs">Guardar Antecedente</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: SUBIR DOCUMENTO -->
    <div x-show="showUploadDoc" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 relative">
            <h3 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Adjuntar Documento Clínico</h3>
            <form method="POST" action="{{ route('admin.patients.clinical_record.document_upload', ['patient' => $patient->id]) }}" enctype="multipart/form-data" class="space-y-3.5 text-xs">
                @csrf
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tipo de Documento</label>
                    <select name="document_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl">
                        <option value="LAB_REPORT">Informe de Laboratorio</option>
                        <option value="IMAGING">Estudio de Imagen (Rayos X, Ecografía, TAC)</option>
                        <option value="SURGICAL_REPORT">Reporte Quirúrgico</option>
                        <option value="CONSENT_FORM">Consentimiento Informado</option>
                        <option value="EXTERNAL_RECORD">Historia Externa / Transferencia</option>
                        <option value="OTHER">Otro Documento</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Archivo (PDF, JPEG, PNG, DOC) <span class="text-red-500">*</span></label>
                    <input type="file" name="document_file" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Descripción / Hallazgos</label>
                    <textarea name="description" rows="2" placeholder="Notas sobre el documento adjunto..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl"></textarea>
                </div>
                <label class="flex items-center gap-2 cursor-pointer pt-1">
                    <input type="checkbox" name="is_sensitive" value="1" class="rounded text-purple-600 focus:ring-purple-500">
                    <span class="font-bold text-purple-800">Marcar como Documento Confidencial</span>
                </label>
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showUploadDoc = false" class="px-3.5 py-1.5 text-slate-600 hover:bg-slate-100 rounded-xl font-bold">Cancelar</button>
                    <button type="submit" class="px-4 py-1.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-xs">Cargar Documento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
