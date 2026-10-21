@extends('layouts.admin')

@section('title', 'Validación de Pagos y Transferencias — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn" x-data="{ 
    tab: '{{ $tab }}',
    validateModal: false, 
    selectedTransfer: null, 
    actionType: 'APPROVED',
    dataLinkEmailModal: false,
    dataLinkWaModal: false,
    selectedDatalink: null,
    datalinkUrl: '',
    datalinkNotes: '',
    copiedWa: false,
    openDataLinkEmail(p) {
        this.selectedDatalink = p;
        this.datalinkUrl = p.datalink_url || (p.payload_response && p.payload_response.datalink_url) || '';
        this.datalinkNotes = '';
        this.dataLinkEmailModal = true;
    },
    openDataLinkWa(p) {
        this.selectedDatalink = p;
        this.datalinkUrl = p.datalink_url || (p.payload_response && p.payload_response.datalink_url) || '';
        this.copiedWa = false;
        this.dataLinkWaModal = true;
    },
    getWhatsAppUrl() {
        if (!this.selectedDatalink) return '#';
        const app = this.selectedDatalink.appointment;
        let phone = app?.patient?.phone || '';
        phone = phone.replace(/[^0-9]/g, '');
        if (phone.startsWith('09') && phone.length === 10) {
            phone = '593' + phone.substring(1);
        } else if (phone.startsWith('0') && phone.length === 9) {
            phone = '593' + phone.substring(1);
        }
        const patientName = app?.patient ? (app.patient.first_name + ' ' + app.patient.last_name) : 'Estimado(a) paciente';
        const doctorName = app?.doctor?.user?.name || 'su médico especialista';
        const specialty = app?.specialty?.name || 'Consulta Médica';
        const date = app?.start_at ? new Date(app.start_at).toLocaleDateString('es-EC', { weekday: 'short', day: 'numeric', month: 'short' }) : 'su fecha agendada';
        const amount = this.selectedDatalink.amount ? Number(this.selectedDatalink.amount).toFixed(2) : '0.00';
        const link = this.datalinkUrl || 'Enlace en preparación';
        const brand = '{{ config('branding.client_name', 'Clínica Sur Hospital') }}';
        
        const msg = `¡Hola ${patientName}! 👋 Le saludamos desde *${brand}*.\n\nLe compartimos su *Enlace de Pago DataLink (Datafast)* para confirmar su cita de *${specialty}* con el Dr(a). *${doctorName}*:\n\n📅 Fecha: ${date}\n💰 Valor a cancelar: *$${amount} USD*\n🔗 Enlace de pago seguro:\n👉 ${link}\n\nUna vez realizado el pago, su turno queda confirmado inmediatamente. ¡Quedamos a su disposición!`;
        return 'https://wa.me/' + phone + '?text=' + encodeURIComponent(msg);
    }
}">
    <!-- Header with Tabs Navigation -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Validación y Gestión de Pagos</h1>
                <p class="text-xs text-slate-500">Valida comprobantes de transferencias bancarias y gestiona enlaces de pago DataLink (Datafast) para confirmar citas.</p>
            </div>

            <!-- Tab Switcher Navigation -->
            <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl shrink-0 self-start sm:self-center">
                <a href="{{ route('admin.transfers', ['tab' => 'transfers']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'transfers' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>🏦 Transferencias Bancarias</span>
                    @if($stats['pending'] > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-white">
                            {{ $stats['pending'] }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('admin.transfers', ['tab' => 'datalink']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab === 'datalink' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    <span>🔗 Enlaces DataLink</span>
                    @if($stats['datalink_pending'] > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $tab === 'datalink' ? 'bg-white text-purple-900' : 'bg-purple-600 text-white' }}">
                            {{ $stats['datalink_pending'] }}
                        </span>
                    @endif
                </a>
            </div>
        </div>

        @if($tab === 'transfers')
            <!-- Filter Stats Cards for Bank Transfers -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('admin.transfers') }}?tab=transfers&status=PENDING_VALIDATION" class="p-4 rounded-2xl border-2 transition-all flex items-center justify-between {{ $status === 'PENDING_VALIDATION' ? 'border-amber-400 bg-amber-50/50' : 'border-slate-100 bg-slate-50/50 hover:border-slate-200' }}">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-700 block">Pendientes de Validación</span>
                        <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $stats['pending'] }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </a>

                <a href="{{ route('admin.transfers') }}?tab=transfers&status=APPROVED" class="p-4 rounded-2xl border-2 transition-all flex items-center justify-between {{ $status === 'APPROVED' ? 'border-emerald-400 bg-emerald-50/50' : 'border-slate-100 bg-slate-50/50 hover:border-slate-200' }}">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block">Aprobadas</span>
                        <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $stats['approved'] }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </a>

                <a href="{{ route('admin.transfers') }}?tab=transfers&status=REJECTED" class="p-4 rounded-2xl border-2 transition-all flex items-center justify-between {{ $status === 'REJECTED' ? 'border-rose-400 bg-rose-50/50' : 'border-slate-100 bg-slate-50/50 hover:border-slate-200' }}">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-rose-700 block">Rechazadas</span>
                        <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $stats['rejected'] }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                </a>
            </div>
        @else
            <!-- Filter Stats Cards for DataLink Payments -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="{{ route('admin.transfers') }}?tab=datalink&datalink_status=PENDING" class="p-4 rounded-2xl border-2 transition-all flex items-center justify-between {{ $datalinkStatus === 'PENDING' ? 'border-purple-400 bg-purple-50/60' : 'border-slate-100 bg-slate-50/50 hover:border-slate-200' }}">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-purple-800 block">Pendientes de Pago / Emisión</span>
                        <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $stats['datalink_pending'] }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-lg">
                        🔗
                    </div>
                </a>

                <a href="{{ route('admin.transfers') }}?tab=datalink&datalink_status=PAID" class="p-4 rounded-2xl border-2 transition-all flex items-center justify-between {{ $datalinkStatus === 'PAID' ? 'border-emerald-400 bg-emerald-50/50' : 'border-slate-100 bg-slate-50/50 hover:border-slate-200' }}">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block">Pagados y Confirmados</span>
                        <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $stats['datalink_paid'] }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </a>

                <a href="{{ route('admin.transfers') }}?tab=datalink" class="p-4 rounded-2xl border-2 transition-all flex items-center justify-between {{ empty($datalinkStatus) ? 'border-blue-400 bg-blue-50/40' : 'border-slate-100 bg-slate-50/50 hover:border-slate-200' }}">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-600 block">Total Registros DataLink</span>
                        <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $stats['datalink_total'] }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold">
                        📊
                    </div>
                </a>
            </div>
        @endif
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

    <!-- TAB 1: TRANSFERS TABLE -->
    @if($tab === 'transfers')
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
                                    <span class="text-brand-primary text-[11px] font-mono">Cita #{{ $t->payment?->appointment?->code ?: 'N/D' }}</span>
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
                                            <button @click="selectedTransfer = {{ $t->toJson() }}; actionType = 'APPROVED'; validateModal = true" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition-colors cursor-pointer">
                                                Aprobar
                                            </button>
                                            <button @click="selectedTransfer = {{ $t->toJson() }}; actionType = 'REJECTED'; validateModal = true" class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] transition-colors cursor-pointer">
                                                Rechazar
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400">Validado por {{ $t->validator?->name ?? 'Administrador' }}</span>
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
    @endif

    <!-- TAB 2: DATALINK PAYMENTS TABLE -->
    @if($tab === 'datalink')
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden animate-fadeIn">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-purple-50/50 border-b border-purple-100 text-purple-900 font-bold uppercase tracking-wider">
                            <th class="py-4 px-6">Cita / Paciente</th>
                            <th class="py-4 px-6">Especialista / Horario</th>
                            <th class="py-4 px-6">Monto</th>
                            <th class="py-4 px-6">Estado DataLink</th>
                            <th class="py-4 px-6">Enlace Asignado</th>
                            <th class="py-4 px-6 text-right">Gestión Recepción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($datalinkPayments as $p)
                            @php
                                $app = $p->appointment;
                                $patient = $app?->patient;
                                $isPaid = $p->status->value === 'PAID';
                                $hasUrl = !empty($p->datalink_url);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Cita y Paciente -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-slate-900 block text-sm">{{ $patient ? $patient->full_name : 'N/D' }}</span>
                                        @if($app)
                                            <span class="text-brand-primary text-[10px] font-mono font-bold bg-brand-soft px-1.5 py-0.5 rounded">
                                                #{{ $app->code }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-slate-500 text-[11px] mt-0.5 space-x-2">
                                        <span>📱 {{ $patient?->phone ?: 'Sin teléfono' }}</span>
                                        <span>✉️ {{ $patient?->email ?: 'Sin email' }}</span>
                                    </div>
                                </td>

                                <!-- Especialista y Horario -->
                                <td class="py-4 px-6">
                                    <span class="font-bold text-slate-800 block text-xs">Dr(a). {{ $app?->doctor?->user?->name ?: 'N/D' }}</span>
                                    <span class="text-slate-500 text-[11px] block">{{ $app?->specialty?->name ?: 'Consulta' }}</span>
                                    <span class="text-slate-400 text-[10px] block mt-0.5">
                                        📅 {{ $app?->start_at ? $app->start_at->translatedFormat('d M Y, H:i') : 'Sin fecha' }}
                                    </span>
                                </td>

                                <!-- Monto -->
                                <td class="py-4 px-6">
                                    <span class="font-extrabold text-slate-900 block text-sm">${{ number_format($p->amount, 2) }}</span>
                                    <span class="text-slate-400 text-[10px] uppercase font-mono">{{ $p->currency }}</span>
                                </td>

                                <!-- Estado DataLink -->
                                <td class="py-4 px-6">
                                    @if($isPaid)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                            ✓ PAGADO
                                        </span>
                                        <span class="text-[10px] text-slate-400 block mt-0.5">
                                            {{ $p->paid_at ? $p->paid_at->format('d/m/Y H:i') : '' }}
                                        </span>
                                    @elseif($hasUrl)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">
                                            ✉️ Enlace Enviado
                                        </span>
                                        @if($p->datalink_sent_at)
                                            <span class="text-[10px] text-slate-400 block mt-0.5">
                                                Enviado: {{ $p->datalink_sent_at->format('d/m/Y H:i') }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                            🟡 Pendiente de Emisión
                                        </span>
                                        <span class="text-[10px] text-amber-700 block mt-0.5">Requiere link Datafast</span>
                                    @endif
                                </td>

                                <!-- Enlace Asignado -->
                                <td class="py-4 px-6 max-w-[200px]" x-data="{ copiedRow: false }">
                                    @if($hasUrl)
                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ $p->datalink_url }}" target="_blank" class="text-xs font-mono text-purple-700 hover:text-purple-900 truncate underline max-w-[140px]" title="{{ $p->datalink_url }}">
                                                {{ $p->datalink_url }}
                                            </a>
                                            <button type="button" @click="navigator.clipboard.writeText('{{ $p->datalink_url }}'); copiedRow = true; setTimeout(() => copiedRow = false, 2000)" class="p-1 text-slate-400 hover:text-slate-600 rounded-md hover:bg-slate-100 transition-colors cursor-pointer" title="Copiar enlace">
                                                <span x-show="!copiedRow">📋</span>
                                                <span x-show="copiedRow" class="text-emerald-600 text-[10px]">✓</span>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Sin enlace cargado</span>
                                    @endif
                                </td>

                                <!-- Acciones -->
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        <!-- Enviar WhatsApp -->
                                        <button type="button" @click="openDataLinkWa({{ $p->toJson() }})" class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition-colors cursor-pointer inline-flex items-center gap-1 shadow-2xs" title="Enviar enlace o mensaje por WhatsApp">
                                            <span>💬</span>
                                            <span>WhatsApp</span>
                                        </button>

                                        <!-- Enviar Correo -->
                                        <button type="button" @click="openDataLinkEmail({{ $p->toJson() }})" class="px-2.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-[11px] transition-colors cursor-pointer inline-flex items-center gap-1 shadow-2xs" title="Ingresar enlace y enviar correo oficial al paciente">
                                            <span>✉️</span>
                                            <span>Correo</span>
                                        </button>

                                        <!-- Marcar como Pagado -->
                                        @if(!$isPaid)
                                            <form action="{{ route('admin.transfers.datalink.mark_paid', $p) }}" method="POST" onsubmit="return confirm('¿Confirmas que el paciente completó el pago mediante el enlace DataLink? La cita médica quedará confirmada.');" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-[11px] transition-colors cursor-pointer inline-flex items-center gap-1 shadow-2xs" title="Marcar como pagado y confirmar cita">
                                                    <span>✓</span>
                                                    <span>Pagado</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-slate-400">
                                    No se encontraron pagos con método Enlace DataLink en el estado seleccionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $datalinkPayments->links() }}
            </div>
        </div>
    @endif

    <!-- VALIDATION MODAL FOR BANK TRANSFERS -->
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
                            <button type="button" @click="validateModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold cursor-pointer">
                                Cancelar
                            </button>
                            <button type="submit" :class="actionType === 'APPROVED' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700'" class="px-4 py-2 rounded-xl text-white font-bold shadow-sm cursor-pointer">
                                Confirmar Acción
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </div>
    </div>

    <!-- MODAL 1: ENVIAR ENLACE DATALINK POR CORREO -->
    <div x-show="dataLinkEmailModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl space-y-4 animate-scaleUp" @click.away="dataLinkEmailModal = false">
            <template x-if="selectedDatalink">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center font-bold text-lg">
                                ✉️
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Enviar Enlace DataLink por Correo</h3>
                                <p class="text-xs text-slate-500">Notifica al paciente con el botón oficial de pago Datafast</p>
                            </div>
                        </div>
                        <button type="button" @click="dataLinkEmailModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Datos del Paciente / Cita -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1.5 mb-4">
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-medium">Paciente:</span>
                            <span class="font-bold text-slate-800" x-text="selectedDatalink.appointment?.patient ? (selectedDatalink.appointment.patient.first_name + ' ' + selectedDatalink.appointment.patient.last_name) : 'N/D'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-medium">Correo Destinatario:</span>
                            <span class="font-bold text-purple-700 font-mono" x-text="selectedDatalink.appointment?.patient?.email || 'Sin correo registrado'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 font-medium">Cita / Monto:</span>
                            <span class="font-bold text-slate-900" x-text="'#' + (selectedDatalink.appointment?.code || '') + ' — $' + Number(selectedDatalink.amount).toFixed(2) + ' USD'"></span>
                        </div>
                    </div>

                    <form :action="'{{ url('/admin/transferencias/datalink') }}/' + selectedDatalink.id + '/enviar-correo'" method="POST" class="space-y-4 text-xs">
                        @csrf
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                                URL del Enlace DataLink (Generado en Portal Datafast) *
                            </label>
                            <input type="url" name="datalink_url" x-model="datalinkUrl" required class="w-full rounded-xl border-slate-200 text-xs p-3 font-mono focus:border-purple-600 focus:ring-purple-600 bg-slate-50 focus:bg-white" placeholder="https://datalink.datafast.com.ec/..." />
                            <p class="text-[11px] text-slate-400 mt-1">Pega la URL del link de cobro generado en el portal oficial de Datafast.</p>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                                Instrucciones Adicionales o Notas (Opcional)
                            </label>
                            <textarea name="notes" x-model="datalinkNotes" rows="2" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-purple-600 focus:ring-purple-600 bg-slate-50 focus:bg-white" placeholder="Ej: Recuerda cancelar antes de las 18:00 para garantizar el turno..."></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                            <button type="button" @click="dataLinkEmailModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold cursor-pointer">
                                Cancelar
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold shadow-md shadow-purple-600/20 cursor-pointer flex items-center gap-1.5">
                                <span>✉️ Enviar Correo Oficial</span>
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </div>
    </div>

    <!-- MODAL 2: GESTIONAR Y ENVIAR WHATSAPP CON ENLACE DATALINK -->
    <div x-show="dataLinkWaModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl space-y-4 animate-scaleUp" @click.away="dataLinkWaModal = false">
            <template x-if="selectedDatalink">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg">
                                💬
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Compartir DataLink por WhatsApp</h3>
                                <p class="text-xs text-slate-500">Envía el link al teléfono registrado del paciente</p>
                            </div>
                        </div>
                        <button type="button" @click="dataLinkWaModal = false" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="space-y-4 text-xs">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                                URL del Enlace DataLink (Datafast)
                            </label>
                            <input type="url" x-model="datalinkUrl" class="w-full rounded-xl border-slate-200 text-xs p-3 font-mono focus:border-emerald-600 focus:ring-emerald-600 bg-slate-50 focus:bg-white" placeholder="https://datalink.datafast.com.ec/..." />
                            <p class="text-[11px] text-slate-400 mt-1">El mensaje a continuación se actualizará en tiempo real con este enlace.</p>
                        </div>

                        <!-- Vista Previa del Mensaje de WhatsApp -->
                        <div>
                            <span class="block font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Vista Previa del Mensaje:</span>
                            <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200 font-mono text-[11px] text-slate-800 whitespace-pre-wrap leading-relaxed">
