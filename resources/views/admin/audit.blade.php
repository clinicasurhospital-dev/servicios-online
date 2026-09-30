@extends('layouts.admin')

@section('title', 'Auditoría & Trazabilidad — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn" x-data="{ payloadModal: false, selectedLog: null, logsMap: @json($logs->keyBy('id')) }">
    <!-- Header with Filters -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Bitácora de Auditoría del Sistema</h1>
            <p class="text-xs text-slate-500">Trazabilidad inmutable de eventos críticos, cambios de estado y transacciones financieras.</p>
        </div>

        <form method="GET" action="{{ route('admin.audit') }}" class="flex items-center space-x-2">
            <input type="text" name="action" value="{{ $action }}" placeholder="Filtrar por evento (ej: Appointment...)" class="rounded-xl border-slate-200 text-xs p-2.5 w-56 focus:border-brand-primary" />
            <input type="text" name="user" value="{{ $userSearch }}" placeholder="Filtrar por usuario..." class="rounded-xl border-slate-200 text-xs p-2.5 w-48 focus:border-brand-primary" />
            <button type="submit" class="px-4 py-2.5 rounded-xl bg-brand-primary text-white font-bold text-xs shadow-sm">
                Filtrar
            </button>
        </form>
    </div>

    <!-- AUDIT LOG TABLE -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-4 px-6">Fecha / Hora</th>
                        <th class="py-4 px-6">Usuario Responsable</th>
                        <th class="py-4 px-6">Evento / Acción</th>
                        <th class="py-4 px-6">Entidad Afectada</th>
                        <th class="py-4 px-6">IP / Dispositivo</th>
                        <th class="py-4 px-6 text-right">Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6 font-mono text-slate-500 text-[11px]">
                                {{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '—' }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block text-sm">{{ $log->user?->name ?? 'Sistema Automático' }}</span>
                                <span class="text-slate-400 text-[11px]">{{ $log->user?->email }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-brand-soft text-brand-primary">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-700 block">{{ $log->auditable_type ? class_basename($log->auditable_type) : 'Sistema' }}</span>
                                <span class="text-slate-400 font-mono text-[11px]">ID: {{ $log->auditable_id ?: 'N/A' }}</span>
                            </td>
                            <td class="py-4 px-6 font-mono text-slate-400 text-[11px]">
                                {{ $log->ip_address ?: '127.0.0.1' }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <button type="button" @click="selectedLog = logsMap[{{ $log->id }}] || null; payloadModal = true" class="px-3 py-1.5 rounded-xl border border-slate-200 hover:border-brand-primary text-slate-700 hover:text-brand-primary font-bold text-xs transition-colors cursor-pointer">
                                    Ver Diff
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                No se encontraron registros de auditoría con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- JSON DIFF PAYLOAD MODAL -->
    <div x-show="payloadModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4" @click.away="payloadModal = false">
            <template x-if="selectedLog">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-base font-bold text-slate-900" x-text="'Detalle Auditoría: ' + selectedLog.action"></h3>
                        <button @click="payloadModal = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-4 py-4 text-xs font-mono">
                        <div>
                            <span class="font-bold text-slate-700 block mb-1">Estado Anterior:</span>
                            <pre class="bg-slate-900 text-emerald-400 p-3 rounded-xl overflow-x-auto text-[11px] max-h-60" x-text="JSON.stringify(selectedLog.before_state, null, 2)"></pre>
                        </div>
                        <div>
                            <span class="font-bold text-slate-700 block mb-1">Estado Posterior:</span>
                            <pre class="bg-slate-900 text-sky-400 p-3 rounded-xl overflow-x-auto text-[11px] max-h-60" x-text="JSON.stringify(selectedLog.after_state, null, 2)"></pre>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="button" @click="payloadModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs">
                            Cerrar
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
