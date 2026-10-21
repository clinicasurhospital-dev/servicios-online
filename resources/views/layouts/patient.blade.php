<!DOCTYPE html>
<html lang="es" class="h-full bg-[#F7F8FC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? $branding->title('patient') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased text-slate-800 bg-[#F7F8FC] min-h-full flex flex-col selection:bg-brand-500 selection:text-white">

    <!-- Patient Header -->
    <header class="sticky top-0 z-40 bg-white border-b border-slate-100 shadow-xs">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-18">
                <!-- Patient Brand Logo -->
                <a href="{{ route('patient.dashboard') }}" class="flex items-center gap-2">
                    <x-brand.logo />
                </a>

                <!-- Patient Quick Nav -->
                <nav class="hidden sm:flex items-center gap-6">
                    <a href="{{ route('patient.dashboard') }}" class="text-sm font-semibold {{ request()->routeIs('patient.dashboard') ? 'text-brand-600' : 'text-slate-600 hover:text-slate-900' }} transition-colors">
                        Inicio
                    </a>
                    <a href="{{ route('patient.appointments') }}" class="text-sm font-semibold {{ request()->routeIs('patient.appointments') ? 'text-brand-600' : 'text-slate-600 hover:text-slate-900' }} transition-colors">
                        Mis Citas
                    </a>
                    @if($branding->canShowBookingButton())
                        <a href="{{ route('booking') }}" class="text-sm font-bold text-brand-600 hover:text-brand-700 transition-colors flex items-center gap-1">
                            <span>+</span> Agendar Cita
                        </a>
                    @endif
                </nav>

                <!-- User Dropdown / Logout -->
                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-bold text-slate-900">{{ auth()->user()->name }}</span>
                        <span class="text-[11px] text-slate-500">Paciente</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-slate-50 rounded-xl transition-colors" title="Cerrar Sesión">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mobile Bottom Nav Bar -->
        <div class="sm:hidden border-t border-slate-100 bg-white grid {{ $branding->canShowBookingButton() ? 'grid-cols-3' : 'grid-cols-2' }} py-2 text-center text-xs">
            <a href="{{ route('patient.dashboard') }}" class="py-1.5 flex flex-col items-center gap-1 {{ request()->routeIs('patient.dashboard') ? 'text-brand-600 font-bold' : 'text-slate-600' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Inicio</span>
            </a>
            <a href="{{ route('patient.appointments') }}" class="py-1.5 flex flex-col items-center gap-1 {{ request()->routeIs('patient.appointments') ? 'text-brand-600 font-bold' : 'text-slate-600' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Mis Citas</span>
            </a>
            @if($branding->canShowBookingButton())
                <a href="{{ route('booking') }}" class="py-1.5 flex flex-col items-center gap-1 text-brand-600 font-bold">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Agendar</span>
                </a>
            @endif
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 py-6 sm:py-8">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Simple Patient Footer -->
    <footer class="bg-white border-t border-slate-100 py-6 text-center text-xs text-slate-400">
        <div class="max-w-4xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>{{ $branding->clientName() }} — Centro Médico</span>
            <x-brand.nexaris-logo variant="footer" />
        </div>
    </footer>

    <!-- Global Toast Container -->
    <x-toast-container />

    @livewireScripts
</body>
</html>
