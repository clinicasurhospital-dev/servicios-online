@extends('layouts.app')

@section('title', config('branding.client_name') . ' — Tu Salud en Manos de Especialistas')
@section('meta_description', 'Clínica Sur Hospital en Guayaquil. Especialistas médicos, quirófanos modernos, hemodinamia, imágenes TAC/RM, laboratorio 24h y agendamiento en línea.')

@section('content')
<!-- HERO SECTION -->
<section class="relative bg-gradient-to-b from-brand-soft/50 via-white to-white py-16 lg:py-24 overflow-hidden">
    <!-- Decorative background blobs -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-brand-primary/5 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 rounded-full bg-emerald-500/5 blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-brand-soft text-brand-primary text-xs font-bold uppercase tracking-wider border border-brand-primary/15 shadow-xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand-primary animate-pulse"></span>
                    <span>Clínica Sur Hospital • Guayaquil, Ecuador</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.1]">
                    Tu salud en manos de <span class="text-brand-primary">especialistas de confianza.</span>
                </h1>

                <p class="text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 font-normal leading-relaxed">
                    Proporcionamos atención oportuna en salud integral con tecnología médica avanzada, quirófanos de vanguardia, hemodinamia, imágenes y agendamiento 100% digital.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                    <a href="{{ route('booking') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-base shadow-xl shadow-brand-primary/25 hover:shadow-brand-primary/40 transition-all transform hover:-translate-y-0.5 flex items-center justify-center space-x-2.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Agendar Cita en Línea</span>
                    </a>
                    <a href="#paquetes" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 font-bold text-base border border-slate-200 shadow-xs transition-all flex items-center justify-center space-x-2">
                        <span>Paquetes Quirúrgicos</span>
                        <svg class="w-4 h-4 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </a>
                </div>

                <!-- Trust Metrics -->
                <div class="grid grid-cols-3 gap-4 pt-8 border-t border-slate-200/80 max-w-xl mx-auto lg:mx-0 text-left">
                    <div>
                        <span class="block text-2xl sm:text-3xl font-black text-brand-primary">14+</span>
                        <span class="text-xs text-slate-500 font-medium">Especialidades Médicas</span>
                    </div>
                    <div>
                        <span class="block text-2xl sm:text-3xl font-black text-emerald-600">24/7</span>
                        <span class="text-xs text-slate-500 font-medium">Emergencias & Laboratorio</span>
                    </div>
                    <div>
                        <span class="block text-2xl sm:text-3xl font-black text-brand-primary">100%</span>
                        <span class="text-xs text-slate-500 font-medium">Atención Complementaria</span>
                    </div>
                </div>
            </div>

            <!-- Hero Feature Card -->
            <div class="lg:col-span-5 relative">
                <div class="relative mx-auto max-w-md bg-white rounded-3xl p-6 sm:p-8 shadow-2xl shadow-slate-200/80 border border-slate-100">
                    <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-brand-primary uppercase tracking-wider">Atención Médica Inmediata</span>
                                <h3 class="text-base font-extrabold text-slate-900">Servicios Destacados</h3>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                            Activo
                        </span>
                    </div>

                    <div class="py-4 space-y-3">
                        <div class="p-3.5 rounded-2xl bg-slate-50 flex items-center justify-between border border-slate-100 hover:bg-slate-100/80 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></div>
                                <span class="text-xs sm:text-sm font-semibold text-slate-800">Sala de Hemodinamia</span>
                            </div>
                            <span class="text-[11px] font-bold text-brand-primary bg-white px-2.5 py-1 rounded-lg border border-slate-200">Fluoroscopía 3D</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50 flex items-center justify-between border border-slate-100 hover:bg-slate-100/80 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                                <span class="text-xs sm:text-sm font-semibold text-slate-800">Imágenes (TAC / RM / RX)</span>
                            </div>
                            <span class="text-[11px] font-bold text-brand-primary bg-white px-2.5 py-1 rounded-lg border border-slate-200">Digital 24h</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50 flex items-center justify-between border border-slate-100 hover:bg-slate-100/80 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                                <span class="text-xs sm:text-sm font-semibold text-slate-800">Laboratorio Clínico</span>
                            </div>
                            <span class="text-[11px] font-bold text-emerald-700 bg-white px-2.5 py-1 rounded-lg border border-slate-200">Alta Complejidad</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-slate-50 flex items-center justify-between border border-slate-100 hover:bg-slate-100/80 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                                <span class="text-xs sm:text-sm font-semibold text-slate-800">Quirófanos & Cirugía</span>
                            </div>
                            <span class="text-[11px] font-bold text-brand-primary bg-white px-2.5 py-1 rounded-lg border border-slate-200">Laparoscopía</span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <a href="{{ route('booking') }}" class="block w-full text-center py-3.5 rounded-xl bg-slate-900 hover:bg-brand-primary text-white font-bold text-sm transition-all shadow-md">
                            Reservar Turno o Procedimiento
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- SECCIÓN: SERVICIOS Y ÁREAS HOSPITALARIAS -->
<section id="servicios" class="py-20 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary border border-brand-primary/10">
                Medical Services & Infraestructura
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-4 tracking-tight">
                Nuestros Servicios Hospitalarios
            </h2>
            <p class="text-slate-600 mt-3 text-base">
                En Clínica Sur Hospital contamos con diversas áreas equipadas con tecnología de última generación para brindar una atención integral y diagnósticos oportunos y precisos.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Hemodinamia -->
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-brand-200 hover:shadow-xl transition-all duration-200 group flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-brand-primary/10 text-brand-primary flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Sala de Hemodinamia</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Equipamiento de alta tecnología con fluoroscopía digitalizada para diagnóstico y tratamiento cardiovascular de precisión.
                    </p>
                    <ul class="space-y-2 text-xs text-slate-500 font-medium">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                            <span>Cateterismo y estudio hemodinámico</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                            <span>Coronariografías diagnósticas y terapéuticas</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                            <span>Ventriculografías y angiografías</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-200/60">
                    <a href="{{ route('booking') }}" class="text-xs font-bold text-brand-primary hover:text-brand-primary-dark inline-flex items-center gap-1">
                        <span>Consultar disponibilidad</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>

            <!-- Diagnóstico por Imágenes -->
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-brand-200 hover:shadow-xl transition-all duration-200 group flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-brand-primary/10 text-brand-primary flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Diagnóstico por Imágenes</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Centro radiológico integral con resultados rápidos y tecnología digital para imágenes anatómicas nítidas.
                    </p>
                    <ul class="space-y-2 text-xs text-slate-500 font-medium">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                            <span>Tomografía Computarizada (TAC) simple y con contraste</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                            <span>Resonancia Magnética (RM) y Angioresonancias</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                            <span>Rayos X Digitales y Ecografía Doppler vascular</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-200/60">
                    <a href="{{ route('booking') }}" class="text-xs font-bold text-brand-primary hover:text-brand-primary-dark inline-flex items-center gap-1">
                        <span>Agendar estudio de imagen</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>

            <!-- Laboratorio Clínico 24h -->
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-brand-200 hover:shadow-xl transition-all duration-200 group flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-brand-primary/10 text-brand-primary flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Laboratorio Clínico 24/7</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Servicios de laboratorio clínico de alta, mediana y baja complejidad funcionando las 24 horas, los 365 días del año.
                    </p>
                    <ul class="space-y-2 text-xs text-slate-500 font-medium">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                            <span>Bioquímica, hematología y coagulación</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                            <span>Marcadores tumorales e inmunología</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                            <span>Tomas de muestra de urgencia 24/7</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-200/60">
                    <a href="tel:042590670" class="text-xs font-bold text-brand-primary hover:text-brand-primary-dark inline-flex items-center gap-1">
                        <span>Consultar pruebas al 042 590670</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>

            <!-- Quirófano & Cirugía Avanzada -->
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-brand-200 hover:shadow-xl transition-all duration-200 group flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-brand-primary/10 text-brand-primary flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Quirófanos & Procedimientos</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Salas de operaciones modernas y equipadas con torres laparoscópicas y soporte anestesiológico permanente.
                    </p>
                    <ul class="space-y-2 text-xs text-slate-500 font-medium">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                            <span>Cirugía laparoscópica y mínimamente invasiva</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                            <span>Salas de recuperación y monitoreo postquirúrgico</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                            <span>Biopsias cerebrales estereotáxicas con neuromonitoreo</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-200/60">
                    <a href="#paquetes" class="text-xs font-bold text-brand-primary hover:text-brand-primary-dark inline-flex items-center gap-1">
                        <span>Ver paquetes quirúrgicos</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>

            <!-- Área de Emergencias 24/7 -->
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-brand-200 hover:shadow-xl transition-all duration-200 group flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Área de Emergencias 24/7</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Atención médica de urgencia y emergencia las 24 horas del día, los 365 días del año con médicos especialistas.
                    </p>
                    <ul class="space-y-2 text-xs text-slate-500 font-medium">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            <span>Médicos especialistas presenciales de guardia</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            <span>Soporte directo con UTI e imágenes inmediatas</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            <span>Triage y estabilización rápida de pacientes</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-200/60">
                    <a href="tel:042590670" class="text-xs font-bold text-rose-600 hover:text-rose-700 inline-flex items-center gap-1">
                        <span>Línea directa: 042 590670</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>

            <!-- Consulta Externa -->
            <div class="p-8 rounded-3xl bg-slate-50 border border-slate-100 hover:border-brand-200 hover:shadow-xl transition-all duration-200 group flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-brand-primary/10 text-brand-primary flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Consulta Externa</h3>
                    <p class="text-sm text-slate-600 leading-relaxed mb-4">
                        Atención médica ambulatoria en más de 14 especialidades clínicas y quirúrgicas con citas programadas sin esperas.
                    </p>
                    <ul class="space-y-2 text-xs text-slate-500 font-medium">
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                            <span>Especialistas certificados por el MSP</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                            <span>Horarios extendidos de Lunes a Sábado</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                            <span>Agendamiento digital ágil y confirmación inmediata</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-200/60">
                    <a href="{{ route('booking') }}" class="text-xs font-bold text-brand-primary hover:text-brand-primary-dark inline-flex items-center gap-1">
                        <span>Agendar consulta médica</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- SECCIÓN: PAQUETES QUIRÚRGICOS OFICIALES SURHOSPITAL -->
