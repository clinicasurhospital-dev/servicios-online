<!DOCTYPE html>
<html lang="es" class="h-full bg-[#F7F8FC]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? $branding->title('public') }}</title>
    <meta name="description" content="SurHospital — Centro de Especialidades Médicas y Agendamiento en Línea. Cuidamos de tu salud con excelencia y calidez.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased text-slate-800 bg-[#F7F8FC] min-h-full flex flex-col selection:bg-brand-500 selection:text-white">
    <!-- Navbar Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo Brand -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 group transition-transform duration-150 active:scale-95">
                    <x-brand.logo />
                </a>

                <!-- Desktop Nav Navigation -->
                <nav class="hidden lg:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-xs xl:text-sm font-semibold text-slate-600 hover:text-brand-600 transition-colors">Inicio</a>
                    <a href="{{ route('home') }}#servicios" class="text-xs xl:text-sm font-semibold text-slate-600 hover:text-brand-600 transition-colors">Servicios</a>
                    <a href="{{ route('home') }}#paquetes" class="text-xs xl:text-sm font-semibold text-brand-600 hover:text-brand-700 transition-colors font-bold">Paquetes Quirúrgicos</a>
                    <a href="{{ route('home') }}#especialidades" class="text-xs xl:text-sm font-semibold text-slate-600 hover:text-brand-600 transition-colors">Especialidades</a>
                    <a href="{{ route('home') }}#medicos" class="text-xs xl:text-sm font-semibold text-slate-600 hover:text-brand-600 transition-colors">Staff Médico</a>
                    <a href="{{ route('home') }}#nosotros" class="text-xs xl:text-sm font-semibold text-slate-600 hover:text-brand-600 transition-colors">Nosotros</a>
                    <a href="{{ route('home') }}#aliados" class="text-xs xl:text-sm font-semibold text-slate-600 hover:text-brand-600 transition-colors">Convenios</a>
                    <a href="{{ route('home') }}#contacto" class="text-xs xl:text-sm font-semibold text-slate-600 hover:text-brand-600 transition-colors">Contacto</a>
                </nav>

                <!-- Actions / Auth CTA -->
                <div class="flex items-center gap-3">
                    <a href="tel:042590670" class="hidden xl:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-all">
                        <svg class="h-3.5 w-3.5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        <span>042 590670</span>
                    </a>

                    @auth
                        @if(auth()->user()->isAdmin() || auth()->user()->isReception() || auth()->user()->isDoctor())
                            <x-button href="{{ route('admin.dashboard') }}" variant="secondary" size="sm">
                                Panel Administrativo
                            </x-button>
                        @else
                            <x-button href="{{ route('patient.dashboard') }}" variant="secondary" size="sm">
                                Mi Portal
                            </x-button>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-xl transition-colors" title="Cerrar Sesión">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex text-sm font-bold text-slate-700 hover:text-brand-600 px-3 py-2 transition-colors">
                            Iniciar Sesión
                        </a>
                        <x-button href="{{ route('booking') }}" variant="primary" size="sm" class="shadow-md shadow-brand-500/20">
                            <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Agendar Cita
                        </x-button>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 pt-16 pb-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
                <!-- Brand Info -->
                <div class="space-y-4">
                    <x-brand.logo variant="white" />
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Somos Clínica Sur Hospital, institución de salud privada en Guayaquil proporcionando atención oportuna e integral con estándares de calidad, ética profesional y tecnología de vanguardia.
                    </p>
                    <div class="pt-2 text-xs text-slate-400 flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-semibold text-emerald-400">Emergencias, Laboratorio e Imágenes 24/7</span>
                    </div>
                </div>

                <!-- Navigation -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white mb-4">Enlaces y Áreas</h4>
                    <ul class="space-y-2.5 text-sm text-slate-400">
                        <li><a href="{{ route('home') }}#servicios" class="hover:text-white transition-colors">Servicios y Hospitalización</a></li>
                        <li><a href="{{ route('home') }}#paquetes" class="hover:text-white transition-colors">Paquetes Quirúrgicos</a></li>
                        <li><a href="{{ route('home') }}#especialidades" class="hover:text-white transition-colors">Especialidades Médicas</a></li>
                        <li><a href="{{ route('home') }}#medicos" class="hover:text-white transition-colors">Directorio de Médicos</a></li>
                        <li><a href="{{ route('home') }}#aliados" class="hover:text-white transition-colors">Aseguradoras y Convenios</a></li>
                        <li><a href="{{ route('booking') }}" class="text-brand-300 font-semibold hover:text-white transition-colors">Agendamiento en Línea</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white mb-4">Contacto Directo</h4>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li class="flex items-start gap-2.5">
                            <svg class="h-5 w-5 text-brand-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ $branding->contact('address') }}</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-5 w-5 text-brand-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>PBX: {{ $branding->contact('phone') }} (Ext. 100 / Ext. 0)</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-5 w-5 text-brand-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $branding->contact('email') }}</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="h-5 w-5 text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $branding->get('contact.careers_email', 'postulaciones@surhospital.net') }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Legal / Hours -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-white mb-4">Horarios & Cobertura</h4>
                    <p class="text-sm text-slate-400 mb-3 leading-relaxed">
                        {{ $branding->contact('working_hours_display') }}
                    </p>
                    <div class="mt-4 pt-4 border-t border-slate-800 space-y-1">
                        <span class="block text-xs font-semibold text-slate-400">Atención Particular y Aseguradoras</span>
                        <span class="block text-xs text-slate-500">Centro Quirúrgico y Hospitalario de Guayaquil</span>
                    </div>
                </div>
            </div>

            <!-- Bottom White Label Row -->
            <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} {{ $branding->clientName() }}. Todos los derechos reservados.</p>
                <x-brand.nexaris-logo variant="footer" />
            </div>
        </div>
    </footer>

    <!-- Global Toast Container -->
    <x-toast-container />

    @livewireScripts
</body>
</html>
