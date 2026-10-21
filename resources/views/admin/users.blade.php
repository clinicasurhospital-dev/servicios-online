@extends('layouts.admin')

@section('title', 'Directorio General de Usuarios — ' . config('branding.app_name'))

@section('content')
<script>
    window.usersList = @json($users->items());
</script>

<div class="space-y-6 animate-fadeIn" x-data="{
    newUserModal: false,
    editUserModal: false,
    viewUserModal: false,
    selectedUser: null,
    selectedRole: 'PATIENT',
    editUserData: {
        id: null,
        name: '',
        email: '',
        phone: '',
        role: 'PATIENT',
        is_active: 1,
        password: ''
    },
    openViewModal(user) {
        this.selectedUser = user;
        this.viewUserModal = true;
    },
    openEditModal(user) {
        this.selectedUser = user;
        this.editUserData = {
            id: user.id,
            name: user.name || '',
            email: user.email || '',
            phone: user.phone || '',
            role: (user.roles && user.roles.length > 0) ? user.roles[0].name : 'PATIENT',
            is_active: user.is_active ? 1 : 0,
            password: ''
        };
        this.editUserModal = true;
    }
}">
    <!-- Top Stats Counter Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5 sm:gap-4">
        <!-- Total Users -->
        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between hover:shadow-sm transition-shadow">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider">Total</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-900">{{ $stats['total'] }}</div>
                <div class="text-[11px] text-emerald-600 font-semibold">{{ $stats['active'] }} activos</div>
            </div>
        </div>

        <!-- Admins -->
        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between hover:shadow-sm transition-shadow">
            <div class="flex items-center justify-between text-indigo-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Administradores</span>
                <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-900">{{ $stats['admins'] }}</div>
                <div class="text-[11px] text-slate-500 font-medium">Control total</div>
            </div>
        </div>

        <!-- Receptionists -->
        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between hover:shadow-sm transition-shadow">
            <div class="flex items-center justify-between text-emerald-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Recepción</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-900">{{ $stats['receptionists'] }}</div>
                <div class="text-[11px] text-slate-500 font-medium">Atención y citas</div>
            </div>
        </div>

        <!-- General Doctors -->
        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between hover:shadow-sm transition-shadow">
            <div class="flex items-center justify-between text-blue-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Médicos</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-900">{{ $stats['doctors'] }}</div>
                <div class="text-[11px] text-slate-500 font-medium">Medicina general</div>
            </div>
        </div>

        <!-- Specialists -->
        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between hover:shadow-sm transition-shadow">
            <div class="flex items-center justify-between text-cyan-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Especialistas</span>
                <div class="w-8 h-8 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-900">{{ $stats['specialists'] }}</div>
                <div class="text-[11px] text-slate-500 font-medium">Subespecialidades</div>
            </div>
        </div>

        <!-- Patients -->
        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-xs flex flex-col justify-between hover:shadow-sm transition-shadow">
            <div class="flex items-center justify-between text-amber-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pacientes</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="mt-2">
                <div class="text-2xl font-black text-slate-900">{{ $stats['patients'] }}</div>
                <div class="text-[11px] text-slate-500 font-medium">Expedientes activos</div>
            </div>
        </div>
    </div>

    <!-- Header & Action Bar -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-xl font-bold text-slate-900">Módulo General de Usuarios</h1>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-brand-soft text-brand-primary">
                    {{ $users->total() }} registros
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">
                Almacén integral de usuarios: pacientes, administradores, recepcionistas, médicos y especialistas con sus roles y credenciales.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <form method="GET" action="{{ route('admin.users') }}" class="flex flex-wrap items-center gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Buscar nombre, correo, cédula, teléfono..."
                    class="rounded-xl border-slate-200 text-xs p-2.5 w-56 sm:w-64 focus:border-brand-primary focus:ring-brand-primary"
                />

                <select name="role" class="rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary focus:ring-brand-primary font-medium text-slate-700">
                    <option value="">Todos los Roles</option>
                    @foreach($roles as $r)
                        @php
                            $rLabel = match($r->name) {
                                'SUPER_ADMIN' => 'Super Administrador',
                                'ADMIN' => 'Administrador',
                                'RECEPTION' => 'Recepcionista',
                                'DOCTOR' => 'Médico',
                                'SPECIALIST' => 'Especialista',
                                'PATIENT' => 'Paciente',
                                default => $r->name
                            };
                        @endphp
                        <option value="{{ $r->name }}" {{ $roleFilter === $r->name ? 'selected' : '' }}>
                            {{ $rLabel }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="rounded-xl border-slate-200 text-xs p-2.5 focus:border-brand-primary focus:ring-brand-primary font-medium text-slate-700">
                    <option value="">Todos los Estados</option>
                    <option value="1" {{ $statusFilter === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ $statusFilter === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>

                <button type="submit" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    Filtrar
                </button>

                @if($search || $roleFilter || $statusFilter !== null)
                    <a href="{{ route('admin.users') }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-500 text-xs font-bold hover:bg-slate-50 transition-colors">
                        Limpiar
                    </a>
                @endif
            </form>

            <button
                type="button"
                @click="newUserModal = true"
                class="px-4 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 flex items-center space-x-2 transition-all cursor-pointer transform active:scale-95 shrink-0"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>+ Registrar Usuario</span>
            </button>
        </div>
    </div>

    <!-- USERS DATA TABLE -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                        <th class="py-4 px-6">Usuario & Cuenta</th>
                        <th class="py-4 px-6">Rol Asignado</th>
                        <th class="py-4 px-6">Perfil Vinculado</th>
                        <th class="py-4 px-6">Contacto</th>
                        <th class="py-4 px-6">Estado</th>
                        <th class="py-4 px-6">Fecha Registro</th>
                        <th class="py-4 px-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        @php
                            $primaryRole = $u->roles->first()?->name;
                            $roleLabel = $u->role_name;
                            $roleBadgeClass = $u->role_badge_classes;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- User info & avatar -->
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-sm shrink-0 shadow-2xs {{ $primaryRole === 'PATIENT' ? 'bg-amber-100 text-amber-700' : ($primaryRole === 'DOCTOR' || $primaryRole === 'SPECIALIST' ? 'bg-blue-100 text-blue-700' : 'bg-brand-soft text-brand-primary') }}">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center space-x-1.5">
                                            <span class="font-bold text-slate-900 block text-sm truncate max-w-[190px] sm:max-w-xs">{{ $u->name }}</span>
                                            @if($u->id === auth()->id())
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">Tú</span>
                                            @endif
                                        </div>
                                        <span class="text-slate-400 text-[11px] block truncate font-mono">{{ $u->email }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Role Badge -->
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $roleBadgeClass }}">
                                    @if($primaryRole === 'SUPER_ADMIN')
                                        👑 Super Administrador
                                    @elseif($primaryRole === 'ADMIN')
                                        🛡️ Administrador
                                    @elseif($primaryRole === 'RECEPTION')
                                        📋 Recepcionista
                                    @elseif($primaryRole === 'DOCTOR')
                                        🩺 Médico
                                    @elseif($primaryRole === 'SPECIALIST')
                                        🔬 Especialista
                                    @elseif($primaryRole === 'PATIENT')
                                        👤 Paciente
                                    @else
                                        {{ $roleLabel }}
                                    @endif
                                </span>
                            </td>

                            <!-- Linked Profile -->
                            <td class="py-4 px-6">
                                @if($u->patient)
                                    <div class="space-y-0.5">
                                        <div class="flex items-center space-x-1">
                                            <span class="font-bold text-slate-800 text-xs">Paciente</span>
                                            <span class="text-[11px] text-slate-500 font-mono">({{ $u->patient->identification_number }})</span>
                                        </div>
                                        <a href="{{ route('admin.patients.clinical_record', $u->patient) }}" class="text-[11px] text-brand-primary hover:underline font-semibold block">
                                            Ver Historia Clínica →
                                        </a>
                                    </div>
                                @elseif($u->doctor)
                                    <div class="space-y-0.5">
                                        <div class="flex items-center space-x-1">
                                            <span class="font-bold text-slate-800 text-xs">{{ $u->doctor->primary_specialty_name ?: 'Especialista' }}</span>
                                        </div>
                                        <span class="text-[11px] text-slate-400 font-mono block">MSP: {{ $u->doctor->license_number }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs italic">Personal Administrativo</span>
                                @endif
                            </td>

                            <!-- Contact -->
                            <td class="py-4 px-6">
                                <span class="text-slate-700 font-medium block">{{ $u->phone ?: 'Sin teléfono' }}</span>
                                <span class="text-slate-400 text-[11px]">
                                    {{ $u->email_verified_at ? 'Email verificado' : 'Pendiente verificación' }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-6">
                                <form method="POST" action="{{ route('admin.users.toggle', $u) }}" class="inline">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold border transition-colors cursor-pointer {{ $u->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200' }}"
                                        title="Clic para cambiar estado"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full {{ $u->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span>{{ $u->is_active ? 'Activo' : 'Inactivo' }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Created At -->
                            <td class="py-4 px-6 text-slate-500">
                                <div>{{ $u->created_at->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-slate-400">{{ $u->created_at->format('H:i') }}</div>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end space-x-1">
                                    <!-- View Details -->
                                    <button
                                        type="button"
                                        @click="openViewModal({{ json_encode($u) }})"
                                        class="p-2 rounded-xl text-slate-500 hover:text-brand-primary hover:bg-brand-soft/60 transition-colors cursor-pointer"
                                        title="Ver Ficha y Permisos"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>

                                    <!-- Send Reset Password Link -->
                                    <form method="POST" action="{{ route('admin.users.send_reset', $u) }}" class="inline" onsubmit="return confirm('¿Deseas enviar un enlace de restablecimiento de contraseña a {{ $u->email }}?')">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="p-2 rounded-xl text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition-colors cursor-pointer"
                                            title="Enviar Correo de Restablecer Contraseña"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </button>
                                    </form>

                                    <!-- Edit User -->
                                    <button
                                        type="button"
                                        @click="openEditModal({{ json_encode($u) }})"
                                        class="p-2 rounded-xl text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors cursor-pointer"
                                        title="Editar Usuario y Rol"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>

                                    <!-- Delete User (if not self) -->
                                    @if($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar al usuario {{ $u->name }}? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                                title="Eliminar Usuario"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <p class="font-bold text-slate-600 text-sm">No se encontraron usuarios</p>
                                    <p class="text-xs text-slate-400">Intenta con otros criterios de búsqueda o registra un nuevo usuario en la plataforma.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: REGISTRAR NUEVO USUARIO -->
    <!-- ========================================================================= -->
    <div x-show="newUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
        <div @click.away="newUserModal = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-2xl w-full max-h-[92vh] overflow-y-auto shadow-2xl border border-slate-100 space-y-6 animate-scaleUp custom-scrollbar">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-lg">Registrar Nuevo Usuario</h3>
                        <p class="text-xs text-slate-500">Crea el usuario en el sistema con su rol correspondiente y credenciales.</p>
                    </div>
                </div>
                <button type="button" @click="newUserModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
                @csrf

                <!-- Role Selection Selector -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                        Selecciona el Rol / Perfil del Usuario <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        <label class="flex items-center p-3 rounded-2xl border cursor-pointer transition-all" :class="selectedRole === 'PATIENT' ? 'border-brand-primary bg-brand-soft/20 text-brand-primary font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                            <input type="radio" name="role" value="PATIENT" x-model="selectedRole" class="sr-only" />
                            <span>👤 Paciente</span>
                        </label>
                        <label class="flex items-center p-3 rounded-2xl border cursor-pointer transition-all" :class="selectedRole === 'DOCTOR' ? 'border-brand-primary bg-brand-soft/20 text-brand-primary font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                            <input type="radio" name="role" value="DOCTOR" x-model="selectedRole" class="sr-only" />
                            <span>🩺 Médico</span>
                        </label>
                        <label class="flex items-center p-3 rounded-2xl border cursor-pointer transition-all" :class="selectedRole === 'SPECIALIST' ? 'border-brand-primary bg-brand-soft/20 text-brand-primary font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                            <input type="radio" name="role" value="SPECIALIST" x-model="selectedRole" class="sr-only" />
                            <span>🔬 Especialista</span>
                        </label>
                        <label class="flex items-center p-3 rounded-2xl border cursor-pointer transition-all" :class="selectedRole === 'RECEPTION' ? 'border-brand-primary bg-brand-soft/20 text-brand-primary font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                            <input type="radio" name="role" value="RECEPTION" x-model="selectedRole" class="sr-only" />
                            <span>📋 Recepcionista</span>
                        </label>
                        <label class="flex items-center p-3 rounded-2xl border cursor-pointer transition-all" :class="selectedRole === 'ADMIN' ? 'border-brand-primary bg-brand-soft/20 text-brand-primary font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                            <input type="radio" name="role" value="ADMIN" x-model="selectedRole" class="sr-only" />
                            <span>🛡️ Administrador</span>
                        </label>
                        <label class="flex items-center p-3 rounded-2xl border cursor-pointer transition-all" :class="selectedRole === 'SUPER_ADMIN' ? 'border-brand-primary bg-brand-soft/20 text-brand-primary font-bold' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                            <input type="radio" name="role" value="SUPER_ADMIN" x-model="selectedRole" class="sr-only" />
                            <span>👑 Super Administrador</span>
                        </label>
                    </div>
                </div>

                <!-- Basic User Information -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nombre Completo <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="Ej: Dr. Roberto Gómez o Carlos Mendoza" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Correo Electrónico <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="usuario@surhospital.com" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Teléfono Móvil</label>
                        <input type="text" name="phone" placeholder="0991234567" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Clave de Acceso Inicial (Genérica)
                        </label>
                        <div class="relative">
                            <input type="text" name="password" value="SurHospital2026!" placeholder="SurHospital2026!" class="w-full rounded-xl border-slate-200 text-xs p-3 font-mono font-bold text-brand-primary bg-slate-50 focus:bg-white focus:border-brand-primary" />
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Por defecto se asigna 'SurHospital2026!'. Puedes modificarla si deseas.</p>
                    </div>
                </div>

                <!-- CONDITIONAL FIELDS: PACIENTE -->
                <div x-show="selectedRole === 'PATIENT'" class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80 space-y-3.5 transition-all">
                    <div class="flex items-center space-x-2 text-amber-800 font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Datos del Paciente & Envío de Clave por Correo</span>
                    </div>

                    <p class="text-[11px] text-amber-700 leading-relaxed">
                        Al registrar al paciente, el sistema generará automáticamente la clave genérica y le enviará un correo electrónico con el enlace seguro directo para que establezca su nueva contraseña personal.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Tipo de Identificación <span class="text-rose-500">*</span></label>
                            <select name="identification_type" class="w-full rounded-xl border-slate-200 text-xs p-2.5 bg-white">
                                <option value="CEDULA">Cédula de Identidad</option>
                                <option value="PASAPORTE">Pasaporte</option>
                                <option value="RUC">RUC</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Número de Identificación <span class="text-rose-500">*</span></label>
                            <input type="text" name="identification_number" placeholder="Ej: 0923456789" class="w-full rounded-xl border-slate-200 text-xs p-2.5 bg-white" />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Fecha de Nacimiento</label>
                            <input type="date" name="birth_date" class="w-full rounded-xl border-slate-200 text-xs p-2.5 bg-white" />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Género</label>
                            <select name="gender" class="w-full rounded-xl border-slate-200 text-xs p-2.5 bg-white">
                                <option value="OTHER">Otro / No especifica</option>
                                <option value="MALE">Masculino</option>
                                <option value="FEMALE">Femenino</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Dirección Domiciliaria</label>
                            <input type="text" name="address" placeholder="Guayaquil, Ecuador" class="w-full rounded-xl border-slate-200 text-xs p-2.5 bg-white" />
                        </div>
                    </div>
                </div>

                <!-- CONDITIONAL FIELDS: MÉDICO O ESPECIALISTA -->
                <div x-show="selectedRole === 'DOCTOR' || selectedRole === 'SPECIALIST'" class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200/80 space-y-3.5 transition-all">
                    <div class="flex items-center space-x-2 text-blue-800 font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        <span>Ficha Profesional del Médico / Especialista</span>
                    </div>

                    <p class="text-[11px] text-blue-700 leading-relaxed">
                        Se registrará el profesional directamente en usuarios con clave genérica y se habilitará automáticamente su agenda institucional con turnos de lunes a viernes.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Registro MSP / Licencia <span class="text-rose-500">*</span></label>
                            <input type="text" name="license_number" placeholder="Ej: MSP-MED-99482" class="w-full rounded-xl border-slate-200 text-xs p-2.5 bg-white" />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Consultorio / Consultorio N°</label>
                            <input type="text" name="room_office" placeholder="Ej: Consultorio 204" class="w-full rounded-xl border-slate-200 text-xs p-2.5 bg-white" />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Precio Consulta ($)</label>
                            <input type="number" step="0.5" name="default_consultation_price" value="35.00" class="w-full rounded-xl border-slate-200 text-xs p-2.5 bg-white font-bold" />
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Duración del Turno (minutos) <span class="text-rose-500">*</span></label>
                            <select name="default_slot_duration_minutes" class="w-full rounded-xl border-slate-200 text-xs p-2.5 bg-white font-bold">
                                <option value="20">20 Minutos</option>
                                <option value="30" selected>30 Minutos (Estándar)</option>
                                <option value="45">45 Minutos</option>
                                <option value="60">60 Minutos</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Especialidad(es) Asignada(s) <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 p-2.5 rounded-xl border border-slate-200 bg-white max-h-36 overflow-y-auto custom-scrollbar">
                                @foreach($specialties as $sp)
                                    <label class="flex items-center space-x-2 text-xs text-slate-700 cursor-pointer">
                                        <input type="checkbox" name="specialty_ids[]" value="{{ $sp->id }}" class="rounded text-brand-primary focus:ring-brand-primary h-4 w-4" />
                                        <span class="truncate">{{ $sp->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="newUserModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 transition-all cursor-pointer">
                        Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: EDITAR USUARIO -->
    <!-- ========================================================================= -->
    <div x-show="editUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
        <div @click.away="editUserModal = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full max-h-[92vh] overflow-y-auto shadow-2xl border border-slate-100 space-y-6 animate-scaleUp custom-scrollbar">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-lg">Modificar Usuario & Rol</h3>
                        <p class="text-xs text-slate-500">Actualiza los datos de la cuenta y su asignación de perfil.</p>
                    </div>
                </div>
                <button type="button" @click="editUserModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" :action="'/admin/usuarios/' + editUserData.id" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nombre Completo <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="editUserData.name" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Correo Electrónico <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" x-model="editUserData.email" required class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Teléfono</label>
                        <input type="text" name="phone" x-model="editUserData.phone" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    </div>
                </div>

                <!-- Role Selector -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Rol / Perfil Asignado <span class="text-rose-500">*</span></label>
                    <select name="role" x-model="editUserData.role" class="w-full rounded-xl border-slate-200 text-xs p-3 font-bold bg-white focus:border-brand-primary">
                        <option value="SUPER_ADMIN">👑 Super Administrador</option>
                        <option value="ADMIN">🛡️ Administrador</option>
                        <option value="RECEPTION">📋 Recepcionista</option>
                        <option value="DOCTOR">🩺 Médico</option>
                        <option value="SPECIALIST">🔬 Especialista</option>
                        <option value="PATIENT">👤 Paciente</option>
                    </select>
                </div>

                <!-- Change Password -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nueva Contraseña (Opcional)</label>
                    <input type="password" name="password" placeholder="Dejar en blanco para conservar la actual" class="w-full rounded-xl border-slate-200 text-xs p-3 focus:border-brand-primary" />
                    <p class="text-[11px] text-slate-400 mt-1">Mínimo 8 caracteres si decides cambiarla.</p>
                </div>

                <!-- Active switch -->
                <div class="pt-2">
                    <label class="flex items-center space-x-2.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" :checked="editUserData.is_active == 1" class="rounded text-brand-primary focus:ring-brand-primary h-4 w-4" />
                        <span class="text-xs font-bold text-slate-800">Usuario Activo en el Sistema</span>
                    </label>
                </div>

                <div class="pt-4 flex items-center justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="editUserModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 transition-all cursor-pointer">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL: VER DETALLE DE USUARIO Y PERFIL -->
    <!-- ========================================================================= -->
    <div x-show="viewUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
        <div @click.away="viewUserModal = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-xl w-full max-h-[92vh] overflow-y-auto shadow-2xl border border-slate-100 space-y-6 animate-scaleUp custom-scrollbar">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-black text-lg">
                        <span x-text="selectedUser ? selectedUser.name.charAt(0).toUpperCase() : 'U'"></span>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-lg" x-text="selectedUser?.name"></h3>
                        <p class="text-xs text-slate-500 font-mono" x-text="selectedUser?.email"></p>
                    </div>
                </div>
                <button type="button" @click="viewUserModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 text-[10px] font-bold uppercase block mb-1">Rol en Sistema</span>
                        <span class="font-bold text-slate-800 text-sm" x-text="selectedUser?.roles && selectedUser.roles.length > 0 ? selectedUser.roles[0].name : 'Sin rol'"></span>
                    </div>

                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 text-[10px] font-bold uppercase block mb-1">Estado de Cuenta</span>
                        <span class="font-bold inline-flex items-center gap-1.5" :class="selectedUser?.is_active ? 'text-emerald-700' : 'text-slate-500'">
                            <span class="w-2 h-2 rounded-full" :class="selectedUser?.is_active ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                            <span x-text="selectedUser?.is_active ? 'Activo' : 'Inactivo'"></span>
                        </span>
                    </div>

                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 text-[10px] font-bold uppercase block mb-1">Teléfono</span>
                        <span class="font-medium text-slate-800" x-text="selectedUser?.phone || 'No registrado'"></span>
                    </div>

                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 text-[10px] font-bold uppercase block mb-1">Fecha Registro</span>
                        <span class="font-medium text-slate-800" x-text="selectedUser?.created_at ? new Date(selectedUser.created_at).toLocaleDateString('es-EC') : 'N/D'"></span>
                    </div>
                </div>

                <!-- Patient Specific Info -->
                <template x-if="selectedUser?.patient">
                    <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80 space-y-2">
                        <h4 class="font-bold text-amber-900 text-xs uppercase tracking-wider">Perfil Clínico del Paciente</h4>
                        <div class="grid grid-cols-2 gap-2 text-xs text-amber-800">
                            <div>Identificación: <strong x-text="selectedUser.patient.identification_type + ': ' + selectedUser.patient.identification_number"></strong></div>
                            <div>Género: <strong x-text="selectedUser.patient.gender || 'No registrado'"></strong></div>
                            <div>Nacimiento: <strong x-text="selectedUser.patient.birth_date ? selectedUser.patient.birth_date.substring(0, 10) : 'N/D'"></strong></div>
                            <div>Dirección: <strong x-text="selectedUser.patient.address || 'No registrada'"></strong></div>
                        </div>
                    </div>
                </template>

                <!-- Doctor Specific Info -->
                <template x-if="selectedUser?.doctor">
                    <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200/80 space-y-2">
                        <h4 class="font-bold text-blue-900 text-xs uppercase tracking-wider">Perfil Médico / Especialista</h4>
                        <div class="grid grid-cols-2 gap-2 text-xs text-blue-800">
                            <div>Registro MSP: <strong x-text="selectedUser.doctor.license_number"></strong></div>
                            <div>Especialidad: <strong x-text="selectedUser.doctor.primary_specialty_name || 'Especialista'"></strong></div>
                            <div>Consultorio: <strong x-text="selectedUser.doctor.room_office || 'N/D'"></strong></div>
                            <div>Duración Turno: <strong x-text="selectedUser.doctor.default_slot_duration_minutes + ' min'"></strong></div>
                            <div>Precio Consulta: <strong x-text="'$' + selectedUser.doctor.default_consultation_price"></strong></div>
                        </div>
                    </div>
                </template>

                <!-- Quick actions -->
                <div class="pt-3 flex items-center justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="viewUserModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                        Cerrar
                    </button>
                    <button type="button" @click="viewUserModal = false; openEditModal(selectedUser)" class="px-4 py-2.5 rounded-xl bg-brand-primary text-white font-bold text-xs shadow-md shadow-brand-primary/20 hover:bg-brand-primary-dark">
                        Editar Datos
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
