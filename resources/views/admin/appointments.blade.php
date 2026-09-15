@extends('layouts.admin')

@section('title', 'Gestión de Citas — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn" x-data="{ statusModal: false, selectedApp: null }">
    <!-- Header with Search and Filter Form -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Listado Maestro de Citas</h1>
                <p class="text-xs text-slate-500">Consulta, filtra y gestiona el ciclo de vida de las consultas médicas.</p>
            </div>

            <button x-on:click="$dispatch('open-modal', 'quick-appointment-modal')" class="px-4 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 transition-all">
                + Crear Cita Manual
            </button>
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
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs">
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
            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
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
                        <th class="py-4 px-6">Total / Pago</th>
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
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block">${{ number_format($app->total_amount, 0) }}</span>
                                @php $p = $app->payments->first(); @endphp
                                <span class="text-[11px] {{ $p && $p->status->value === 'PAID' ? 'text-emerald-600 font-semibold' : 'text-amber-600' }}">
                                    {{ $p ? $p->method->label() . ' (' . $p->status->label() . ')' : 'Sin pago' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <x-status-badge :status="$app->status" />
                            </td>
                            <td class="py-4 px-6 text-right">
                                <button @click="selectedApp = {{ $app->toJson() }}; statusModal = true" class="px-3 py-1.5 rounded-xl border border-slate-200 hover:border-brand-primary hover:text-brand-primary font-bold text-xs transition-colors">
                                    Gestionar
                                </button>
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

    <!-- STATUS CHANGE MODAL -->
    <div x-show="statusModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="statusModal = false">
            <template x-if="selectedApp">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Actualizar Cita <span class="font-mono text-brand-primary" x-text="'#' + selectedApp.code"></span></h3>
                    <p class="text-xs text-slate-500 mb-4" x-text="'Paciente: ' + (selectedApp.patient?.first_name || '') + ' ' + (selectedApp.patient?.last_name || '')"></p>

                    <form :action="'{{ url('/admin/citas') }}/' + selectedApp.id + '/estado'" method="POST" class="space-y-4 text-xs">
                        @csrf
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nuevo Estado *</label>
                            <select name="status" class="w-full rounded-xl border-slate-200 text-xs p-2.5 font-bold focus:border-brand-primary">
                                <option value="CONFIRMED">CONFIRMED (Confirmar Asistencia / Reserva)</option>
                                <option value="COMPLETED">COMPLETED (Marcar Atendida)</option>
                                <option value="NO_SHOW">NO_SHOW (No se presentó el paciente)</option>
                                <option value="CANCELLED">CANCELLED (Cancelar Cita y Liberar Turno)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Motivo / Notas de Recepción</label>
                            <textarea name="reason" rows="2" class="w-full rounded-xl border-slate-200 text-xs p-2.5" placeholder="Observaciones del cambio de estado..."></textarea>
                        </div>

                        <div class="flex justify-end space-x-2 pt-2">
                            <button type="button" @click="statusModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold">
                                Volver
                            </button>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-brand-primary text-white font-bold shadow-sm">
                                Guardar Cambio
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
