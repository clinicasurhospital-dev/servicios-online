@extends('layouts.admin')

@section('title', 'Cuerpo Médico & Horarios — ' . config('branding.app_name'))

@section('content')
<script>
    window.doctorsList = @json($doctors->items());
</script>

<div class="space-y-6 animate-fadeIn" x-data="{ 
    newDoctorModal: false, 
    editDoctorModal: false,
    schedulesModal: false, 
    selectedDoctor: null,
    editDoctorData: {
        id: null,
        name: '',
        email: '',
        phone: '',
        license_number: '',
        room_office: '',
        default_slot_duration_minutes: 30,
        default_consultation_price: 35,
        bio: '',
        specialty_ids: []
    },
    schedulesData: [
        { day: 1, name: 'Lunes', enabled: true, blocks: [{ start: '08:00', end: '13:00' }] },
        { day: 2, name: 'Martes', enabled: true, blocks: [{ start: '08:00', end: '13:00' }] },
        { day: 3, name: 'Miércoles', enabled: true, blocks: [{ start: '08:00', end: '13:00' }] },
        { day: 4, name: 'Jueves', enabled: true, blocks: [{ start: '08:00', end: '13:00' }] },
        { day: 5, name: 'Viernes', enabled: true, blocks: [{ start: '08:00', end: '13:00' }] },
        { day: 6, name: 'Sábado', enabled: false, blocks: [{ start: '08:00', end: '12:00' }] },
        { day: 0, name: 'Domingo', enabled: false, blocks: [{ start: '08:00', end: '12:00' }] }
    ],
    addBlock(dayItem) {
        dayItem.enabled = true;
        dayItem.blocks.push({ start: '14:00', end: '18:00' });
    },
    removeBlock(dayItem, bIdx) {
        if (dayItem.blocks.length > 1) {
            dayItem.blocks.splice(bIdx, 1);
        } else {
            dayItem.enabled = false;
        }
    },
    openEditModal(doc) {
        this.selectedDoctor = doc;
        this.editDoctorData = {
            id: doc.id,
            name: doc.user?.name || '',
            email: doc.user?.email || '',
            phone: doc.user?.phone || '',
            license_number: doc.license_number || '',
            room_office: doc.room_office || '',
            default_slot_duration_minutes: doc.default_slot_duration_minutes || 30,
            default_consultation_price: doc.default_consultation_price || 35,
            bio: doc.bio || '',
            specialty_ids: (doc.specialties || []).map(s => s.id)
        };
        this.editDoctorModal = true;
    },
    openScheduleModal(doctorId) {
        const doc = (window.doctorsList || []).find(d => d.id === doctorId);
        if (!doc) return;
        this.selectedDoctor = doc;
        // Map existing schedules
        this.schedulesData.forEach(dayItem => {
            const foundSchedules = (doc.schedules || []).filter(s => s.day_of_week === dayItem.day && s.is_active);
            if (foundSchedules.length > 0) {
                dayItem.enabled = true;
                dayItem.blocks = foundSchedules.map(s => ({
                    start: (s.start_time || '08:00').substring(0, 5),
                    end: (s.end_time || '13:00').substring(0, 5)
                }));
            } else {
                dayItem.enabled = false;
                dayItem.blocks = [{ start: '08:00', end: '13:00' }];
            }
        });
        this.schedulesModal = true;
    }
}">
    <!-- Header with Filters & Actions -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            @if(auth()->user()->isDoctor())
                <h1 class="text-xl font-bold text-slate-900">Mi Perfil Profesional & Horarios</h1>
                <p class="text-xs text-slate-500">Configuración de mis horarios de atención médica y datos profesionales.</p>
            @else
                <h1 class="text-xl font-bold text-slate-900">Especialistas Médicos & Horarios</h1>
                <p class="text-xs text-slate-500">Gestión de profesionales de la salud, registros médicos y asignación de horarios de atención.</p>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if(!auth()->user()->isDoctor())
                <form method="GET" action="{{ route('admin.doctors') }}" class="flex flex-wrap items-center gap-2">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar por nombre o registro..." class="rounded-xl border-slate-200 text-xs p-2.5 w-44 sm:w-56 focus:border-brand-primary" />
                    <select name="specialty_id" class="rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary">
                        <option value="">Todas las Especialidades</option>
                        @foreach($specialties as $s)
                            <option value="{{ $s->id }}" {{ $specialtyId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                        Filtrar
                    </button>
                </form>

                <button type="button" @click="newDoctorModal = true" class="px-4 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 flex items-center space-x-2 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    <span>+ Registrar Médico</span>
                </button>
            @endif
        </div>
    </div>

    <!-- DOCTORS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($doctors as $doc)
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
                <div>
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-primary to-brand-primary-dark text-white flex items-center justify-center font-bold text-lg shadow-md shrink-0">
                                {{ substr($doc->user->name, 0, 2) }}
                            </div>
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-brand-primary">
                                    {{ $doc->specialties->first()?->name ?? 'General' }}
                                </span>
                                <h3 class="font-bold text-slate-900 text-base">{{ $doc->user->name }}</h3>
                                <p class="text-xs text-slate-400 font-mono">Reg. {{ $doc->license_number }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-1">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $doc->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                {{ $doc->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                            @if(!auth()->user()->isDoctor())
                                <span class="text-[10px] text-slate-400 font-bold">${{ number_format($doc->default_consultation_price, 2) }}</span>
                            @endif
                        </div>
                    </div>

                    <p class="text-xs text-slate-500 line-clamp-2 mb-4 leading-relaxed">
                        {{ $doc->bio ?: 'Especialista clínico en Clínica Sur Hospital.' }}
                    </p>

                    <!-- Weekly Schedules Badges -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 block">Horarios Asignados:</span>
                            <button type="button" @click="openScheduleModal({{ $doc->id }})" class="text-[11px] font-bold text-brand-primary hover:underline flex items-center space-x-1 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Configurar</span>
                            </button>
                        </div>
                        @if($doc->schedules->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5">
                                @php
                                    $dayNames = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 0 => 'Dom'];
                                @endphp
                                @foreach($doc->schedules as $sch)
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-white text-slate-700 border border-slate-200 shadow-2xs">
                                        {{ $dayNames[$sch->day_of_week] ?? 'Día' }}: {{ substr($sch->start_time, 0, 5) }}-{{ substr($sch->end_time, 0, 5) }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-xs text-slate-400 italic">Sin horarios configurados</span>
                        @endif
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex flex-col gap-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-medium">Turnos: {{ $doc->default_slot_duration_minutes ?? 30 }} min</span>
                        <div class="flex items-center space-x-1.5">
                            <button type="button" @click="openEditModal({{ json_encode($doc) }})" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-brand-soft hover:text-brand-primary text-slate-700 font-bold transition-colors cursor-pointer text-xs">
                                Editar
                            </button>
                            @if(!auth()->user()->isDoctor())
                                <form method="POST" action="{{ route('admin.doctors.toggle', $doc) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 rounded-xl {{ $doc->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} font-bold text-xs transition-colors cursor-pointer" title="{{ $doc->is_active ? 'Desactivar médico' : 'Activar médico' }}">
                                        {{ $doc->is_active ? 'Pausar' : 'Activar' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.doctors.destroy', $doc) }}" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar o desactivar este médico?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Eliminar médico">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 text-slate-400">
                No se encontraron especialistas con los filtros ingresados.
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $doctors->links() }}
    </div>

    <!-- MODAL: REGISTRAR NUEVO MÉDICO -->
    @if(!auth()->user()->isDoctor())
        <div x-show="newDoctorModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
            <div @click.away="newDoctorModal = false" class="bg-white rounded-3xl p-6 max-w-xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100 space-y-5 animate-scaleUp custom-scrollbar">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-9 h-9 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">Registrar Nuevo Especialista Médico</h3>
                    </div>
                    <button type="button" @click="newDoctorModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.doctors.store') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nombre Completo *</label>
                            <input type="text" name="name" required placeholder="Dr(a). Nombre y Apellido" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Email Profesional *</label>
                            <input type="email" name="email" required placeholder="medico@surhospital.com" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Teléfono Móvil</label>
                            <input type="text" name="phone" placeholder="+593 99 000 0000" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Registro Médico / MSP *</label>
                            <input type="text" name="license_number" required placeholder="MSP-0987654" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Consultorio</label>
                            <input type="text" name="room_office" placeholder="Cons. 204" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Precio Consulta ($)</label>
                            <input type="number" step="0.5" name="default_consultation_price" value="35.00" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Turno (Min) *</label>
                            <input type="number" name="default_slot_duration_minutes" required value="30" min="10" step="5" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Especialidades Asignadas *</label>
                        <div class="grid grid-cols-2 gap-2 max-h-36 overflow-y-auto p-3 rounded-2xl bg-slate-50 border border-slate-200/80 custom-scrollbar">
                            @foreach($specialties as $sp)
                                <label class="flex items-center space-x-2 text-xs text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="specialty_ids[]" value="{{ $sp->id }}" class="rounded text-brand-primary focus:ring-brand-primary" />
                                    <span>{{ $sp->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Perfil / Biografía</label>
                        <textarea name="bio" rows="2" placeholder="Formación profesional, trayectoria y subespecialidades..." class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary"></textarea>
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2">
                        <button type="button" @click="newDoctorModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">Cancelar</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary text-white font-bold text-xs shadow-md shadow-brand-primary/20 hover:bg-brand-primary-dark cursor-pointer">Guardar Médico</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL: EDITAR MÉDICO -->
    <div x-show="editDoctorModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
        <div @click.away="editDoctorModal = false" class="bg-white rounded-3xl p-6 max-w-xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100 space-y-5 animate-scaleUp custom-scrollbar">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">{{ auth()->user()->isDoctor() ? 'Editar Mi Información Profesional' : 'Editar Datos del Especialista' }}</h3>
                </div>
                <button type="button" @click="editDoctorModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" :action="'/admin/medicos/' + editDoctorData.id" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nombre Completo *</label>
                        <input type="text" name="name" x-model="editDoctorData.name" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Email Profesional *</label>
                        <input type="email" name="email" x-model="editDoctorData.email" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Teléfono Móvil</label>
                        <input type="text" name="phone" x-model="editDoctorData.phone" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Registro Médico / MSP *</label>
                        <input type="text" name="license_number" x-model="editDoctorData.license_number" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <div class="grid grid-cols-1 {{ auth()->user()->isDoctor() ? 'sm:grid-cols-2' : 'sm:grid-cols-3' }} gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Consultorio</label>
                        <input type="text" name="room_office" x-model="editDoctorData.room_office" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                    @if(!auth()->user()->isDoctor())
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Precio Consulta ($)</label>
                            <input type="number" step="0.5" name="default_consultation_price" x-model="editDoctorData.default_consultation_price" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                        </div>
                    @endif
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Turno (Min) *</label>
                        <input type="number" name="default_slot_duration_minutes" x-model="editDoctorData.default_slot_duration_minutes" required min="10" step="5" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Especialidades Asignadas *</label>
                    <div class="grid grid-cols-2 gap-2 max-h-36 overflow-y-auto p-3 rounded-2xl bg-slate-50 border border-slate-200/80 custom-scrollbar">
                        @foreach($specialties as $sp)
                            <label class="flex items-center space-x-2 text-xs text-slate-700 cursor-pointer">
                                <input type="checkbox" name="specialty_ids[]" value="{{ $sp->id }}" :checked="editDoctorData.specialty_ids.includes({{ $sp->id }})" class="rounded text-brand-primary focus:ring-brand-primary" />
                                <span>{{ $sp->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Perfil / Biografía</label>
                    <textarea name="bio" x-model="editDoctorData.bio" rows="2" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary"></textarea>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2">
                    <button type="button" @click="editDoctorModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary text-white font-bold text-xs shadow-md shadow-brand-primary/20 hover:bg-brand-primary-dark cursor-pointer">Actualizar Médico</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: ASIGNAR / CONFIGURAR HORARIOS SEMANALES -->
    <div x-show="schedulesModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
        <div @click.away="schedulesModal = false" class="bg-white rounded-3xl p-6 max-w-xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100 space-y-5 animate-scaleUp custom-scrollbar">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-9 h-9 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Configurar Horario Semanal</h3>
                        <p class="text-xs text-slate-500">Médico: <span class="font-bold text-brand-primary" x-text="selectedDoctor?.user?.name"></span></p>
                    </div>
                </div>
                <button type="button" @click="schedulesModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form method="POST" :action="'/admin/medicos/' + selectedDoctor?.id + '/horarios'" class="space-y-4">
                @csrf
                <p class="text-xs text-slate-500">Configura los días y múltiples franjas horarias de atención para este especialista (ej: mañana de 08:00 a 09:00 y tarde de 11:00 a 11:30):</p>

                <div class="space-y-3">
                    <template x-for="(dayItem, dayIdx) in schedulesData" :key="dayItem.day">
                        <div class="p-3.5 rounded-2xl border transition-all space-y-2.5" :class="dayItem.enabled ? 'border-brand-primary/40 bg-brand-soft/10 shadow-2xs' : 'border-slate-100 bg-slate-50/50 opacity-60'">
                            <div class="flex items-center justify-between">
                                <label class="flex items-center space-x-3 cursor-pointer">
                                    <input type="checkbox" x-model="dayItem.enabled" class="rounded text-brand-primary focus:ring-brand-primary h-4 w-4" />
                                    <span class="font-bold text-xs text-slate-800" x-text="dayItem.name"></span>
                                </label>

                                <div class="flex items-center space-x-2" x-show="dayItem.enabled">
                                    <button type="button" @click="addBlock(dayItem)" class="inline-flex items-center gap-1 text-[11px] font-bold text-brand-primary hover:text-brand-primary-dark bg-white hover:bg-brand-soft px-2.5 py-1 rounded-lg border border-brand-primary/20 shadow-2xs transition-all cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        <span>+ Agregar horario</span>
                                    </button>
                                </div>

                                <span class="text-xs text-slate-400 italic" x-show="!dayItem.enabled">No atiende</span>
                            </div>

                            <!-- List of multiple blocks for this day -->
                            <div class="space-y-2 pl-7 pt-1" x-show="dayItem.enabled">
                                <template x-for="(block, bIdx) in dayItem.blocks" :key="bIdx">
                                    <div class="flex items-center justify-between gap-2 p-2 rounded-xl bg-white border border-slate-200/80 shadow-2xs">
                                        <input type="hidden" :name="'schedules[' + dayItem.day + '_' + bIdx + '][day_of_week]'" :value="dayItem.day" />
                                        <input type="hidden" :name="'schedules[' + dayItem.day + '_' + bIdx + '][enabled]'" value="1" />

                                        <div class="flex items-center space-x-2">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase">Turno <span x-text="bIdx + 1"></span>:</span>
                                            <div class="flex items-center space-x-1">
                                                <span class="text-[11px] text-slate-500 font-medium">De</span>
                                                <input type="time" :name="'schedules[' + dayItem.day + '_' + bIdx + '][start_time]'" x-model="block.start" required class="rounded-lg border-slate-200 text-xs p-1.5 w-24 font-bold bg-slate-50 focus:bg-white" />
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <span class="text-[11px] text-slate-500 font-medium">a</span>
                                                <input type="time" :name="'schedules[' + dayItem.day + '_' + bIdx + '][end_time]'" x-model="block.end" required class="rounded-lg border-slate-200 text-xs p-1.5 w-24 font-bold bg-slate-50 focus:bg-white" />
                                            </div>
                                        </div>

                                        <button type="button" @click="removeBlock(dayItem, bIdx)" class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Eliminar este horario">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2">
                    <button type="button" @click="schedulesModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary text-white font-bold text-xs shadow-md shadow-brand-primary/20 hover:bg-brand-primary-dark cursor-pointer">Guardar Horarios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
