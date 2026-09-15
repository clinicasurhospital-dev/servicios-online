@extends('layouts.admin')

@section('title', 'Validación de Transferencias — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn" x-data="{ validateModal: false, selectedTransfer: null, actionType: 'APPROVED' }">
    <!-- Header with Stats -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Validación de Transferencias Bancarias</h1>
                <p class="text-xs text-slate-500">Revisa los comprobantes adjuntados por los pacientes y valida los pagos para confirmar las citas.</p>
            </div>
        </div>

        <!-- Filter Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('admin.transfers') }}?status=PENDING_VALIDATION" class="p-4 rounded-2xl border-2 transition-all flex items-center justify-between {{ $status === 'PENDING_VALIDATION' ? 'border-amber-400 bg-amber-50/50' : 'border-slate-100 bg-slate-50/50 hover:border-slate-200' }}">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700 block">Pendientes de Validación</span>
                    <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $stats['pending'] }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </a>

            <a href="{{ route('admin.transfers') }}?status=APPROVED" class="p-4 rounded-2xl border-2 transition-all flex items-center justify-between {{ $status === 'APPROVED' ? 'border-emerald-400 bg-emerald-50/50' : 'border-slate-100 bg-slate-50/50 hover:border-slate-200' }}">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block">Aprobadas</span>
                    <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $stats['approved'] }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </a>

            <a href="{{ route('admin.transfers') }}?status=REJECTED" class="p-4 rounded-2xl border-2 transition-all flex items-center justify-between {{ $status === 'REJECTED' ? 'border-rose-400 bg-rose-50/50' : 'border-slate-100 bg-slate-50/50 hover:border-slate-200' }}">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-700 block">Rechazadas</span>
                    <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $stats['rejected'] }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center space-x-2">
            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center space-x-2">
            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- TRANSFERS TABLE -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-4 px-6">Referencia / Banco</th>
                        <th class="py-4 px-6">Paciente / Cita</th>
                        <th class="py-4 px-6">Monto / Fecha</th>
                        <th class="py-4 px-6">Comprobante</th>
                        <th class="py-4 px-6">Estado</th>
                        <th class="py-4 px-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transfers as $t)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6">
                                <span class="font-mono font-bold text-slate-900 block text-sm">{{ $t->reference_number }}</span>
                                <span class="text-slate-500 text-[11px]">{{ $t->bank_name }}</span>
                            </td>
                            <td class="py-4 px-6">
                                @php $patient = $t->payment?->appointment?->patient; @endphp
                                <span class="font-bold text-slate-900 block text-sm">{{ $patient ? $patient->first_name . ' ' . $patient->last_name : 'N/D' }}</span>
                                <span class="text-brand-primary text-[11px] font-mono">Cita #{{ $t->payment?->appointment?->code }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-extrabold text-slate-900 block text-sm">${{ number_format($t->amount, 0) }}</span>
                                <span class="text-slate-400 text-[11px]">{{ $t->transfer_date?->format('d/m/Y') }}</span>
                            </td>
                            <td class="py-4 px-6">
                                @if($t->receipt_path)
                                    <a href="{{ route('admin.transfers.receipt', $t) }}" class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition-colors">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        <span>Descargar Recibo</span>
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">Sin archivo</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $t->validation_status->badgeClasses() }}">
                                    {{ $t->validation_status->label() }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if($t->isPending())
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <button @click="selectedTransfer = {{ $t->toJson() }}; actionType = 'APPROVED'; validateModal = true" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition-colors">
                                            Aprobar
                                        </button>
                                        <button @click="selectedTransfer = {{ $t->toJson() }}; actionType = 'REJECTED'; validateModal = true" class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] transition-colors">
                                            Rechazar
                                        </button>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400">Validado por {{ $t->validator?->name ?? 'Admin' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                No se encontraron transferencias con el estado seleccionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $transfers->links() }}
        </div>
    </div>

    <!-- VALIDATION MODAL -->
    <div x-show="validateModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="validateModal = false">
            <template x-if="selectedTransfer">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1" x-text="actionType === 'APPROVED' ? 'Confirmar Aprobación de Transferencia' : 'Rechazar Transferencia Bancaria'"></h3>
                    <p class="text-xs text-slate-500 mb-4" x-text="'Referencia: ' + selectedTransfer.reference_number + ' ($' + selectedTransfer.amount + ')'"></p>

                    <form :action="'{{ url('/admin/transferencias') }}/' + selectedTransfer.id + '/validar'" method="POST" class="space-y-4 text-xs">
                        @csrf
                        <input type="hidden" name="status" :value="actionType">

                        <div>
                            <label class="block font-bold text-slate-700 mb-1" x-text="actionType === 'APPROVED' ? 'Notas de Aprobación (Opcional)' : 'Motivo del Rechazo *'"></label>
                            <textarea name="notes" rows="3" :required="actionType === 'REJECTED'" class="w-full rounded-xl border-slate-200 text-xs p-2.5" :placeholder="actionType === 'APPROVED' ? 'Comprobante verificado en cuenta bancaria...' : 'El número de referencia no coincide con los extractos bancarios...'"></textarea>
                        </div>

                        <div class="flex justify-end space-x-2 pt-2">
                            <button type="button" @click="validateModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold">
                                Cancelar
                            </button>
                            <button type="submit" :class="actionType === 'APPROVED' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'" class="px-4 py-2 rounded-xl text-white font-bold shadow-sm">
                                Confirmar Acción
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
