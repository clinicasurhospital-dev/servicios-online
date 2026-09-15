@extends('layouts.app')

@section('title', config('branding.app_name') . ' — Atención Médica y Especialistas de Confianza')
@section('meta_description', 'En ' . config('branding.app_name') . ' cuidamos de ti y de tu familia con los mejores especialistas, tecnología médica avanzada y agendamiento 100% digital.')

@section('content')
<!-- HERO SECTION -->
<section class="relative bg-gradient-to-b from-brand-soft/40 via-white to-white py-16 lg:py-24 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-brand-soft text-brand-primary text-xs font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-brand-primary animate-pulse"></span>
                    <span>Centro Médico de Alta Especialidad</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.1]">
                    Tu salud en las mejores manos, <span class="text-brand-primary">sin filas ni esperas.</span>
                </h1>

                <p class="text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 font-normal leading-relaxed">
                    Agenda tu cita médica con los mejores especialistas de la región en menos de 2 minutos. Consultas presenciales, procedimientos especializados y atención con calidez humana.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                    <a href="{{ route('booking') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-base shadow-xl shadow-brand-primary/25 hover:shadow-brand-primary/40 transition-all transform hover:-translate-y-0.5 flex items-center justify-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Agendar Cita en Línea</span>
                    </a>
                    <a href="#especialidades" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-base border border-slate-200 shadow-sm transition-all flex items-center justify-center space-x-2">
                        <span>Ver Especialidades</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </a>
                </div>

                <!-- Trust Metrics -->
                <div class="grid grid-cols-3 gap-4 pt-8 border-t border-slate-200/80 max-w-xl mx-auto lg:mx-0 text-left">
                    <div>
                        <span class="block text-2xl sm:text-3xl font-black text-brand-primary">15+</span>
                        <span class="text-xs text-slate-500 font-medium">Especialidades Médicas</span>
                    </div>
                    <div>
                        <span class="block text-2xl sm:text-3xl font-black text-brand-primary">99.8%</span>
                        <span class="text-xs text-slate-500 font-medium">Satisfacción de Pacientes</span>
                    </div>
                    <div>
                        <span class="block text-2xl sm:text-3xl font-black text-brand-primary">24/7</span>
                        <span class="text-xs text-slate-500 font-medium">Agendamiento Digital</span>
                    </div>
                </div>
            </div>

            <!-- Hero Visual Feature Card -->
            <div class="lg:col-span-5 relative">
                <div class="relative mx-auto max-w-md bg-white rounded-3xl p-6 shadow-2xl shadow-slate-200/80 border border-slate-100">
                    <div class="flex items-center space-x-4 pb-6 border-b border-slate-100">
                        <div class="w-14 h-14 rounded-2xl bg-brand-primary/10 text-brand-primary flex items-center justify-center font-bold text-xl">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-brand-primary uppercase tracking-wider">Turnos Disponibles Hoy</span>
                            <h3 class="text-lg font-extrabold text-slate-900">Consulta Rápida</h3>
                        </div>
                    </div>

                    <div class="py-4 space-y-3">
                        <div class="p-3.5 rounded-2xl bg-slate-50 flex items-center justify-between border border-slate-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></div>
                                <span class="text-sm font-semibold text-slate-800">Medicina General</span>
                            </div>
                            <span class="text-xs font-bold text-brand-primary bg-white px-2.5 py-1 rounded-lg border border-slate-200">Hoy 09:30 AM</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50 flex items-center justify-between border border-slate-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                <span class="text-sm font-semibold text-slate-800">Cardiología</span>
                            </div>
                            <span class="text-xs font-bold text-brand-primary bg-white px-2.5 py-1 rounded-lg border border-slate-200">Hoy 11:00 AM</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50 flex items-center justify-between border border-slate-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                                <span class="text-sm font-semibold text-slate-800">Pediatría</span>
                            </div>
                            <span class="text-xs font-bold text-brand-primary bg-white px-2.5 py-1 rounded-lg border border-slate-200">Hoy 02:30 PM</span>
                        </div>
                    </div>

                    <a href="{{ route('booking') }}" class="mt-2 block w-full text-center py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm transition-all">
                        Reservar Turno Ahora
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- SOBRE SURHOSPITAL -->
<section id="nosotros" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary">Sobre {{ config('branding.app_name') }}</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3 leading-tight">
                    Comprometidos con la excelencia y el cuidado humano de tu bienestar.
                </h2>
                <p class="text-slate-600 mt-4 text-base leading-relaxed">
                    {{ config('branding.app_name') }} es una institución médica de vanguardia diseñada para brindar atención integral con los más altos estándares clínicos y tecnológicos. Nuestro equipo multidisciplinario trabaja día a día para brindarte un diagnóstico oportuno y un tratamiento personalizado.
                </p>

                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm">Seguridad y Bioseguridad</h4>
                        <p class="text-xs text-slate-500 mt-1">Instalaciones modernas que cumplen con todos los protocolos sanitarios de habilitación.</p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm">Puntualidad en Turnos</h4>
                        <p class="text-xs text-slate-500 mt-1">Gestión optimizada de citas para reducir al mínimo tus tiempos de espera en clínica.</p>
                    </div>
                </div>
            </div>

            <!-- Features side card -->
            <div class="bg-gradient-to-br from-brand-primary to-brand-primary-dark rounded-3xl p-8 sm:p-12 text-white shadow-xl shadow-brand-primary/20">
                <h3 class="text-2xl font-bold mb-4">¿Por qué elegirnos?</h3>
                <ul class="space-y-4 text-sm text-blue-100">
                    <li class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Cuerpo médico con amplia trayectoria en hospitales universitarios.</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Portal del paciente para consultar tus citas, recibos y reprogramaciones 24/7.</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Flexibilidad en pagos: pasarela online segura o transferencias bancarias directas.</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>Ubicación central accesible con parqueadero privado y facilidades de acceso.</span>
                    </li>
                </ul>

                <div class="mt-8 pt-6 border-t border-white/20 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-blue-200 block">Línea de Atención</span>
                        <span class="font-bold text-lg text-white">{{ config('branding.contact_phone') }}</span>
                    </div>
                    <a href="{{ route('booking') }}" class="px-5 py-2.5 rounded-xl bg-white text-brand-primary font-bold text-xs hover:bg-slate-100 transition-colors">
                        Agendar Ahora
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ESPECIALIDADES -->
<section id="especialidades" class="py-20 bg-slate-50/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary">Nuestros Servicios</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">Especialidades Médicas</h2>
            <p class="text-slate-500 text-sm mt-2">Contamos con profesionales altamente cualificados en las ramas médicas clave.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($specialties as $specialty)
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-xl hover:border-brand-primary/40 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                        </div>
                        <h3 class="font-bold text-slate-900 text-lg group-hover:text-brand-primary transition-colors">{{ $specialty->name }}</h3>
                        <p class="text-slate-500 text-xs mt-2 line-clamp-3 leading-relaxed">{{ $specialty->description }}</p>

                        @if($specialty->procedures->isNotEmpty())
                            <div class="mt-4 pt-3 border-t border-slate-100">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Procedimientos:</span>
                                <ul class="text-xs text-slate-600 space-y-1">
                                    @foreach($specialty->procedures->take(2) as $proc)
                                        <li class="flex items-center space-x-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                                            <span class="truncate">{{ $proc->name }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] text-slate-400 block">Consulta desde</span>
                            <span class="font-extrabold text-brand-primary text-base">${{ number_format($specialty->base_price, 0) }}</span>
                        </div>
                        <a href="{{ route('booking') }}" class="px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-brand-primary hover:text-white text-slate-700 font-bold text-xs transition-colors">
                            Agendar
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- MÉDICOS -->
<section id="medicos" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary">Cuerpo Médico</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2">Nuestros Especialistas</h2>
            <p class="text-slate-500 text-sm mt-2">Médicos certificados con vocación de servicio y constante actualización científica.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($doctors as $doctor)
                <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm hover:shadow-xl transition-all duration-300 flex items-start space-x-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-primary to-brand-primary-dark text-white flex items-center justify-center font-bold text-xl shadow-md shrink-0">
                        {{ substr($doctor->user->name, 0, 2) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-wrap gap-1 mb-1">
                            @foreach($doctor->specialties as $spec)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-soft text-brand-primary">
                                    {{ $spec->name }}
                                </span>
                            @endforeach
                        </div>
                        <h4 class="font-bold text-slate-900 text-base leading-snug">{{ $doctor->user->name }}</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Reg. Profesional: {{ $doctor->license_number }}</p>
                        <p class="text-xs text-slate-500 mt-2 line-clamp-2">{{ $doctor->biography ?: 'Atención especializada con dedicación y calidez.' }}</p>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex justify-end">
                            <a href="{{ route('booking') }}" class="inline-flex items-center space-x-1 text-xs font-bold text-brand-primary hover:text-brand-primary-dark">
                                <span>Ver disponibilidad</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CONTACTO -->
<section id="contacto" class="py-20 bg-slate-50/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <div class="lg:col-span-5 space-y-6">
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary">Contacto y Sedes</span>
                <h2 class="text-3xl font-extrabold text-slate-900">Estamos cerca de ti cuando nos necesitas</h2>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Comunícate con nuestras líneas de atención o visítanos directamente en nuestra sede central. Nuestro personal te brindará asesoría personalizada.
                </p>

                <div class="space-y-4 pt-4">
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-slate-200 flex items-center justify-center text-brand-primary shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Dirección Principal</h4>
                            <p class="text-xs text-slate-500 mt-0.5">{{ config('branding.contact_address') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-slate-200 flex items-center justify-center text-brand-primary shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Central Telefónica / WhatsApp</h4>
                            <p class="text-xs text-slate-500 mt-0.5">{{ config('branding.contact_phone') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-slate-200 flex items-center justify-center text-brand-primary shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Correo Institucional</h4>
                            <p class="text-xs text-slate-500 mt-0.5">{{ config('branding.contact_email') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Inquiry Card -->
            <div class="lg:col-span-7 bg-white rounded-3xl p-8 border border-slate-200/80 shadow-lg shadow-slate-200/50">
                <h3 class="text-xl font-bold text-slate-900 mb-2">Envíanos un Mensaje</h3>
                <p class="text-xs text-slate-500 mb-6">Si tienes dudas sobre convenios, EPS o autorizaciones médicas, escríbenos.</p>

                <form onsubmit="event.preventDefault(); alert('Mensaje recibido. Uno de nuestros asesores te contactará a la brevedad.'); this.reset();" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nombre Completo</label>
                            <input type="text" required class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary" placeholder="Ej: Maria Lopez" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono</label>
                            <input type="tel" required class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary" placeholder="3001234567" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Correo Electrónico</label>
                        <input type="email" required class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary" placeholder="maria@ejemplo.com" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Mensaje o Consulta</label>
                        <textarea rows="3" required class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary" placeholder="¿Cómo podemos ayudarte?"></textarea>
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-sm shadow-md transition-all">
                        Enviar Consulta
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

<!-- CALL TO ACTION BANNER -->
<section class="py-16 bg-gradient-to-r from-brand-primary to-brand-primary-dark text-white text-center">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <h2 class="text-3xl sm:text-4xl font-extrabold">¿Listo para agendar tu consulta médica?</h2>
        <p class="mt-3 text-blue-100 text-base max-w-xl mx-auto">Selecciona tu especialista en pocos segundos y asegura tu turno médico con total tranquilidad.</p>
        <div class="mt-8">
            <a href="{{ route('booking') }}" class="inline-flex items-center space-x-2 px-8 py-4 rounded-2xl bg-white text-brand-primary font-black text-base shadow-2xl hover:bg-slate-50 transition-all transform hover:scale-105">
                <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>Agendar Cita Ahora</span>
            </a>
        </div>
    </div>
</section>
@endsection
