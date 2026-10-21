@extends('layouts.admin')

@section('title', 'Agenda Médica — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn" x-data="{ selectedApp: null, showModal: false }">
    <!-- Top Agenda Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 sm:p-6 rounded-3xl border border-slate-100 shadow-sm">
        
        <!-- Date Navigation Controls -->
        <div class="flex items-center space-x-2">
            @php
                $prevDate = $viewMode === 'week' ? $selectedDate->copy()->subWeek()->format('Y-m-d') : $selectedDate->copy()->subDay()->format('Y-m-d');
                $nextDate = $viewMode === 'week' ? $selectedDate->copy()->addWeek()->format('Y-m-d') : $selectedDate->copy()->addDay()->format('Y-m-d');
                $routeParams = fn($date, $view) => array_filter([
                    'view' => $view,
                    'date' => $date,
                    'doctor_id' => $selectedDoctorId,
                    'specialty_id' => $selectedSpecialtyId,
                ]);
            @endphp

            <a href="{{ route('admin.agenda', $routeParams($prevDate, $viewMode)) }}" class="p-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors" title="Anterior">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>

            <a href="{{ route('admin.agenda', $routeParams(now()->format('Y-m-d'), $viewMode)) }}" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                Hoy
            </a>

            <a href="{{ route('admin.agenda', $routeParams($nextDate, $viewMode)) }}" class="p-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors" title="Siguiente">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>

            <div class="ml-2">
                <span class="text-sm font-extrabold text-slate-900 block">
                    @if($viewMode === 'week')
                        Semana del {{ $selectedDate->copy()->startOfWeek()->format('d M') }} al {{ $selectedDate->copy()->endOfWeek()->format('d M, Y') }}
                    @else
                        {{ $selectedDate->translatedFormat('l, d \d\e F \d\e Y') }}
                    @endif
                </span>
            </div>
        </div>

        <!-- View Mode, Specialty and Doctor Filters -->
        <div class="flex flex-wrap items-center gap-3">
            @if(!auth()->user()->isDoctor())
                <form method="GET" action="{{ route('admin.agenda') }}" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" name="view" value="{{ $viewMode }}">
                    <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">
                    
                    <!-- Specialty Filter -->
                    <div class="relative">
                        <select name="specialty_id" onchange="this.form.submit()" class="rounded-xl border-slate-200 text-xs font-semibold py-2 px-3 pr-8 focus:border-brand-primary focus:ring-brand-primary bg-white shadow-xs">
                            <option value="">Todas las Especialidades</option>
                            @foreach($specialties as $spec)
                                <option value="{{ $spec->id }}" {{ $selectedSpecialtyId == $spec->id ? 'selected' : '' }}>
                                    {{ $spec->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Doctor Filter -->
                    <div class="relative">
                        <select name="doctor_id" onchange="this.form.submit()" class="rounded-xl border-slate-200 text-xs font-semibold py-2 px-3 pr-8 focus:border-brand-primary focus:ring-brand-primary bg-white shadow-xs">
                            <option value="">Todos los Especialistas / Médicos</option>
                            @foreach($doctors as $doc)
                                @php
                                    $matchesSpec = !$selectedSpecialtyId || $doc->specialties->contains('id', $selectedSpecialtyId);
                                @endphp
                                @if($matchesSpec || $selectedDoctorId == $doc->id)
                                    <option value="{{ $doc->id }}" {{ $selectedDoctorId == $doc->id ? 'selected' : '' }}>
                                        Dr. {{ $doc->user->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    @if($selectedDoctorId || $selectedSpecialtyId)
                        <a href="{{ route('admin.agenda', ['view' => $viewMode, 'date' => $selectedDate->format('Y-m-d')]) }}" class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors text-xs font-semibold" title="Limpiar filtros">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </form>
            @endif

            <!-- View Switcher Tabs -->
            <div class="flex p-1 bg-slate-100 rounded-xl">
                <a href="{{ route('admin.agenda', $routeParams($selectedDate->format('Y-m-d'), 'day')) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $viewMode === 'day' ? 'bg-white text-brand-primary shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Día
                </a>
                <a href="{{ route('admin.agenda', $routeParams($selectedDate->format('Y-m-d'), 'week')) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $viewMode === 'week' ? 'bg-white text-brand-primary shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Semana
                </a>
            </div>
        </div>
    </div>

    <!-- CALENDAR MATRIX -->
    @if($viewMode === 'day')
        <!-- DAY VIEW -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Horarios del Día ({{ $appointments->count() }} citas)</h3>
                <span class="text-xs text-slate-400">Haz clic sobre cualquier turno para ver o actualizar</span>
            </div>

            @if($appointments->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($appointments as $app)
                        <div @click="selectedApp = {{ $app->toJson() }}; showModal = true" class="p-4 rounded-2xl border border-slate-200/80 bg-slate-50/50 hover:bg-brand-soft/30 hover:border-brand-primary cursor-pointer transition-all space-y-3 group shadow-xs">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-white text-brand-primary border border-slate-200 shadow-xs">
                                    {{ $app->start_at->format('H:i') }} - {{ $app->end_at->format('H:i') }}
                                </span>
                                <x-status-badge :status="$app->status" />
                            </div>

                            <div>
                                <h4 class="font-bold text-slate-900 text-sm group-hover:text-brand-primary transition-colors">
                                    {{ $app->patient->first_name }} {{ $app->patient->last_name }}
                                </h4>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Dr. {{ $app->doctor->user->name }} &bull; {{ $app->specialty->name }}
                                </p>
                            </div>

                            <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-xs text-slate-400">
                                <span class="font-mono">#{{ $app->code }}</span>
                                @if(!auth()->user()->isDoctor())
                                    <span class="font-bold text-slate-700">${{ number_format($app->total_amount, 0) }}</span>
                                @else
                                    <span class="text-brand-primary font-semibold text-[11px]">
                                        {{ $app->hasClinicalHistory() ? 'Historia ✓' : 'Sin doc' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 text-slate-400">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-sm font-semibold text-slate-600">No hay citas registradas para este día</p>
                    @if(!auth()->user()->isDoctor())
                        <p class="text-xs text-slate-400 mt-1">Crea una nueva cita manual desde el botón superior o selecciona otra fecha.</p>
                    @endif
                </div>
            @endif
        </div>
    @else
        <!-- WEEK VIEW -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm overflow-x-auto">
            <div class="grid grid-cols-7 gap-3 min-w-[900px]">
                @php
                    $startDay = $selectedDate->copy()->startOfWeek();
                @endphp

                @for($i = 0; $i < 7; $i++)
                    @php
                        $day = $startDay->copy()->addDays($i);
                        $dayApps = $appointments->filter(fn ($a) => $a->start_at->isSameDay($day));
                        $isToday = $day->isToday();
                    @endphp

                    <div class="rounded-2xl p-3 border {{ $isToday ? 'border-brand-primary bg-brand-soft/20' : 'border-slate-100 bg-slate-50/50' }} min-h-[400px] flex flex-col">
                        <div class="text-center pb-2 border-b {{ $isToday ? 'border-brand-primary/20' : 'border-slate-200' }} mb-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider block {{ $isToday ? 'text-brand-primary' : 'text-slate-400' }}">
                                {{ $day->translatedFormat('D') }}
                            </span>
                            <span class="text-base font-black {{ $isToday ? 'text-brand-primary' : 'text-slate-800' }}">
                                {{ $day->format('d M') }}
                            </span>
                        </div>

                        <div class="space-y-2 flex-1 overflow-y-auto">
                            @forelse($dayApps as $app)
                                <div @click="selectedApp = {{ $app->toJson() }}; showModal = true" class="p-2 rounded-xl bg-white border border-slate-200 shadow-xs hover:border-brand-primary cursor-pointer text-xs space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-black text-brand-primary">{{ $app->start_at->format('H:i') }}</span>
                                        <span class="w-2 h-2 rounded-full {{ $app->status->value === 'CONFIRMED' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    </div>
                                    <p class="font-bold text-slate-800 truncate">{{ $app->patient->first_name }} {{ $app->patient->last_name }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">Dr. {{ $app->doctor->user->name }}</p>
                                </div>
                            @empty
                                <div class="text-center py-8 text-[11px] text-slate-300">Libre</div>
                            @endforelse
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    @endif

    <!-- QUICK APPOINTMENT DETAIL DRAWER / MODAL -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto custom-scrollbar" @click.away="showModal = false">
            <template x-if="selectedApp">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-mono font-bold text-brand-primary" x-text="'#' + selectedApp.code"></span>
                            <h3 class="text-lg font-extrabold text-slate-900" x-text="selectedApp.patient ? selectedApp.patient.first_name + ' ' + selectedApp.patient.last_name : 'Paciente'"></h3>
                            <p class="text-xs text-slate-400" x-text="(selectedApp.specialty?.name || '') + (selectedApp.procedure ? ' — ' + selectedApp.procedure.name : ' — Consulta')"></p>
                        </div>
                        <button @click="showModal = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="py-4 space-y-4 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 rounded-xl bg-slate-50">
                                <span class="text-slate-400 block">Médico Especialista:</span>
                                <span class="font-bold text-slate-800" x-text="selectedApp.doctor?.user?.name || 'Dr.'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50">
                                <span class="text-slate-400 block">Especialidad:</span>
                                <span class="font-bold text-slate-800" x-text="selectedApp.specialty?.name || 'General'"></span>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50">
                            <span class="text-slate-400 block">Fecha y Horario:</span>
                            <span class="font-bold text-brand-primary" x-text="selectedApp.start_at"></span>
                        </div>

                        <!-- LOPDP Data Treatment Status -->
                        <div class="p-3 rounded-xl bg-slate-50 flex items-center justify-between">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Tratamiento de Datos (LOPDP):</span>
                                <span class="font-bold text-slate-700">Consentimiento de Salud</span>
                            </div>
                            <template x-if="selectedApp.patient?.data_consent_accepted">
                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[11px] flex items-center gap-1 border border-emerald-200">
                                    <span>✓</span>
                                    <span>Autorizado</span>
                                </span>
                            </template>
                            <template x-if="!selectedApp.patient?.data_consent_accepted">
                                <span class="px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-bold text-[11px] flex items-center gap-1 border border-amber-200">
                                    <span>⚠️</span>
                                    <span>Pendiente</span>
                                </span>
                            </template>
                        </div>

                        <!-- DataLink Quick Actions in Agenda Modal -->
                        <div class="p-3.5 rounded-2xl bg-purple-50/70 border border-purple-200 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-purple-950 flex items-center gap-1.5 text-xs">
                                    <span>🔗</span>
                                    <span>Gestión de Pago DataLink</span>
                                </span>
                                <span class="text-[10px] font-bold text-purple-800 bg-purple-100 px-2 py-0.5 rounded-md">Datafast</span>
                            </div>
                            <div class="flex items-center gap-2 pt-1">
                                <a :href="'{{ url('/admin/citas') }}?search=' + selectedApp.code" class="flex-1 px-3 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-center text-xs shadow-xs transition-colors">
                                    Gestionar Enlace / WhatsApp
                                </a>
                                <a :href="'{{ url('/admin/transferencias') }}?tab=datalink'" class="px-3 py-2 rounded-xl bg-white border border-purple-200 text-purple-900 font-bold hover:bg-purple-50 text-center text-xs transition-colors">
                                    Validar Pagos
                                </a>
                            </div>
                        </div>

                        <!-- Existing Documents in Modal -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                            <span class="font-bold text-slate-700 block">Documentos Clínicos:</span>
                            <div class="space-y-1.5">
                                <template x-if="selectedApp.clinical_history_path">
                                    <div class="flex items-center justify-between p-2 rounded-xl bg-white border border-slate-200">
                                        <span class="font-bold text-slate-800 truncate" x-text="selectedApp.clinical_history_filename || 'Historia Clínica'"></span>
                                        <a :href="'/admin/citas/' + selectedApp.id + '/historia-clinica/historia'" class="px-2.5 py-1 rounded-lg bg-brand-soft text-brand-primary font-bold hover:bg-brand-primary hover:text-white transition-colors text-[11px] shrink-0">
                                            Descargar
                                        </a>
                                    </div>
                                </template>
                                <template x-if="selectedApp.procedure_document_path">
                                    <div class="flex items-center justify-between p-2 rounded-xl bg-white border border-slate-200">
                                        <span class="font-bold text-slate-800 truncate" x-text="selectedApp.procedure_document_filename || 'Informe Procedimiento'"></span>
                                        <a :href="'/admin/citas/' + selectedApp.id + '/historia-clinica/procedimiento'" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold hover:bg-emerald-600 hover:text-white transition-colors text-[11px] shrink-0">
                                            Descargar
                                        </a>
                                    </div>
                                </template>
                                <template x-if="!selectedApp.clinical_history_path && !selectedApp.procedure_document_path">
                                    <span class="text-slate-400 italic">Sin documentos adjuntos</span>
                                </template>
                            </div>
                        </div>

                        <!-- Cancelled Notice -->
                        <template x-if="selectedApp.status === 'CANCELLED'">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center space-y-1">
                                <span class="font-bold text-slate-700 text-xs block">⚠️ Cita Cancelada</span>
                                <p class="text-[11px] text-slate-400">Esta cita médica se encuentra cancelada y no permite modificaciones.</p>
                            </div>
                        </template>

                        <!-- Upload / Update Form (Only for non-cancelled) -->
                        <template x-if="selectedApp.status !== 'CANCELLED'">
                            <form :action="'{{ url('/admin/citas') }}/' + selectedApp.id + '/historia-clinica'" method="POST" enctype="multipart/form-data" class="space-y-3 pt-2 border-t border-slate-100">
                                @csrf
                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Subir / Actualizar Historia Clínica</label>
                                    <input type="file" name="clinical_history" accept=".pdf,image/*,.doc,.docx" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-soft file:text-brand-primary hover:file:bg-brand-primary hover:file:text-white transition-colors" />
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Subir Informe de Procedimiento (Opcional)</label>
                                    <input type="file" name="procedure_document" accept=".pdf,image/*,.doc,.docx" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-600 hover:file:text-white transition-colors" />
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Notas Clínicas / Prescripción</label>
                                    <textarea name="clinical_notes" x-model="selectedApp.clinical_notes" rows="2" class="w-full rounded-xl border-slate-200 text-xs p-2.5" placeholder="Evolución médica o indicaciones..."></textarea>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-700 mb-1">Estado de la Consulta</label>
                                    <template x-if="selectedApp.status === 'COMPLETED'">
                                        <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                                            <span class="font-black text-emerald-900 text-xs">COMPLETED (Atendida)</span>
                                            <span class="text-[10px] text-emerald-600 font-semibold italic">Estado cerrado</span>
                                        </div>
                                    </template>
                                    <template x-if="selectedApp.status !== 'COMPLETED'">
                                        <select name="status" class="w-full rounded-xl border-slate-200 text-xs p-2 font-bold focus:border-brand-primary">
                                            <option value="COMPLETED" selected>COMPLETED (Atendida / Realizada)</option>
                                            <option value="CONFIRMED">CONFIRMED (Confirmada)</option>
                                            <option value="NO_SHOW">NO_SHOW (No Asistió)</option>
                                        </select>
                                    </template>
                                </div>

                                <div class="flex justify-end space-x-2 pt-2">
                                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold">
                                        Cerrar
                                    </button>
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-primary text-white font-bold shadow-sm hover:bg-brand-primary-dark">
                                        Guardar
                                    </button>
                                </div>
                            </form>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
