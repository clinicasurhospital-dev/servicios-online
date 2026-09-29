@extends('layouts.admin')

@section('title', 'Gestión de Citas — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn" x-data="{ 
    statusModal: false, 
    clinicalModal: false,
    selectedApp: null,
    activeTab: 'status',
    openManageModal(app) {
        this.selectedApp = app;
        this.activeTab = 'status';
        this.statusModal = true;
    },
    openClinicalModal(app) {
        this.selectedApp = app;
        this.activeTab = 'clinical';
        this.statusModal = true;
    }
}">
    <!-- Header with Search and Filter Form -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900">{{ auth()->user()->isDoctor() ? 'Mis Citas & Consultas' : 'Listado Maestro de Citas' }}</h1>
                <p class="text-xs text-slate-500">Consulta, filtra, gestiona historias clínicas y el estado de atención de los pacientes.</p>
            </div>

            @if(!auth()->user()->isDoctor())
                <button x-on:click="$dispatch('open-modal', 'quick-appointment-modal')" class="px-4 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 transition-all cursor-pointer">
                    + Crear Cita Manual
                </button>
            @endif
        </div>

        <!-- Filter Bar -->
        <form method="GET" action="{{ route('admin.appointments') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 pt-4 border-t border-slate-100">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por código, paciente, CC..." class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary" />
            </div>

            <div>
                <select name="status" class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary">
                    <option value="">Todos los Estados</option>
                    <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Pendiente</option>
                    <option value="PENDING_PAYMENT" {{ request('status') === 'PENDING_PAYMENT' ? 'selected' : '' }}>Pendiente de Pago</option>
                    <option value="CONFIRMED" {{ request('status') === 'CONFIRMED' ? 'selected' : '' }}>Confirmada</option>
                    <option value="COMPLETED" {{ request('status') === 'COMPLETED' ? 'selected' : '' }}>Atendida</option>
                    <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelada</option>
                    <option value="NO_SHOW" {{ request('status') === 'NO_SHOW' ? 'selected' : '' }}>No Asistió</option>
                </select>
            </div>

            @if(!auth()->user()->isDoctor())
                <div>
                    <select name="doctor_id" class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary">
                        <option value="">Todos los Médicos</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>{{ $doc->user->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary" />
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs cursor-pointer">
                    Filtrar
                </button>
                <a href="{{ route('admin.appointments') }}" class="py-2.5 px-3 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 text-xs font-bold text-center">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center space-x-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center space-x-2">
            <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- DATA TABLE -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-4 px-6">Código / Fecha</th>
                        <th class="py-4 px-6">Paciente</th>
                        <th class="py-4 px-6">Especialista / Servicio</th>
                        @if(!auth()->user()->isDoctor())
                            <th class="py-4 px-6">Total / Pago</th>
                        @else
                            <th class="py-4 px-6">Documentos Clínicos</th>
                        @endif
                        <th class="py-4 px-6">Estado</th>
                        <th class="py-4 px-6 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($appointments as $app)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6">
                                <span class="font-mono font-bold text-brand-primary block text-sm">#{{ $app->code }}</span>
                                <span class="text-slate-500 text-[11px]">{{ $app->start_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block text-sm">{{ $app->patient->first_name }} {{ $app->patient->last_name }}</span>
                                <span class="text-slate-400 text-[11px]">{{ $app->patient->identification_type->value ?? 'CC' }}: {{ $app->patient->identification_number }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-800 block">Dr. {{ $app->doctor->user->name }}</span>
                                <span class="text-slate-500 text-[11px]">{{ $app->specialty->name }} &bull; {{ $app->procedure ? $app->procedure->name : 'Consulta' }}</span>
                            </td>
                            @if(!auth()->user()->isDoctor())
                                <td class="py-4 px-6">
                                    <span class="font-bold text-slate-900 block">${{ number_format($app->total_amount, 0) }}</span>
                                    @php $p = $app->payments->first(); @endphp
                                    <span class="text-[11px] {{ $p && $p->status?->value === 'PAID' ? 'text-emerald-600 font-semibold' : 'text-amber-600' }}">
                                        {{ $p ? ($p->payment_method?->label() ?? 'Pago') . ' (' . ($p->status?->label() ?? 'Pendiente') . ')' : 'Sin pago' }}
                                    </span>
                                </td>
                            @else
                                <td class="py-4 px-6">
                                    <div class="flex flex-col gap-1">
                                        @if($app->hasClinicalHistory())
                                            <a href="{{ route('admin.appointments.download_history', [$app, 'historia']) }}" class="inline-flex items-center space-x-1 text-brand-primary font-bold hover:underline text-[11px]">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                <span>Historia Clínica</span>
                                            </a>
                                        @endif
                                        @if($app->hasProcedureDocument())
                                            <a href="{{ route('admin.appointments.download_history', [$app, 'procedimiento']) }}" class="inline-flex items-center space-x-1 text-emerald-600 font-bold hover:underline text-[11px]">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                                <span>Informe Procedimiento</span>
                                            </a>
                                        @endif
                                        @if(!$app->hasClinicalHistory() && !$app->hasProcedureDocument())
                                            <span class="text-slate-400 text-[11px] italic">Sin documentos</span>
                                        @endif
                                    </div>
                                </td>
                            @endif
                            <td class="py-4 px-6">
                                <x-status-badge :status="$app->status" />
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <button @click="openClinicalModal({{ $app->toJson() }})" class="px-2.5 py-1.5 rounded-xl bg-brand-soft text-brand-primary hover:bg-brand-primary hover:text-white font-bold text-xs transition-colors flex items-center space-x-1 cursor-pointer" title="Subir o gestionar historia clínica">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        <span>Historia / Doc</span>
                                    </button>
                                    <button @click="openManageModal({{ $app->toJson() }})" class="px-2.5 py-1.5 rounded-xl border border-slate-200 hover:border-brand-primary hover:text-brand-primary font-bold text-xs transition-colors cursor-pointer">
                                        Estado
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                No se encontraron citas con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $appointments->links() }}
        </div>
    </div>

    <!-- COMBINED MANAGEMENT & CLINICAL RECORD MODAL -->
    <div x-show="statusModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl space-y-5 animate-scaleUp max-h-[90vh] overflow-y-auto custom-scrollbar" @click.away="statusModal = false">
            <template x-if="selectedApp">
                <div>
                    <!-- Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-mono font-bold text-brand-primary" x-text="'#' + selectedApp.code"></span>
                            <h3 class="text-lg font-black text-slate-900 mt-0.5" x-text="(selectedApp.patient?.first_name || '') + ' ' + (selectedApp.patient?.last_name || '')"></h3>
                            <p class="text-xs text-slate-400" x-text="(selectedApp.specialty?.name || '') + (selectedApp.procedure ? ' — ' + selectedApp.procedure.name : ' — Consulta')"></p>
                        </div>
                        <button type="button" @click="statusModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Modal Navigation Tabs -->
                    <div class="flex p-1 bg-slate-100 rounded-2xl my-4 text-xs font-bold">
                        <button type="button" @click="activeTab = 'status'" :class="activeTab === 'status' ? 'bg-white text-brand-primary shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="flex-1 py-2 rounded-xl transition-all">
                            Estado de la Cita
                        </button>
                        <button type="button" @click="activeTab = 'clinical'" :class="activeTab === 'clinical' ? 'bg-white text-brand-primary shadow-xs' : 'text-slate-600 hover:text-slate-900'" class="flex-1 py-2 rounded-xl transition-all flex items-center justify-center space-x-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Historia Clínica / Procedimiento</span>
                        </button>
                    </div>

                    <!-- TAB 1: ESTADO DE LA CITA -->
                    <div x-show="activeTab === 'status'">
                        <form :action="'{{ url('/admin/citas') }}/' + selectedApp.id + '/estado'" method="POST" class="space-y-4 text-xs">
                            @csrf
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nuevo Estado *</label>
                                <select name="status" class="w-full rounded-xl border-slate-200 text-xs p-3 font-bold focus:border-brand-primary">
                                    <option value="CONFIRMED" :selected="selectedApp.status === 'CONFIRMED'">CONFIRMED (Confirmar Asistencia / Reserva)</option>
                                    <option value="COMPLETED" :selected="selectedApp.status === 'COMPLETED'">COMPLETED (Marcar Atendida / Realizada)</option>
                                    <option value="NO_SHOW" :selected="selectedApp.status === 'NO_SHOW'">NO_SHOW (No se presentó el paciente)</option>
                                    <option value="CANCELLED" :selected="selectedApp.status === 'CANCELLED'">CANCELLED (Cancelar Cita y Liberar Turno)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Motivo / Notas de Recepción</label>
                                <textarea name="reason" rows="3" class="w-full rounded-xl border-slate-200 text-xs p-3" placeholder="Observaciones del cambio de estado o asistencia..."></textarea>
                            </div>

                            <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                                <button type="button" @click="statusModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold">
                                    Cerrar
                                </button>
                                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary text-white font-bold shadow-md shadow-brand-primary/20 hover:bg-brand-primary-dark cursor-pointer">
                                    Guardar Estado
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: SUBIR HISTORIA CLÍNICA O PROCEDIMIENTO -->
                    <div x-show="activeTab === 'clinical'" class="space-y-4 text-xs">
                        <!-- Current Uploaded Documents List -->
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                            <span class="font-bold text-slate-700 block text-xs">Documentos Actuales Adjuntos:</span>
                            
                            <div class="space-y-1.5">
                                <!-- Historia Clínica File -->
                                <template x-if="selectedApp.clinical_history_path">
                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-white border border-slate-200 shadow-2xs">
                                        <div class="flex items-center space-x-2 truncate">
                                            <svg class="w-4 h-4 text-brand-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            <div>
                                                <span class="font-bold text-slate-900 block" x-text="selectedApp.clinical_history_filename || 'Historia Clínica'"></span>
                                                <span class="text-[10px] text-slate-400">Historia Clínica</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-1 shrink-0">
                                            <a :href="'/admin/citas/' + selectedApp.id + '/historia-clinica/historia'" class="px-2.5 py-1 rounded-lg bg-brand-soft text-brand-primary font-bold hover:bg-brand-primary hover:text-white transition-colors text-[11px]">
                                                Descargar
                                            </a>
                                            <form :action="'/admin/citas/' + selectedApp.id + '/historia-clinica/historia'" method="POST" class="inline" onsubmit="return confirm('¿Deseas eliminar la historia clínica adjunta?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 rounded-lg text-rose-500 hover:bg-rose-50 transition-colors" title="Eliminar archivo">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </template>

                                <!-- Procedure Document File -->
                                <template x-if="selectedApp.procedure_document_path">
                                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-white border border-slate-200 shadow-2xs">
                                        <div class="flex items-center space-x-2 truncate">
                                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            <div>
                                                <span class="font-bold text-slate-900 block" x-text="selectedApp.procedure_document_filename || 'Informe de Procedimiento'"></span>
                                                <span class="text-[10px] text-slate-400">Informe / Resultado de Procedimiento</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-1 shrink-0">
                                            <a :href="'/admin/citas/' + selectedApp.id + '/historia-clinica/procedimiento'" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold hover:bg-emerald-600 hover:text-white transition-colors text-[11px]">
                                                Descargar
                                            </a>
                                            <form :action="'/admin/citas/' + selectedApp.id + '/historia-clinica/procedimiento'" method="POST" class="inline" onsubmit="return confirm('¿Deseas eliminar el informe de procedimiento?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 rounded-lg text-rose-500 hover:bg-rose-50 transition-colors" title="Eliminar archivo">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="!selectedApp.clinical_history_path && !selectedApp.procedure_document_path">
                                    <p class="text-slate-400 italic text-[11px]">Aún no se han adjuntado documentos clínicos para esta cita.</p>
                                </template>
                            </div>
                        </div>

                        <!-- Upload Form -->
                        <form :action="'{{ url('/admin/citas') }}/' + selectedApp.id + '/historia-clinica'" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">1. Adjuntar Historia Clínica (PDF, Fotos, DOCX - Máx 10MB)</label>
                                <input type="file" name="clinical_history" accept=".pdf,image/*,.doc,.docx" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-soft file:text-brand-primary hover:file:bg-brand-primary hover:file:text-white transition-colors" />
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">2. Adjuntar Informe / Protocolo de Procedimiento (Opcional)</label>
                                <input type="file" name="procedure_document" accept=".pdf,image/*,.doc,.docx" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-600 hover:file:text-white transition-colors" />
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">3. Notas Clínicas / Diagnóstico / Prescripción Médica</label>
                                <textarea name="clinical_notes" x-model="selectedApp.clinical_notes" rows="3" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" placeholder="Evolución clínica, hallazgos, diagnóstico CIE, plan de tratamiento..."></textarea>
                            </div>

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">4. Actualizar Estado de la Cita</label>
                                <select name="status" class="w-full rounded-xl border-slate-200 text-xs p-2.5 font-bold focus:border-brand-primary">
                                    <option value="COMPLETED" selected>COMPLETED (Marcar Consulta como Atendida)</option>
                                    <option value="CONFIRMED">CONFIRMED (Mantener Confirmada)</option>
                                    <option value="NO_SHOW">NO_SHOW (No Asistió)</option>
                                </select>
                            </div>

                            <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                                <button type="button" @click="statusModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold">
                                    Cerrar
                                </button>
                                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary text-white font-bold shadow-md shadow-brand-primary/20 hover:bg-brand-primary-dark cursor-pointer">
                                    Guardar Documentos Clínicos
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
