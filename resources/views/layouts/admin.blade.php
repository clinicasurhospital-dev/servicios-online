<!DOCTYPE html>
<html lang="es" class="h-full bg-[#F7F8FC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? $branding->title('admin') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased text-slate-800 bg-[#F7F8FC] h-full flex overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div
        x-show="sidebarOpen"
        x-on:click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 lg:hidden"
        style="display: none;"
    ></div>

    <!-- Sidebar Component -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-100 flex flex-col justify-between transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shadow-sm"
    >
        <div class="flex flex-col flex-1 overflow-y-auto custom-scrollbar">
            <!-- Sidebar Header Brand -->
            <div class="h-20 px-6 border-b border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.dashboard') }}" class="group">
                    <x-brand.nexaris-logo variant="header" />
                </a>
                <button
                    type="button"
                    x-on:click="sidebarOpen = false"
                    class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="px-4 py-6 space-y-1.5 flex-1">
                @php
                    $navItems = [
                        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'home', 'can' => 'view_dashboard'],
                        ['label' => 'Agenda Médica', 'route' => 'admin.agenda', 'icon' => 'calendar', 'can' => 'view_all_appointments'],
                        ['label' => 'Gestión de Citas', 'route' => 'admin.appointments', 'icon' => 'clock', 'can' => 'view_all_appointments'],
                        ['label' => 'Pacientes', 'route' => 'admin.patients', 'icon' => 'users', 'can' => 'manage_patients'],
                        ['label' => 'Médicos & Turnos', 'route' => 'admin.doctors', 'icon' => 'user-md', 'can' => 'manage_doctors'],
                        ['label' => 'Especialidades', 'route' => 'admin.specialties', 'icon' => 'sparkles', 'can' => 'manage_specialties'],
                        ['label' => 'Validar Pagos', 'route' => 'admin.transfers', 'icon' => 'credit-card', 'can' => 'validate_bank_transfers'],
                        ['label' => 'Auditoría & Logs', 'route' => 'admin.audit', 'icon' => 'shield-check', 'can' => 'view_audit_logs'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermissionTo($item['can']) || auth()->user()->isAdmin() || auth()->user()->isReception())
                        @php
                            $isActive = request()->routeIs($item['route']);
                        @endphp
                        <a
                            href="{{ route($item['route']) }}"
                            class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 {{ $isActive ? 'bg-brand-50 text-brand-600 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                        >
                            @if($item['icon'] === 'home')
                                <svg class="h-5 w-5 {{ $isActive ? 'text-brand-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            @elseif($item['icon'] === 'calendar')
                                <svg class="h-5 w-5 {{ $isActive ? 'text-brand-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @elseif($item['icon'] === 'clock')
                                <svg class="h-5 w-5 {{ $isActive ? 'text-brand-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @elseif($item['icon'] === 'users')
                                <svg class="h-5 w-5 {{ $isActive ? 'text-brand-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            @elseif($item['icon'] === 'user-md')
                                <svg class="h-5 w-5 {{ $isActive ? 'text-brand-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            @elseif($item['icon'] === 'credit-card')
                                <svg class="h-5 w-5 {{ $isActive ? 'text-brand-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                            @elseif($item['icon'] === 'shield-check')
                                <svg class="h-5 w-5 {{ $isActive ? 'text-brand-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            @else
                                <svg class="h-5 w-5 {{ $isActive ? 'text-brand-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                </svg>
                            @endif
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>

            <!-- User Profile Card in Sidebar Bottom -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl bg-brand-600 text-white font-bold flex items-center justify-center shadow-xs">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] font-medium text-slate-500 truncate">
                            {{ auth()->user()->roles->first()?->name ?? 'Usuario' }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-xl transition-colors" title="Cerrar Sesión">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top App Bar -->
        <header class="h-20 bg-white border-b border-slate-100 flex items-center justify-between px-6 z-10">
            <div class="flex items-center gap-4">
                <button
                    type="button"
                    x-on:click="sidebarOpen = true"
                    class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition-colors"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-xl font-bold text-slate-900 tracking-tight">{{ $header ?? 'Panel Administrativo' }}</h1>
            </div>

            <!-- Header Quick Actions -->
            <div class="flex items-center gap-3">
                <x-button href="{{ route('home') }}" variant="outline" size="sm">
                    <svg class="h-4 w-4 mr-1.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Ver Sitio Público
                </x-button>

                <x-button x-on:click="$dispatch('open-modal', 'quick-appointment-modal')" variant="primary" size="sm">
                    <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Cita
                </x-button>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8 custom-scrollbar">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    <!-- Quick Manual Appointment Creation Modal for Reception/Admin -->
    <x-modal name="quick-appointment-modal" maxWidth="2xl">
        <div class="p-6">
            <h3 class="text-lg font-bold text-slate-900 mb-2">Creación Manual de Cita</h3>
            <p class="text-xs text-slate-500 mb-6">Asigna una consulta o procedimiento clínico directamente desde recepción.</p>
            <livewire:booking-wizard :is-manual="true" />
        </div>
    </x-modal>

    <!-- Global Toast Container -->
    <x-toast-container />

    @livewireScripts
</body>
</html>
