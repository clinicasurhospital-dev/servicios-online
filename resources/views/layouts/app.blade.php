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
    <!-- Top Utility Bar (Desktop only) -->
    <div class="hidden lg:block bg-slate-900 text-slate-300 text-xs py-2 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-2 text-slate-400">
                    <svg class="w-3.5 h-3.5 text-brand-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                    <span>José Mascote 2123 entre Huancavilca y Cap. Nájera, Guayaquil</span>
                </div>
                <div class="flex items-center gap-1.5 text-emerald-400 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Emergencias, Laboratorio e Imágenes 24/7</span>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <a href="tel:042590670" class="flex items-center gap-1.5 hover:text-white transition-colors">
                    <svg class="w-3.5 h-3.5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    <span>PBX: <strong>04 259 0670</strong> (Ext. 100 / 0)</span>
                </a>
                <a href="https://wa.me/593997783576" target="_blank" class="flex items-center gap-1.5 hover:text-emerald-400 transition-colors">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    <span>WhatsApp: <strong>+593 99 778 3576</strong></span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Sticky Navbar Header -->
    <header x-data="{ mobileOpen: false }" class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Brand Logo (Left with generous margin) -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0 mr-6 lg:mr-8 xl:mr-10 group transition-transform duration-150 active:scale-95">
                    <x-brand.logo />
                </a>

                <!-- Desktop Navigation Links (Center) -->
                <nav class="hidden lg:flex items-center gap-1 xl:gap-1.5 flex-1">
                    <a href="{{ route('home') }}" class="px-2.5 py-2 rounded-xl text-xs xl:text-sm font-semibold text-slate-700 hover:text-brand-primary hover:bg-slate-50 whitespace-nowrap transition-all">Inicio</a>
                    <a href="{{ route('home') }}#nosotros" class="px-2.5 py-2 rounded-xl text-xs xl:text-sm font-semibold text-slate-700 hover:text-brand-primary hover:bg-slate-50 whitespace-nowrap transition-all">Quiénes Somos</a>
                    <a href="{{ route('home') }}#servicios" class="px-2.5 py-2 rounded-xl text-xs xl:text-sm font-semibold text-slate-700 hover:text-brand-primary hover:bg-slate-50 whitespace-nowrap transition-all">Servicios</a>
                    <a href="{{ route('home') }}#medicos" class="px-2.5 py-2 rounded-xl text-xs xl:text-sm font-semibold text-slate-700 hover:text-brand-primary hover:bg-slate-50 whitespace-nowrap transition-all">Staff Médico</a>
                    <a href="{{ route('home') }}#especialidades" class="px-2.5 py-2 rounded-xl text-xs xl:text-sm font-semibold text-slate-700 hover:text-brand-primary hover:bg-slate-50 whitespace-nowrap transition-all">Especialidades</a>
                    <a href="{{ route('home') }}#paquetes" class="px-2.5 py-2 rounded-xl text-xs xl:text-sm font-extrabold text-brand-primary bg-brand-soft/60 hover:bg-brand-soft whitespace-nowrap transition-all border border-brand-primary/15">
                        Paquetes
                    </a>
                    <a href="{{ route('home') }}#docencia" class="px-2.5 py-2 rounded-xl text-xs xl:text-sm font-semibold text-slate-700 hover:text-brand-primary hover:bg-slate-50 whitespace-nowrap transition-all">Docencia</a>
                    <a href="{{ route('home') }}#contacto" class="px-2.5 py-2 rounded-xl text-xs xl:text-sm font-semibold text-slate-700 hover:text-brand-primary hover:bg-slate-50 whitespace-nowrap transition-all">Contactos</a>
                </nav>

                <!-- Actions / Auth CTA (Right) -->
                <div class="flex items-center gap-2.5 sm:gap-3 shrink-0">
                    @auth
                        @if(auth()->user()->isAdmin() || auth()->user()->isReception() || auth()->user()->isDoctor())
                            <a href="{{ route('admin.dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs xl:text-sm font-bold shadow-xs transition-all">
                                <svg class="w-4 h-4 text-brand-soft" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                <span>{{ auth()->user()->isDoctor() ? 'Panel Médico' : 'Panel Admin' }}</span>
                            </a>
                        @else
                            <a href="{{ route('patient.dashboard') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs xl:text-sm font-bold shadow-xs transition-all">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <span>Mi Portal</span>
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="hidden sm:inline">
                            @csrf
                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-xl transition-colors" title="Cerrar Sesión">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs xl:text-sm font-bold text-slate-700 hover:text-brand-primary hover:bg-slate-50 border border-slate-200/80 transition-all">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            <span>Iniciar Sesión</span>
                        </a>
                    @endauth

                    @if($branding->canShowBookingButton())
                        <a href="{{ route('booking') }}" class="hidden md:inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white text-xs xl:text-sm font-bold shadow-md shadow-brand-primary/25 hover:shadow-brand-primary/40 transition-all transform hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Agendar Cita</span>
                        </a>
                    @endif

                    <!-- Mobile Hamburger Toggle Button -->
                    <button @click="mobileOpen = !mobileOpen" type="button" class="lg:hidden inline-flex items-center justify-center p-2 rounded-xl text-slate-600 hover:text-brand-primary hover:bg-slate-100 focus:outline-none transition-colors ml-1" aria-label="Abrir Menú de Navegación">
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu Dropdown -->
        <div x-show="mobileOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-3"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-3"
             class="lg:hidden border-t border-slate-100 bg-white/98 backdrop-blur-xl px-4 pt-3 pb-6 space-y-3 shadow-2xl">
            
            @if($branding->canShowBookingButton())
                <!-- Mobile Primary CTA: Agendar Cita en Línea -->
                <div class="pb-1">
                    <a @click="mobileOpen = false" href="{{ route('booking') }}" class="flex items-center justify-center space-x-2 w-full py-3.5 rounded-2xl bg-brand-primary hover:bg-brand-primary-dark text-white font-black text-sm shadow-lg shadow-brand-primary/25 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Agendar Cita en Línea</span>
                    </a>
                </div>
            @endif

            <!-- Mobile Navigation Links -->
            <nav class="grid grid-cols-1 gap-1">
                <a @click="mobileOpen = false" href="{{ route('home') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-800 hover:bg-brand-soft hover:text-brand-primary transition-colors">
                    <span>Inicio</span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
                <a @click="mobileOpen = false" href="{{ route('home') }}#nosotros" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-800 hover:bg-brand-soft hover:text-brand-primary transition-colors">
                    <span>Quiénes Somos</span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
                <a @click="mobileOpen = false" href="{{ route('home') }}#servicios" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-800 hover:bg-brand-soft hover:text-brand-primary transition-colors">
                    <span>Servicios</span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
                <a @click="mobileOpen = false" href="{{ route('home') }}#medicos" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-800 hover:bg-brand-soft hover:text-brand-primary transition-colors">
                    <span>Staff Médico</span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
                <a @click="mobileOpen = false" href="{{ route('home') }}#especialidades" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-800 hover:bg-brand-soft hover:text-brand-primary transition-colors">
                    <span>Especialidades</span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
                <a @click="mobileOpen = false" href="{{ route('home') }}#paquetes" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-extrabold text-brand-primary bg-brand-soft/50 hover:bg-brand-soft transition-colors">
                    <span>Paquetes</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-brand-primary text-white">Quirúrgicos</span>
                </a>
                <a @click="mobileOpen = false" href="{{ route('home') }}#docencia" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-800 hover:bg-brand-soft hover:text-brand-primary transition-colors">
                    <span>Docencia</span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
                <a @click="mobileOpen = false" href="{{ route('home') }}#contacto" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-bold text-slate-800 hover:bg-brand-soft hover:text-brand-primary transition-colors">
                    <span>Contactos</span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </nav>

            <!-- Quick Contacts Row in Mobile Menu -->
            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                <a href="tel:042590670" class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                    <span>PBX 04 259 0670</span>
                </a>
                <a href="https://wa.me/593997783576" target="_blank" class="flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-bold text-emerald-700 hover:bg-emerald-100 transition-colors">
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    <span>WhatsApp</span>
                </a>
            </div>

            <!-- Mobile Auth Buttons -->
            <div class="pt-2 border-t border-slate-100">
                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->isReception() || auth()->user()->isDoctor())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-center w-full py-3 rounded-xl bg-slate-900 text-white font-bold text-sm shadow-md">
                            Ir al {{ auth()->user()->isDoctor() ? 'Panel Médico' : 'Panel Administrativo' }}
                        </a>
                    @else
                        <a href="{{ route('patient.dashboard') }}" class="flex items-center justify-center w-full py-3 rounded-xl bg-slate-900 text-white font-bold text-sm shadow-md">
                            Ir a Mi Portal
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full py-2 text-center text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                            Cerrar Sesión
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="flex items-center justify-center w-full py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-sm transition-colors mb-2">
                        Iniciar Sesión
                    </a>
                @endauth
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
                        <li><a href="{{ route('home') }}#nosotros" class="hover:text-white transition-colors">Quiénes Somos</a></li>
                        <li><a href="{{ route('home') }}#servicios" class="hover:text-white transition-colors">Servicios Hospitalarios</a></li>
                        <li><a href="{{ route('home') }}#medicos" class="hover:text-white transition-colors">Staff Médico</a></li>
                        <li><a href="{{ route('home') }}#especialidades" class="hover:text-white transition-colors">Especialidades Médicas</a></li>
                        <li><a href="{{ route('home') }}#paquetes" class="hover:text-white transition-colors">Paquetes Quirúrgicos</a></li>
                        <li><a href="{{ route('home') }}#docencia" class="hover:text-white transition-colors">Docencia & Posgrados</a></li>
                        <li><a href="{{ route('home') }}#contacto" class="hover:text-white transition-colors">Contactos & Ubicación</a></li>
                        @if($branding->canShowBookingButton())
                            <li><a href="{{ route('booking') }}" class="text-brand-300 font-semibold hover:text-white transition-colors">Agendamiento en Línea</a></li>
                        @endif
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