¡Hola <span class="font-bold" x-text="selectedDatalink.appointment?.patient ? (selectedDatalink.appointment.patient.first_name + ' ' + selectedDatalink.appointment.patient.last_name) : 'Paciente'"></span>! 👋 Le saludamos desde *{{ config('branding.client_name', 'Clínica Sur Hospital') }}*.

Le compartimos su *Enlace de Pago DataLink (Datafast)* para confirmar su cita médica:

📅 Cita: #<span x-text="selectedDatalink.appointment?.code"></span>
💰 Monto: *$<span x-text="Number(selectedDatalink.amount).toFixed(2)"></span> USD*
🔗 Enlace de pago seguro:
👉 <span class="text-emerald-700 underline font-bold" x-text="datalinkUrl || '[Pega aquí la URL de Datafast]'"></span>

Una vez realizado el pago, su turno queda confirmado inmediatamente. ¡Quedamos a su disposición!</div>
                        </div>

                        <!-- Acciones -->
                        <div class="flex flex-col sm:flex-row items-center justify-end gap-2 pt-3 border-t border-slate-100">
                            <button type="button" @click="navigator.clipboard.writeText(getWhatsAppUrl()); copiedWa = true; setTimeout(() => copiedWa = false, 2500)" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors cursor-pointer text-center">
                                <span x-show="!copiedWa">📋 Copiar Enlace Directo</span>
                                <span x-show="copiedWa" class="text-emerald-600">✓ Enlace Copiado</span>
                            </button>
                            <a :href="getWhatsAppUrl()" target="_blank" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-md shadow-emerald-600/20 text-center inline-flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>💬 Abrir Chat de WhatsApp</span>
                            </a>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