<section id="paquetes" class="py-20 bg-slate-900 text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:16px_16px]"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-primary/30 text-brand-300 border border-brand-primary/40">
                Paquetes Quirúrgicos Integrales
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-white mt-4 tracking-tight">
                Cirugías con Todo Incluido y Precios Transparentes
            </h2>
            <p class="text-slate-400 mt-3 text-base">
                En Clínica Sur Hospital cuidamos de tu salud y economía con paquetes quirúrgicos cerrados, sin sorpresas ni costos ocultos.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
            
            <!-- Paquete Apendicectomía -->
            <div class="bg-slate-800/90 rounded-3xl p-8 sm:p-10 border border-slate-700 hover:border-brand-500 transition-all duration-300 shadow-2xl relative flex flex-col justify-between group">
                <div class="absolute top-6 right-6">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-brand-primary text-white">
                        Alta Demanda
                    </span>
                </div>

                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-brand-400">Cirugía Digestiva</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-white mt-1">Apendicectomía</h3>
                    <p class="text-xs text-slate-400 mt-2">Procedimiento quirúrgico para la extracción del apéndice cecal con hospitalización completa.</p>

                    <div class="my-6 p-4 rounded-2xl bg-slate-900/80 border border-slate-700 flex items-baseline gap-2">
                        <span class="text-4xl sm:text-5xl font-black text-white">$980</span>
                        <span class="text-xs text-slate-400">/ Valor en efectivo</span>
                    </div>

                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300 mb-3">El Paquete Incluye:</h4>
                    <ul class="space-y-2.5 text-sm text-slate-300">
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Valoración pre-quirúrgica integral</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Hospitalización por 24 Horas</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Honorarios médicos quirúrgicos</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Derecho de Quirófano equipado</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Servicio de Anestesia y Anestesiólogo</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Medicamentos e Insumos quirúrgicos</span>
                        </li>
                    </ul>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-700">
                    <a href="{{ route('booking') }}" class="block w-full text-center py-4 rounded-2xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-sm transition-all shadow-lg shadow-brand-primary/30">
                        Agendar Valoración Quirúrgica
                    </a>
                </div>
            </div>

            <!-- Paquete Cesárea -->
            <div class="bg-slate-800/90 rounded-3xl p-8 sm:p-10 border border-slate-700 hover:border-emerald-500 transition-all duration-300 shadow-2xl relative flex flex-col justify-between group">
                <div class="absolute top-6 right-6">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white">
                        Maternidad Segura
                    </span>
                </div>

                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Ginecología & Maternidad</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-white mt-1">Cesárea Integral</h3>
                    <p class="text-xs text-slate-400 mt-2">Nacimiento seguro con el mejor equipo obstétrico, pediátrico y calidez humana.</p>

                    <div class="my-6 p-4 rounded-2xl bg-slate-900/80 border border-slate-700 flex items-baseline gap-2">
                        <span class="text-4xl sm:text-5xl font-black text-white">$850</span>
                        <span class="text-xs text-slate-400">/ Valor en efectivo</span>
                    </div>

                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300 mb-3">El Paquete Incluye:</h4>
                    <ul class="space-y-2.5 text-sm text-slate-300">
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Valoración pre-quirúrgica obstétrica</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Hospitalización por 24 Horas en sala</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Honorarios de Gineco-Obstetra y Pediatra</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Derecho de Quirófano y Sala de Partos</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Servicio de Anestesia y Monitoreo</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            <span>Medicamentos e Insumos quirúrgicos</span>
                        </li>
                    </ul>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-700">
                    <a href="{{ route('booking') }}" class="block w-full text-center py-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition-all shadow-lg shadow-emerald-600/30">
                        Agendar Valoración de Maternidad
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- SECCIÓN: TECNOLOGÍA MÉDICA DESTACADA (BIOPSIA CEREBRAL POR ESTEREOTAXIA) -->
<section class="py-16 bg-gradient-to-r from-brand-soft via-white to-blue-50/50 border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-brand-100 shadow-xl grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-8 space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-soft text-brand-primary text-xs font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-brand-primary"></span>
                    <span>Innovación Médica en Neurocirugía</span>
                </div>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Biopsia Cerebral Guiada por Estereotaxia con Neuromonitoreo Intraoperatorio
                </h3>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    En Clínica Sur Hospital realizamos biopsias cerebrales estereotáxicas de alta precisión para tumores parietales y lesiones del sistema nervioso central. Nuestro staff de especialistas en neurocirugía utiliza tecnología de vanguardia para brindar procedimientos seguros y diagnósticos patológicos exactos.
                </p>
                <div class="pt-2 flex flex-wrap items-center gap-4 text-xs font-semibold text-slate-700">
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-brand-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg> Mínima Invasión</span>
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-brand-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg> Neuromonitoreo Continuo</span>
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-brand-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg> Neurocirujanos Certificados</span>
                </div>
            </div>
            <div class="lg:col-span-4 text-center lg:text-right">
                <a href="{{ route('booking') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-sm shadow-md transition-all">
                    <span>Consultar Neurocirugía</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- SECCIÓN: ESPECIALIDADES MÉDICAS -->
