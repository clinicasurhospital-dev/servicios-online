@extends('layouts.admin')

@section('title', 'Directorio de Pacientes — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn" x-data="{
    newPatientModal: false,
    editPatientModal: false,
    editPatientData: {
        id: null,
        first_name: '',
        last_name: '',
        identification_type: 'CEDULA',
        identification_number: '',
        email: '',
        phone: '',
        birth_date: '',
        gender: 'OTHER',
        address: '',
        medical_notes: ''
    },
    openEditModal(p) {
        this.editPatientData = {
            id: p.id,
            first_name: p.first_name || '',
            last_name: p.last_name || '',
            identification_type: p.identification_type?.value || p.identification_type || 'CEDULA',
            identification_number: p.identification_number || '',
            email: p.user?.email || p.email || '',
            phone: p.phone || '',
            birth_date: p.birth_date ? p.birth_date.substring(0, 10) : '',
            gender: p.gender?.value || p.gender || 'OTHER',
            address: p.address || '',
            medical_notes: p.medical_notes || ''
        };
        this.editPatientModal = true;
    }
}">
    <!-- Header with Search & Actions -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Directorio de Pacientes</h1>
            <p class="text-xs text-slate-500">Historial, información clínica y datos de contacto de pacientes registrados.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ route('admin.patients') }}" class="flex items-center space-x-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre, CC, teléfono o email..." class="rounded-xl border-slate-200 text-xs p-2.5 w-60 sm:w-72 focus:border-brand-primary" />
                <button type="submit" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                    Buscar
                </button>
                @if($search)
                    <a href="{{ route('admin.patients') }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-500 text-xs font-bold hover:bg-slate-50">
                        Limpiar
                    </a>
                @endif
            </form>

            <button type="button" @click="newPatientModal = true" class="px-4 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 flex items-center space-x-2 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>+ Registrar Paciente</span>
            </button>
        </div>
    </div>

    <!-- PATIENT TABLE -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-4 px-6">Paciente</th>
                        <th class="py-4 px-6">Identificación</th>
                        <th class="py-4 px-6">Contacto</th>
                        <th class="py-4 px-6">Citas Registradas</th>
                        <th class="py-4 px-6">Fecha Registro</th>
                        <th class="py-4 px-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($patients as $p)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold text-sm shrink-0">
                                        {{ substr($p->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block text-sm">{{ $p->first_name }} {{ $p->last_name }}</span>
                                        <span class="text-slate-400 text-[11px]">{{ $p->user?->email ?: $p->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-800">{{ $p->identification_type?->value ?? 'CEDULA' }}</span>
                                <span class="text-slate-600 block text-[11px] font-mono">{{ $p->identification_number }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-slate-800 font-medium block">{{ $p->phone }}</span>
                                <span class="text-slate-400 text-[11px]">Nacimiento: {{ $p->birth_date?->format('d/m/Y') ?? 'N/D' }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-brand-soft text-brand-primary">
                                    {{ $p->appointments_count }} citas
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                {{ $p->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end space-x-1.5">
                                    <button type="button" @click="openEditModal({{ json_encode($p) }})" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-brand-soft hover:text-brand-primary font-bold text-slate-700 text-xs transition-colors cursor-pointer">
                                        Editar
                                    </button>
                                    <form method="POST" action="{{ route('admin.patients.destroy', $p) }}" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este paciente?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Eliminar paciente">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400">
                                No se encontraron pacientes con los criterios de búsqueda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $patients->links() }}
        </div>
    </div>

    <!-- MODAL: REGISTRAR PACIENTE -->
    <div x-show="newPatientModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
        <div @click.away="newPatientModal = false" class="bg-white rounded-3xl p-6 max-w-xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100 space-y-5 animate-scaleUp custom-scrollbar">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">Registrar Nuevo Paciente</h3>
                </div>
                <button type="button" @click="newPatientModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.patients.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nombres *</label>
                        <input type="text" name="first_name" required placeholder="Ej. Juan Carlos" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Apellidos *</label>
                        <input type="text" name="last_name" required placeholder="Ej. Pérez Mendoza" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Tipo Identificación *</label>
                        <select name="identification_type" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary">
                            <option value="CEDULA">Cédula de Identidad</option>
                            <option value="PASAPORTE">Pasaporte</option>
                            <option value="RUC">RUC</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Número de Identificación *</label>
                        <input type="text" name="identification_number" required placeholder="0987654321" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Correo Electrónico *</label>
                        <input type="email" name="email" required placeholder="paciente@correo.com" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Teléfono Móvil *</label>
                        <input type="text" name="phone" required placeholder="+593 99 000 0000" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Fecha de Nacimiento</label>
                        <input type="date" name="birth_date" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Género</label>
                        <select name="gender" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary">
                            <option value="MALE">Masculino</option>
                            <option value="FEMALE">Femenino</option>
                            <option value="OTHER">Otro / No especifica</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Dirección Domiciliaria</label>
                    <input type="text" name="address" placeholder="Ciudad, Barrio, Calle principal y secundaria" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Notas Médicas / Alergias</label>
                    <textarea name="medical_notes" rows="2" placeholder="Alergias conocidas, antecedentes relevantes..." class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary"></textarea>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2">
                    <button type="button" @click="newPatientModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary text-white font-bold text-xs shadow-md shadow-brand-primary/20 hover:bg-brand-primary-dark cursor-pointer">Guardar Paciente</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDITAR PACIENTE -->
    <div x-show="editPatientModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
        <div @click.away="editPatientModal = false" class="bg-white rounded-3xl p-6 max-w-xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100 space-y-5 animate-scaleUp custom-scrollbar">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">Editar Ficha del Paciente</h3>
                </div>
                <button type="button" @click="editPatientModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" :action="'/admin/pacientes/' + editPatientData.id" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nombres *</label>
                        <input type="text" name="first_name" x-model="editPatientData.first_name" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Apellidos *</label>
                        <input type="text" name="last_name" x-model="editPatientData.last_name" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Tipo Identificación *</label>
                        <select name="identification_type" x-model="editPatientData.identification_type" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary">
                            <option value="CEDULA">Cédula de Identidad</option>
                            <option value="PASAPORTE">Pasaporte</option>
                            <option value="RUC">RUC</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Número de Identificación *</label>
                        <input type="text" name="identification_number" x-model="editPatientData.identification_number" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Correo Electrónico *</label>
                        <input type="email" name="email" x-model="editPatientData.email" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Teléfono Móvil *</label>
                        <input type="text" name="phone" x-model="editPatientData.phone" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Fecha de Nacimiento</label>
                        <input type="date" name="birth_date" x-model="editPatientData.birth_date" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Género</label>
                        <select name="gender" x-model="editPatientData.gender" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary">
                            <option value="MALE">Masculino</option>
                            <option value="FEMALE">Femenino</option>
                            <option value="OTHER">Otro / No especifica</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Dirección Domiciliaria</label>
                    <input type="text" name="address" x-model="editPatientData.address" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Notas Médicas / Alergias</label>
                    <textarea name="medical_notes" x-model="editPatientData.medical_notes" rows="2" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary"></textarea>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2">
                    <button type="button" @click="editPatientModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary text-white font-bold text-xs shadow-md shadow-brand-primary/20 hover:bg-brand-primary-dark cursor-pointer">Actualizar Paciente</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
