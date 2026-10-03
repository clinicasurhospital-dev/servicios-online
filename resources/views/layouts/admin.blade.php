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
                    $isDoctor = auth()->user()->isDoctor();
                    $isAdminOrSuper = auth()->user()->isSuperAdmin() || auth()->user()->isAdmin();
                    $isReception = auth()->user()->isReception();

                    if ($isDoctor) {
                        $navItems = [
                            ['label' => 'Mi Panel', 'route' => 'admin.dashboard', 'icon' => 'home'],
                            ['label' => 'Mi Agenda', 'route' => 'admin.agenda', 'icon' => 'calendar'],
                            ['label' => 'Mis Citas', 'route' => 'admin.appointments', 'icon' => 'clock'],
                            ['label' => 'Mis Valoraciones', 'route' => 'admin.reviews', 'icon' => 'star'],
                        ];
                    } else {
                        $navItems = [
                            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'home', 'can' => 'view_dashboard'],
                            ['label' => 'Agenda Médica', 'route' => 'admin.agenda', 'icon' => 'calendar', 'can' => 'view_all_appointments'],
                            ['label' => 'Gestión de Citas', 'route' => 'admin.appointments', 'icon' => 'clock', 'can' => 'view_all_appointments'],
                            ['label' => 'Pacientes', 'route' => 'admin.patients', 'icon' => 'users', 'can' => 'manage_patients'],
                            ['label' => 'Médicos & Turnos', 'route' => 'admin.doctors', 'icon' => 'user-md', 'can' => 'manage_doctors'],
                            ['label' => 'Especialidades', 'route' => 'admin.specialties', 'icon' => 'sparkles', 'can' => 'manage_specialties'],
                            ['label' => 'Validar Pagos', 'route' => 'admin.transfers', 'icon' => 'credit-card', 'can' => 'validate_bank_transfers'],
                            ['label' => 'Plantillas Clínicas', 'route' => 'admin.clinical.templates.index', 'icon' => 'clipboard-document-list', 'can' => 'clinical_template_view'],
                            ['label' => 'Valoraciones & Sugerencias', 'route' => 'admin.reviews', 'icon' => 'star'],
                            ['label' => 'Auditoría & Logs', 'route' => 'admin.audit', 'icon' => 'shield-check', 'can' => 'view_audit_logs'],
                        ];
                    }
                @endphp

                @foreach($navItems as $item)
                    @if($isDoctor || $isAdminOrSuper || $isReception || (isset($item['can']) && auth()->user()->hasPermissionTo($item['can'])))
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
                            @elseif($item['icon'] === 'star')
                                <svg class="h-5 w-5 {{ $isActive ? 'text-brand-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
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
        <header class="h-16 sm:h-20 bg-white border-b border-slate-100 flex items-center justify-between px-3 sm:px-6 z-10 gap-2">
            <div class="flex items-center gap-2 sm:gap-4 min-w-0">
                <button
                    type="button"
                    x-on:click="sidebarOpen = true"
                    class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition-colors shrink-0"
                    aria-label="Abrir menú"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 class="text-base sm:text-xl font-bold text-slate-900 tracking-tight truncate max-w-[150px] xs:max-w-[220px] sm:max-w-none">
                    {{ $header ?? (auth()->check() && auth()->user()->isDoctor() ? 'Panel Médico' : 'Panel Administrativo') }}
                </h1>
            </div>

            <!-- Header Quick Actions -->
            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <a href="{{ route('home') }}" class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:text-brand-600 text-xs font-semibold transition-colors">
                    <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span>Ver Sitio Público</span>
                </a>

                @if(!auth()->user()->isDoctor())
                    <button
                        type="button"
                        x-on:click="$dispatch('open-modal', 'quick-appointment-modal')"
                        class="inline-flex items-center justify-center gap-1.5 px-3 sm:px-4 py-2 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs sm:text-sm shadow-md shadow-brand-primary/20 transition-all transform active:scale-95 shrink-0 cursor-pointer"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Nueva Cita</span>
                    </button>
                @endif
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8 custom-scrollbar space-y-4">
            {{-- Global Feedback Alerts --}}
            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs animate-fadeIn" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-xs animate-fadeIn" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="show = false" class="text-rose-500 hover:text-rose-700 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            @if(session('warning'))
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold flex items-center justify-between shadow-xs animate-fadeIn" x-data="{ show: true }" x-show="show">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-7 h-7 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <span>{{ session('warning') }}</span>
                    </div>
                    <button type="button" @click="show = false" class="text-amber-500 hover:text-amber-700 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs animate-fadeIn" x-data="{ show: true }" x-show="show">
                    <div class="flex items-start justify-between">
                        <div class="space-y-1">
                            <p class="font-bold flex items-center space-x-1.5 text-rose-900">
                                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Por favor corrige los siguientes errores:</span>
                            </p>
                            <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" @click="show = false" class="text-rose-400 hover:text-rose-600 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>
            @endif

            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>

    <!-- Quick Manual Appointment Creation Modal for Reception/Admin -->
    @if(!auth()->user()->isDoctor())
        <x-modal name="quick-appointment-modal" maxWidth="5xl">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/70 shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Creación Manual de Cita</h3>
                        <p class="text-xs text-slate-500">Asignar consulta médica o procedimiento desde recepción.</p>
                    </div>
                </div>
                <button x-on:click="$dispatch('close-modal', 'quick-appointment-modal')" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 rounded-xl transition-colors" title="Cerrar ventana">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-4 sm:p-6 md:p-8 overflow-y-auto custom-scrollbar flex-1 bg-slate-50/30">
                <livewire:booking-wizard :is-manual="true" wire:key="admin-manual-wizard" />
            </div>
        </x-modal>
    @endif

    <!-- Global Toast Container -->
    <x-toast-container />

    @livewireScripts
</body>
</html>