<section id="especialidades" class="py-20 bg-[#F7F8FC]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-14">
            <div>
                <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary border border-brand-primary/10">
                    Áreas Médicas
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-3 tracking-tight">
                    Nuestras Especialidades Médicas
                </h2>
                <p class="text-slate-600 mt-2 text-base max-w-xl">
                    Consulta con médicos especialistas en diagnóstico, prevención y tratamiento oportuno.
                </p>
            </div>
            <a href="{{ route('booking') }}" class="mt-4 md:mt-0 text-sm font-bold text-brand-primary hover:text-brand-primary-dark inline-flex items-center gap-1 group">
                <span>Agendar en cualquier especialidad</span>
                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($specialties as $specialty)
                <div class="bg-white rounded-2xl p-6 border border-slate-100 hover:border-brand-200 hover:shadow-lg transition-all duration-200 flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-md">
                                {{ $specialty->doctors_count }} {{ $specialty->doctors_count == 1 ? 'Especialista' : 'Especialistas' }}
                            </span>
                        </div>

                        <h3 class="text-base font-extrabold text-slate-900 group-hover:text-brand-primary transition-colors">
                            {{ $specialty->name }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-2 line-clamp-3 leading-relaxed">
                            {{ $specialty->description }}
                        </p>

                        @if($specialty->procedures->count() > 0)
                            <div class="mt-4 pt-3 border-t border-slate-100">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Procedimientos:</span>
                                <div class="space-y-1">
                                    @foreach($specialty->procedures->take(2) as $proc)
                                        <div class="text-xs text-slate-700 font-medium flex items-center justify-between">
                                            <span class="truncate pr-2">• {{ $proc->name }}</span>
                                            <span class="text-[11px] font-bold text-brand-primary shrink-0">${{ number_format($proc->price, 0) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 block uppercase font-medium">Consulta</span>
                            <span class="text-sm font-extrabold text-slate-900">${{ number_format($specialty->base_price, 2) }}</span>
                        </div>
                        <a href="{{ route('booking', ['specialty_id' => $specialty->id]) }}" class="px-3.5 py-1.5 rounded-xl bg-slate-50 hover:bg-brand-primary hover:text-white text-brand-primary text-xs font-bold border border-slate-200/80 transition-all">
                            Agendar
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- SECCIÓN: STAFF MÉDICO OFICIAL DE SURHOSPITAL -->
<section id="medicos" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary border border-brand-primary/10">
                Staff Médico Oficial
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-4 tracking-tight">
                Conoce a Nuestro Equipo de Médicos
            </h2>
            <p class="text-slate-600 mt-3 text-base">
                Profesionales certificados con amplia trayectoria y vocación de servicio, dedicados a tu salud y bienestar en Clínica Sur Hospital.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($doctors as $doctor)
                <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100 hover:border-brand-200 hover:shadow-xl transition-all duration-200 flex flex-col justify-between group">
                    <div>
                        <!-- Doctor Avatar -->
                        <div class="w-16 h-16 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-extrabold text-xl mb-4 border border-brand-primary/20 shadow-xs group-hover:scale-105 transition-transform">
                            {{ substr(str_replace(['Dr. ', 'Dra. '], '', $doctor->user->name), 0, 2) }}
                        </div>

                        <span class="text-xs font-bold uppercase tracking-wider text-brand-primary bg-white px-2.5 py-1 rounded-lg border border-slate-200 inline-block mb-2">
                            {{ $doctor->primary_specialty_name }}
                        </span>

                        <h3 class="text-base font-extrabold text-slate-900 leading-snug">
                            {{ $doctor->user->name }}
                        </h3>

                        <p class="text-xs text-slate-500 mt-1 font-mono">
                            {{ $doctor->license_number }}
                        </p>

                        <p class="text-xs text-slate-600 mt-3 line-clamp-3 leading-relaxed">
                            {{ $doctor->bio }}
                        </p>

                        <div class="mt-4 pt-3 border-t border-slate-200/60 text-xs text-slate-500 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span class="truncate">{{ $doctor->room_office ?? 'Torre Médica SurHospital' }}</span>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-200/80 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 block uppercase font-medium">Consulta</span>
                            <span class="text-sm font-extrabold text-slate-900">${{ number_format($doctor->default_consultation_price, 2) }}</span>
                        </div>
                        <a href="{{ route('booking', ['doctor_id' => $doctor->id]) }}" class="px-4 py-2 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white text-xs font-bold shadow-sm transition-all">
                            Agendar Cita
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- SECCIÓN: ACERCA DE NOSOTROS (MISIÓN, VISIÓN Y 6 PILARES) -->
<section id="nosotros" class="py-20 bg-slate-50 border-t border-slate-200/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-16">
            <div>
                <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary border border-brand-primary/10">
                    Acerca de Nosotros • Clínica Sur Hospital
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-4 leading-tight">
                    Excelencia médica, ética profesional y compromiso con la comunidad.
                </h2>
                <p class="text-slate-600 mt-4 text-base leading-relaxed">
                    Clínica Sur Hospital está ubicada en el corazón de la ciudad de Guayaquil y cuenta con una sólida trayectoria en la prestación de servicios de salud privada de alta y mediana complejidad.
                </p>

                <!-- Misión & Visión -->
                <div class="mt-8 space-y-4">
                    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-brand-primary/10 text-brand-primary flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </div>
                            <h4 class="text-base font-extrabold text-slate-900">Nuestra Misión</h4>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Somos Clínica Sur Hospital una institución que ofrece servicios de salud privada proporcionando atención oportuna en salud integral con estándares de calidad, a través de ética profesional con responsabilidad social contribuyendo así al bienestar y cuidado, respaldado por tecnología que garantiza la satisfacción de cada uno de nuestros pacientes.
                        </p>
                    </div>

                    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </div>
                            <h4 class="text-base font-extrabold text-slate-900">Nuestra Visión</h4>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Ser líder en prestación de servicios de salud a nivel nacional, liderar en procedimientos asistenciales y administrativos, donde prime la unión de esfuerzos y el espíritu de servicio con calidez, encaminados a impactar favorablemente en las condiciones de vida de la comunidad.
                        </p>
                    </div>
                </div>
            </div>

            <!-- 6 Pilares de Atención -->
            <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-xl">
                <h3 class="text-xl font-black text-slate-900 mb-6">¿Por qué somos tu mejor opción?</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-brand-soft text-brand-primary flex items-center justify-center shrink-0 mt-0.5 font-bold text-xs">1</div>
                        <div>
                            <h5 class="text-sm font-bold text-slate-900">Experiencia y Calidad de Atención</h5>
                            <p class="text-xs text-slate-500">Profesionales altamente capacitados y con años de experiencia en diferentes especialidades médicas.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-brand-soft text-brand-primary flex items-center justify-center shrink-0 mt-0.5 font-bold text-xs">2</div>
                        <div>
                            <h5 class="text-sm font-bold text-slate-900">Variedad de Especialidades</h5>
                            <p class="text-xs text-slate-500">Capacidad integral para atender desde consultas preventivas hasta cirugías de alta complejidad.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-brand-soft text-brand-primary flex items-center justify-center shrink-0 mt-0.5 font-bold text-xs">3</div>
                        <div>
                            <h5 class="text-sm font-bold text-slate-900">Trato Humano y Cercano</h5>
                            <p class="text-xs text-slate-500">Relación de empatía, calidez y confianza con cada paciente y su familia.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-brand-soft text-brand-primary flex items-center justify-center shrink-0 mt-0.5 font-bold text-xs">4</div>
                        <div>
                            <h5 class="text-sm font-bold text-slate-900">Eficiencia y Puntualidad</h5>
                            <p class="text-xs text-slate-500">Proceso de cita rápido y sencillo, respetando el tiempo valioso de nuestros pacientes.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-brand-soft text-brand-primary flex items-center justify-center shrink-0 mt-0.5 font-bold text-xs">5</div>
                        <div>
                            <h5 class="text-sm font-bold text-slate-900">Compromiso con la Seguridad</h5>
                            <p class="text-xs text-slate-500">Cumplimiento estricto de normas de bioseguridad y protocolos de esterilización hospitalaria.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-brand-soft text-brand-primary flex items-center justify-center shrink-0 mt-0.5 font-bold text-xs">6</div>
                        <div>
                            <h5 class="text-sm font-bold text-slate-900">Infraestructura Moderna</h5>
                            <p class="text-xs text-slate-500">Quirófanos, salas de recuperación, hemodinamia e imágenes con tecnología avanzada.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- SECCIÓN: CONVENIOS Y ASEGURADORAS ALIADAS -->
<section id="aliados" class="py-16 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary border border-brand-primary/10">
                Red de Cobertura
            </span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">
                Nuestros Aliados y Aseguradoras
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Aceptamos planes de salud y convenios con las principales entidades aseguradoras del país.
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4 items-center">
            @php
                $partners = [
                    'Asisken', 'Confiamed', 'MAPFRE', 'Humana S.A.', 'Latina Seguros',
                    'Privilegio', 'Inmedical', 'Medec', 'Nesec', 'Sweden'
                ];
            @endphp
            @foreach($partners as $partner)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-center hover:bg-brand-soft/30 hover:border-brand-200 transition-all">
                    <span class="text-xs sm:text-sm font-bold text-slate-700 tracking-wide">{{ $partner }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- SECCIÓN: PREGUNTAS FRECUENTES (FAQ) -->
<section class="py-20 bg-[#F7F8FC]" x-data="{ activeFaq: 1 }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary border border-brand-primary/10">
                Resolución de Dudas
            </span>
            <h2 class="text-3xl font-black text-slate-900 mt-3">
                Preguntas Frecuentes
            </h2>
        </div>

        <div class="space-y-4">
            <!-- FAQ 1 -->
            <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
                <button @click="activeFaq = activeFaq === 1 ? null : 1" class="w-full px-6 py-5 text-left flex items-center justify-between font-bold text-slate-900 hover:text-brand-primary transition-colors">
                    <span class="text-sm sm:text-base">¿Cuentan con servicio de laboratorio clínico?</span>
                    <svg class="w-5 h-5 transform transition-transform" :class="activeFaq === 1 ? 'rotate-180 text-brand-primary' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="activeFaq === 1" x-collapse class="px-6 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Sí. Clínica Sur Hospital cuenta con los servicios de Laboratorio Clínico de alta, mediana y baja complejidad; funciona las 24 horas los 365 días del año facilitando el acceso a la toma de exámenes para un diagnóstico oportuno.
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
                <button @click="activeFaq = activeFaq === 2 ? null : 2" class="w-full px-6 py-5 text-left flex items-center justify-between font-bold text-slate-900 hover:text-brand-primary transition-colors">
                    <span class="text-sm sm:text-base">¿Cuentan con área de emergencia?</span>
                    <svg class="w-5 h-5 transform transition-transform" :class="activeFaq === 2 ? 'rotate-180 text-brand-primary' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="activeFaq === 2" x-collapse class="px-6 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Nuestra Área de Emergencias atiende las 24 horas del día, los 365 días del año. Contamos con médicos especialistas en varias áreas de guardia permanente para estabilización y tratamiento inmediato.
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
                <button @click="activeFaq = activeFaq === 3 ? null : 3" class="w-full px-6 py-5 text-left flex items-center justify-between font-bold text-slate-900 hover:text-brand-primary transition-colors">
                    <span class="text-sm sm:text-base">¿Cómo puedo agendar una consulta médica?</span>
                    <svg class="w-5 h-5 transform transition-transform" :class="activeFaq === 3 ? 'rotate-180 text-brand-primary' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="activeFaq === 3" x-collapse class="px-6 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Puedes agendar directamente desde nuestra plataforma en línea haciendo clic en el botón "Agendar Cita", seleccionando la especialidad o médico de tu preferencia, eligiendo la fecha y hora disponible, y completando tus datos de paciente en menos de 2 minutos.
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-xs">
                <button @click="activeFaq = activeFaq === 4 ? null : 4" class="w-full px-6 py-5 text-left flex items-center justify-between font-bold text-slate-900 hover:text-brand-primary transition-colors">
                    <span class="text-sm sm:text-base">¿Qué formas de pago están disponibles?</span>
                    <svg class="w-5 h-5 transform transition-transform" :class="activeFaq === 4 ? 'rotate-180 text-brand-primary' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="activeFaq === 4" x-collapse class="px-6 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                    Aceptamos pagos en efectivo, transferencias bancarias directas (Banco Pichincha / Banco Bolivariano), tarjetas de crédito y débito, así como coberturas y cartas de garantía con nuestras aseguradoras aliadas.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECCIÓN: CONTACTO DIRECTO & CANALES DE ATENCIÓN -->
<section id="contacto" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-brand-soft via-white to-blue-50/70 rounded-3xl p-8 sm:p-14 border border-brand-100 shadow-xl">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                
                <div class="lg:col-span-7 space-y-4">
                    <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-primary text-white">
                        Canales de Atención
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                        Estamos listos para atenderte en Clínica Sur Hospital.
                    </h2>
                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                        Visítanos en el corazón de Guayaquil o comunícate a nuestra central telefónica para consultas médicas, información de paquetes quirúrgicos y emergencias.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                            <span class="text-xs font-bold text-slate-400 block uppercase">Central Telefónica PBX</span>
                            <a href="tel:042590670" class="text-base font-extrabold text-brand-primary hover:underline">
                                042 590670
                            </a>
                            <span class="block text-xs text-slate-500 mt-0.5">Ext. 100 / Ext. 0</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
                            <span class="text-xs font-bold text-slate-400 block uppercase">Correo Institucional</span>
                            <a href="mailto:info@surhospital.net" class="text-sm font-extrabold text-slate-800 hover:text-brand-primary">
                                info@surhospital.net
                            </a>
                            <span class="block text-xs text-slate-500 mt-0.5">Postulaciones: postulaciones@surhospital.net</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 text-center lg:text-right space-y-4">
                    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-md text-left">
                        <h4 class="font-extrabold text-slate-900 text-sm mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                            Ubicación Principal
                        </h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Centro de la ciudad de Guayaquil, Ecuador.
                        </p>
                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700">Emergencias 24 Horas</span>
                            <a href="tel:042590670" class="px-3 py-1 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold border border-rose-200">
                                Llamar 24/7
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('booking') }}" class="block w-full py-4 rounded-2xl bg-brand-primary hover:bg-brand-primary-dark text-white font-extrabold text-base shadow-lg shadow-brand-primary/25 transition-all">
                        Agendar Cita en Línea Ahora
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
