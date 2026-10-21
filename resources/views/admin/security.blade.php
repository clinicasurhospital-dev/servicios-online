@extends('layouts.admin')

@section('title', 'Módulo de Seguridad & Roles — ' . config('branding.app_name'))

@section('content')
<script>
    window.securityRoles = @json($roles);
</script>

<div class="space-y-6 animate-fadeIn" x-data="{
    activeTab: 'roles_matrix', // 'roles_matrix' or 'user_assignment'
    selectedRoleId: {{ $roles->firstWhere('name', 'ADMIN')?->id ?? ($roles->first()?->id ?? 1) }},
    rolesList: @json($roles),
    currentRolePermissions: [],
    init() {
        this.loadRolePermissions(this.selectedRoleId);
    },
    loadRolePermissions(roleId) {
        this.selectedRoleId = roleId;
        const found = this.rolesList.find(r => r.id === roleId);
        if (found && found.permissions) {
            this.currentRolePermissions = found.permissions.map(p => p.name);
        } else {
            this.currentRolePermissions = [];
        }
    },
    isPermissionChecked(permName) {
        return this.currentRolePermissions.includes(permName);
    },
    togglePermission(permName) {
        const idx = this.currentRolePermissions.indexOf(permName);
        if (idx > -1) {
            this.currentRolePermissions.splice(idx, 1);
        } else {
            this.currentRolePermissions.push(permName);
        }
    },
    toggleModuleAll(permNames, selectAll) {
        permNames.forEach(p => {
            const idx = this.currentRolePermissions.indexOf(p);
            if (selectAll && idx === -1) {
                this.currentRolePermissions.push(p);
            } else if (!selectAll && idx > -1) {
                this.currentRolePermissions.splice(idx, 1);
            }
        });
    }
}">
    <!-- Header banner -->
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Módulo de Seguridad & Perfiles</h1>
                    <p class="text-xs text-slate-500">Configuración de roles y asignación de permisos de acceso a los módulos institucionales.</p>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex items-center p-1.5 bg-slate-100 rounded-2xl">
            <button
                type="button"
                @click="activeTab = 'roles_matrix'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer"
                :class="activeTab === 'roles_matrix' ? 'bg-white text-brand-primary shadow-xs' : 'text-slate-600 hover:text-slate-900'"
            >
                🛡️ Permisos por Rol / Módulo
            </button>
            <button
                type="button"
                @click="activeTab = 'user_assignment'"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all cursor-pointer"
                :class="activeTab === 'user_assignment' ? 'bg-white text-brand-primary shadow-xs' : 'text-slate-600 hover:text-slate-900'"
            >
                👥 Asignación Rápida a Usuarios
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: MATRIZ DE ROLES Y PERMISOS DE MÓDULOS -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'roles_matrix'" class="space-y-6">
        <!-- Role Selection Tabs -->
        <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm overflow-x-auto custom-scrollbar">
            <div class="flex items-center space-x-2 min-w-max">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider px-2">Seleccionar Rol:</span>
                @foreach($roles as $r)
                    @php
                        $roleEmoji = match($r->name) {
                            'SUPER_ADMIN' => '👑',
                            'ADMIN' => '🛡️',
                            'RECEPTION' => '📋',
                            'DOCTOR' => '🩺',
                            'SPECIALIST' => '🔬',
                            'PATIENT' => '👤',
                            default => '🔑'
                        };
                        $roleTitle = match($r->name) {
                            'SUPER_ADMIN' => 'Super Administrador',
                            'ADMIN' => 'Administrador',
                            'RECEPTION' => 'Recepcionista',
                            'DOCTOR' => 'Médico',
                            'SPECIALIST' => 'Especialista',
                            'PATIENT' => 'Paciente',
                            default => $r->name
                        };
                    @endphp
                    <button
                        type="button"
                        @click="loadRolePermissions({{ $r->id }})"
                        class="px-4 py-2.5 rounded-2xl text-xs font-bold border transition-all cursor-pointer flex items-center space-x-2"
                        :class="selectedRoleId === {{ $r->id }} ? 'bg-brand-primary text-white border-brand-primary shadow-md shadow-brand-primary/20 scale-102' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200'"
                    >
                        <span>{{ $roleEmoji }}</span>
                        <span>{{ $roleTitle }}</span>
                        <span class="ml-1 text-[10px] px-1.5 py-0.2 rounded-full" :class="selectedRoleId === {{ $r->id }} ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-600'">
                            {{ $r->permissions->count() }}
                        </span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Role Permissions Edit Form -->
        <template x-for="role in rolesList" :key="role.id">
            <div x-show="selectedRoleId === role.id" class="space-y-6">
                <!-- Role Info Alert -->
                <div class="p-4 rounded-2xl bg-brand-soft/30 border border-brand-primary/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-brand-primary text-white flex items-center justify-center font-bold text-sm">
                            <span x-text="role.name === 'SUPER_ADMIN' ? '👑' : (role.name === 'ADMIN' ? '🛡️' : (role.name === 'RECEPTION' ? '📋' : (role.name === 'DOCTOR' ? '🩺' : (role.name === 'SPECIALIST' ? '🔬' : '👤'))))"></span>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">
                                Configurando Permisos para: <span class="text-brand-primary" x-text="role.name"></span>
                            </h3>
                            <p class="text-xs text-slate-500">
                                Activa o desactiva el acceso a los módulos institucionales para este perfil.
                            </p>
                        </div>
                    </div>

                    <template x-if="role.name === 'SUPER_ADMIN'">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                            Super Administrador: Acceso Irrestricto Completo
                        </span>
                    </template>
                </div>

                <!-- Form to Submit Permissions -->
                <form method="POST" :action="'/admin/seguridad/roles/' + role.id + '/permisos'" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Modules Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($modules as $mKey => $mod)
                            @php
                                $permKeys = array_keys($mod['permissions']);
                            @endphp
                            <div class="bg-white rounded-3xl border border-slate-100 shadow-xs p-5 flex flex-col justify-between hover:shadow-sm transition-shadow">
                                <div class="space-y-3">
                                    <!-- Module Header -->
                                    <div class="flex items-start justify-between border-b border-slate-100 pb-3">
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                                <span>{{ $mod['name'] }}</span>
                                            </h4>
                                            <p class="text-[11px] text-slate-400 mt-0.5 leading-snug">{{ $mod['description'] }}</p>
                                        </div>
                                    </div>

                                    <!-- Quick actions for module -->
                                    <div class="flex items-center justify-end space-x-2 text-[10px] text-brand-primary font-bold">
                                        <button
                                            type="button"
                                            @click="toggleModuleAll(@json($permKeys), true)"
                                            class="hover:underline cursor-pointer"
                                        >
                                            + Todos
                                        </button>
                                        <span class="text-slate-300">|</span>
                                        <button
                                            type="button"
                                            @click="toggleModuleAll(@json($permKeys), false)"
                                            class="hover:underline text-slate-400 hover:text-rose-600 cursor-pointer"
                                        >
                                            Ninguno
                                        </button>
                                    </div>

                                    <!-- Permissions Checkboxes -->
                                    <div class="space-y-2 pt-1">
                                        @foreach($mod['permissions'] as $pKey => $pLabel)
                                            <label class="flex items-start space-x-2.5 p-2 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer group">
                                                <input
                                                    type="checkbox"
                                                    name="permissions[]"
                                                    value="{{ $pKey }}"
                                                    :checked="currentRolePermissions.includes('{{ $pKey }}')"
                                                    @change="togglePermission('{{ $pKey }}')"
                                                    class="mt-0.5 rounded text-brand-primary focus:ring-brand-primary h-4 w-4 border-slate-300"
                                                />
                                                <div class="text-xs">
                                                    <span class="font-bold text-slate-700 group-hover:text-slate-900 block">{{ $pLabel }}</span>
                                                    <span class="text-[10px] text-slate-400 font-mono">{{ $pKey }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Floating Submit Bar -->
                    <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-sm flex items-center justify-between sticky bottom-4 z-20">
                        <div class="text-xs text-slate-500">
                            Total permisos seleccionados para <strong class="text-slate-900" x-text="role.name"></strong>: 
                            <span class="font-bold text-brand-primary" x-text="currentRolePermissions.length"></span>
                        </div>

                        <button
                            type="submit"
                            :disabled="role.name === 'SUPER_ADMIN'"
                            class="px-6 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Guardar Permisos de este Rol
                        </button>
                    </div>
                </form>
            </div>
        </template>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: ASIGNACIÓN RÁPIDA DE ROL A USUARIOS -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'user_assignment'" class="space-y-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Asignar o Cambiar Rol a Usuarios</h3>
                <p class="text-xs text-slate-500">Busca cualquier usuario del sistema y asígnale su nuevo rol directamente.</p>
            </div>

            <form method="GET" action="{{ route('admin.security') }}" class="flex items-center space-x-2">
                <input
                    type="text"
                    name="user_search"
                    value="{{ $userSearch }}"
                    placeholder="Buscar por nombre, correo, teléfono..."
                    class="rounded-xl border-slate-200 text-xs p-2.5 w-60 sm:w-72 focus:border-brand-primary"
                />
                <button type="submit" class="px-3.5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                    Buscar
                </button>
                @if($userSearch)
                    <a href="{{ route('admin.security') }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-500 text-xs font-bold hover:bg-slate-50">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>

        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 font-bold uppercase tracking-wider">
                            <th class="py-4 px-6">Usuario</th>
                            <th class="py-4 px-6">Contacto</th>
                            <th class="py-4 px-6">Rol Actual</th>
                            <th class="py-4 px-6">Reasignar Rol</th>
                            <th class="py-4 px-6 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $usr)
                            @php
                                $uRole = $usr->roles->first()?->name ?? 'Sin Rol';
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-9 h-9 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($usr->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 block text-sm">{{ $usr->name }}</span>
                                            <span class="text-slate-400 text-[11px] font-mono">{{ $usr->email }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-4 px-6 text-slate-600">
                                    {{ $usr->phone ?: 'Sin teléfono' }}
                                </td>

                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold border {{ $usr->role_badge_classes }}">
                                        {{ $usr->role_name }}
                                    </span>
                                </td>

                                <td class="py-4 px-6" colspan="2">
                                    <form method="POST" action="{{ route('admin.security.assign_role', $usr) }}" class="flex items-center justify-end space-x-2">
                                        @csrf
                                        <select name="role" class="rounded-xl border-slate-200 text-xs p-2 focus:border-brand-primary font-bold text-slate-800 bg-slate-50">
                                            @foreach($roles as $rl)
                                                <option value="{{ $rl->name }}" {{ $uRole === $rl->name ? 'selected' : '' }}>
                                                    {{ match($rl->name) {
                                                        'SUPER_ADMIN' => '👑 Super Administrador',
                                                        'ADMIN' => '🛡️ Administrador',
                                                        'RECEPTION' => '📋 Recepcionista',
                                                        'DOCTOR' => '🩺 Médico',
                                                        'SPECIALIST' => '🔬 Especialista',
                                                        'PATIENT' => '👤 Paciente',
                                                        default => $rl->name
                                                    } }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <button
                                            type="submit"
                                            class="px-3.5 py-2 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-xs transition-all cursor-pointer"
                                        >
                                            Guardar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $users->appends(['user_search' => $userSearch])->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
