@extends('layouts.app')

@section('title', config('branding.client_name') . ' — Especialistas en tu Salud en Guayaquil')
@section('meta_description', 'Clínica Sur Hospital en Guayaquil. Quirófanos inteligentes, sala de hemodinamia, imágenes TAC/RM, laboratorio 24h, emergencias y agendamiento en línea.')

@section('content')
<!-- HERO SECTION -->
<section class="relative bg-gradient-to-b from-brand-soft/40 via-white to-white py-12 lg:py-20 overflow-hidden">
    <!-- Background Accents -->
    <div class="absolute top-0 right-0 -mr-32 -mt-32 w-[32rem] h-[32rem] rounded-full bg-brand-primary/5 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 -ml-32 -mb-32 w-[32rem] h-[32rem] rounded-full bg-emerald-500/5 blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Hero Content -->
            <div class="lg:col-span-6 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-brand-soft text-brand-primary text-xs font-extrabold uppercase tracking-wider border border-brand-primary/15 shadow-xs">
                    <span class="w-2.5 h-2.5 rounded-full bg-brand-primary animate-pulse"></span>
                    <span>Clínica Sur Hospital • Guayaquil, Ecuador</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.1]">
                    Tu salud en manos de <span class="text-brand-primary">especialistas de confianza.</span>
                </h1>

                <p class="text-base sm:text-lg text-slate-600 font-normal leading-relaxed">
                    Brindamos atención médica oportuna y de alta calidad con estándares de excelencia, ética profesional y tecnología de vanguardia en el centro de Guayaquil.
                </p>

                <!-- Action CTAs -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5 pt-2">
                    <a href="{{ route('booking') }}" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-base shadow-xl shadow-brand-primary/25 hover:shadow-brand-primary/40 transition-all transform hover:-translate-y-0.5 flex items-center justify-center space-x-2.5 group">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span>Agendar Cita en Línea</span>
                    </a>
                    <a href="#servicios" class="w-full sm:w-auto px-7 py-4 rounded-2xl bg-white hover:bg-slate-50 text-slate-800 font-bold text-base border border-slate-200 shadow-xs transition-all flex items-center justify-center space-x-2">
                        <span>Conocer Servicios</span>
                        <svg class="w-4 h-4 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </a>
                </div>

                <!-- Trust Metrics -->
                <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-200/80 max-w-xl mx-auto lg:mx-0 text-left">
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
                        <span class="text-xs text-slate-500 font-medium">Quirófanos de Vanguardia</span>
                    </div>
                </div>
            </div>

            <!-- Right Hero Visual Card -->
            <div class="lg:col-span-6 relative">
                <div class="relative rounded-3xl overflow-hidden shadow-2xl shadow-slate-300/80 border border-slate-100 group">
                    <img src="{{ asset('images/surhospital/hero-clinic.jpg') }}" alt="Clínica Sur Hospital Guayaquil" class="w-full h-[420px] object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                    
                    <!-- Floating Overlay Badges -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/20 to-transparent flex flex-col justify-between p-6 sm:p-8">
                        <div class="flex justify-between items-start">
                            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-emerald-500/90 text-white shadow-lg backdrop-blur-md">
                                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                Emergencias 24/7 Activas
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold bg-white/90 text-slate-800 shadow-md backdrop-blur-md">
                                <svg class="w-3.5 h-3.5 text-brand-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                                José Mascote 2123
                            </span>
                        </div>

                        <div class="space-y-2 text-white">
                            <h3 class="text-xl sm:text-2xl font-black tracking-tight text-white drop-shadow-sm">
                                Clínica Sur Hospital
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-200 line-clamp-2">
                                Centro hospitalario con quirófanos inteligentes, sala de hemodinamia, resonancia magnética, tomografía y laboratorio integral.
                            </p>
                            <div class="pt-2 flex flex-wrap gap-2">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white/20 backdrop-blur-md text-white border border-white/20">Hemodinamia</span>
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white/20 backdrop-blur-md text-white border border-white/20">Quirófanos Laparoscópicos</span>
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-white/20 backdrop-blur-md text-white border border-white/20">Imágenes TAC / RM</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- SECCIÓN: QUIÉNES SOMOS & DOCENCIA MÉDICA (surhospital.net) -->
<section id="nosotros" class="py-20 bg-slate-50 border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Image -->
            <div class="lg:col-span-6 relative">
                <div class="relative rounded-3xl overflow-hidden shadow-xl border border-slate-200">
                    <img src="{{ asset('images/surhospital/docencia-medica.jpg') }}" alt="Docencia Médica y Formación de Especialistas SurHospital" class="w-full h-[400px] object-cover">
                    <div class="absolute bottom-0 inset-x-0 p-6 bg-gradient-to-t from-slate-950/90 to-transparent text-white">
                        <span class="text-xs font-bold uppercase tracking-wider text-brand-soft">Formación y Excelencia</span>
                        <h4 class="text-lg font-bold text-white">Compromiso con la Docencia Médica</h4>
                        <p class="text-xs text-slate-300 mt-1">Formación continua de médicos residentes y especialistas con estándares internacionales.</p>
                    </div>
                </div>
            </div>

            <!-- Right Text Copy -->
            <div class="lg:col-span-6 space-y-6">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1 rounded-full bg-brand-soft text-brand-primary text-xs font-bold uppercase tracking-wider border border-brand-primary/10">
                    <span>Quiénes Somos</span>
                </div>

                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight">
                    Comprometidos con tu bienestar, la ética y la excelencia médica.
                </h2>

                <p class="text-base text-slate-600 leading-relaxed">
                    <strong>Clínica Sur Hospital</strong> es una institución de salud privada en Guayaquil comprometida con brindar atención médica oportuna y de calidad, fundamentada en la ética profesional y la responsabilidad social.
                </p>

                <p class="text-base text-slate-600 leading-relaxed">
                    Nuestro enfoque se centra en el bienestar del paciente mediante el uso de tecnología avanzada y un trato humano y cercano. Además, participamos activamente en la docencia médica, apoyando la formación académica de médicos residentes y personal de enfermería.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
                        <div class="w-8 h-8 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">Ética & Seguridad</h4>
                        <p class="text-xs text-slate-500 mt-1">Protocolos clínicos internacionales y seguridad quirúrgica.</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">Tecnología de Punta</h4>
                        <p class="text-xs text-slate-500 mt-1">Fluoroscopía 3D, TAC multicorte, RM y cirugía laparoscópica.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- SECCIÓN: SERVICIOS Y ÁREAS HOSPITALARIAS (FOTOGRAFÍAS REALES) -->
<section id="servicios" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary border border-brand-primary/10">
                Infraestructura & Especialidades
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-4 tracking-tight">
                Nuestros Servicios Hospitalarios
            </h2>
            <p class="text-slate-600 mt-3 text-base">
                Contamos con áreas equipadas con tecnología de última generación para brindar una atención integral y diagnósticos oportunos y precisos.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Hemodinamia -->
            <div class="rounded-3xl bg-white border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl hover:border-brand-primary/30 transition-all duration-300 flex flex-col group">
                <div class="relative h-52 overflow-hidden">
                    <img src="{{ asset('images/surhospital/hemodinamia.jpg') }}" alt="Sala de Hemodinamia SurHospital" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-brand-primary text-white shadow-md">
                            Cardiovascular
                        </span>
                    </div>
                </div>
                <div class="p-6 sm:p-7 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-brand-primary transition-colors mb-2">Sala de Hemodinamia</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-4">
                            Equipamiento de alta tecnología con fluoroscopía digitalizada para diagnóstico y tratamiento cardiovascular y neurointervencionista de precisión.
                        </p>
                        <ul class="space-y-2 text-xs text-slate-600 font-medium mb-6">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                                <span>Cateterismo cardíaco diagnóstico y terapéutico</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                                <span>Angioplastias coronarias y periféricas</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                                <span>Implante de marcapasos y stents</span>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('booking') }}" class="inline-flex items-center justify-center w-full py-3 rounded-xl bg-slate-50 hover:bg-brand-primary text-slate-800 hover:text-white font-bold text-xs border border-slate-200 hover:border-brand-primary transition-all">
                        Consultar Procedimiento &rarr;
                    </a>
                </div>
            </div>

            <!-- Quirófanos & Cirugía -->
            <div class="rounded-3xl bg-white border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl hover:border-brand-primary/30 transition-all duration-300 flex flex-col group">
                <div class="relative h-52 overflow-hidden">
                    <img src="{{ asset('images/surhospital/quirofanos.jpg') }}" alt="Centro Quirúrgico SurHospital" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-blue-600 text-white shadow-md">
                            Quirófanos Inteligentes
                        </span>
                    </div>
                </div>
                <div class="p-6 sm:p-7 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-brand-primary transition-colors mb-2">Centro Quirúrgico</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-4">
                            Quirófanos completamente equipados con torres de laparoscopía, electrobisturí avanzado y salas de recuperación post-anestésica monitoreadas.
                        </p>
                        <ul class="space-y-2 text-xs text-slate-600 font-medium mb-6">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                                <span>Cirugía Laparoscópica mínimamente invasiva</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                                <span>Cirugías digestivas, urológicas y traumatología</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                                <span>Área de recuperación con monitoreo continuo</span>
                            </li>
                        </ul>
                    </div>
                    <a href="#paquetes" class="inline-flex items-center justify-center w-full py-3 rounded-xl bg-slate-50 hover:bg-brand-primary text-slate-800 hover:text-white font-bold text-xs border border-slate-200 hover:border-brand-primary transition-all">
                        Ver Paquetes Quirúrgicos &rarr;
                    </a>
                </div>
            </div>

            <!-- Diagnóstico por Imágenes -->
            <div class="rounded-3xl bg-white border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl hover:border-brand-primary/30 transition-all duration-300 flex flex-col group">
                <div class="relative h-52 overflow-hidden">
                    <img src="{{ asset('images/surhospital/imagenes-diagnosticas.jpg') }}" alt="Diagnóstico por Imágenes SurHospital" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-indigo-600 text-white shadow-md">
                            TAC & Resonancia
                        </span>
                    </div>
                </div>
                <div class="p-6 sm:p-7 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-brand-primary transition-colors mb-2">Diagnóstico por Imágenes</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-4">
                            Estudios radiológicos de alta resolución con entrega de informes digitales rápidos y confiables para diagnósticos acertados.
                        </p>
                        <ul class="space-y-2 text-xs text-slate-600 font-medium mb-6">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                                <span>Tomografía Computarizada Multicorte (TAC)</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                                <span>Resonancia Magnética Nuclear (RM)</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span>
                                <span>Rayos X Digital y Ecografía Doppler 4D</span>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('booking') }}" class="inline-flex items-center justify-center w-full py-3 rounded-xl bg-slate-50 hover:bg-brand-primary text-slate-800 hover:text-white font-bold text-xs border border-slate-200 hover:border-brand-primary transition-all">
                        Agendar Estudio de Imagen &rarr;
                    </a>
                </div>
            </div>

            <!-- Laboratorio Clínico 24h -->
            <div class="rounded-3xl bg-white border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl hover:border-brand-primary/30 transition-all duration-300 flex flex-col group">
                <div class="relative h-52 overflow-hidden">
                    <img src="{{ asset('images/surhospital/laboratorio-24h.jpg') }}" alt="Laboratorio Clínico 24 Horas SurHospital" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-emerald-600 text-white shadow-md">
                            24 Horas / 365 Días
                        </span>
                    </div>
                </div>
                <div class="p-6 sm:p-7 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-brand-primary transition-colors mb-2">Laboratorio Clínico 24/7</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-4">
                            Servicio de laboratorio de alta, mediana y baja complejidad con procesamiento automatizado y entrega de resultados en línea.
                        </p>
                        <ul class="space-y-2 text-xs text-slate-600 font-medium mb-6">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Hematología, bioquímica y coagulación</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Inmunología, hormonas y marcadores tumorales</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                <span>Pruebas de urgencia las 24 horas</span>
                            </li>
                        </ul>
                    </div>
                    <a href="tel:042590670" class="inline-flex items-center justify-center w-full py-3 rounded-xl bg-slate-50 hover:bg-emerald-600 text-slate-800 hover:text-white font-bold text-xs border border-slate-200 hover:border-emerald-600 transition-all">
                        Consultar Pruebas (PBX 04 259 0670) &rarr;
                    </a>
                </div>
            </div>

            <!-- Emergencias 24/7 -->
            <div class="rounded-3xl bg-white border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl hover:border-brand-primary/30 transition-all duration-300 flex flex-col group">
                <div class="relative h-52 overflow-hidden">
                    <img src="{{ asset('images/surhospital/emergencias-24h.jpg') }}" alt="Área de Emergencias SurHospital" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-rose-600 text-white shadow-md">
                            Emergencias Inmediatas
                        </span>
                    </div>
                </div>
                <div class="p-6 sm:p-7 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-brand-primary transition-colors mb-2">Emergencias 24/7</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-4">
                            Área de triaje y atención médica de urgencia continua los 365 días del año con médicos especialistas y quirófanos disponibles de inmediato.
                        </p>
                        <ul class="space-y-2 text-xs text-slate-600 font-medium mb-6">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                <span>Atención de emergencias médicas y quirúrgicas</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                <span>Box de reanimación y estabilización crítica</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                <span>Acceso directo por José Mascote 2123</span>
                            </li>
                        </ul>
                    </div>
                    <a href="tel:042590670" class="inline-flex items-center justify-center w-full py-3 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white font-bold text-xs border border-rose-200 hover:border-rose-600 transition-all">
                        Llamar a Emergencias: 04 259 0670 &rarr;
                    </a>
                </div>
            </div>

            <!-- Hospitalización & Cuidados Críticos -->
            <div class="rounded-3xl bg-white border border-slate-200 overflow-hidden shadow-sm hover:shadow-xl hover:border-brand-primary/30 transition-all duration-300 flex flex-col group">
                <div class="relative h-52 overflow-hidden bg-slate-900">
                    <img src="{{ asset('images/surhospital/hero-clinic.jpg') }}" alt="Hospitalización SurHospital" class="w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1 rounded-full text-[11px] font-extrabold bg-teal-600 text-white shadow-md">
                            Confort & Monitoreo
                        </span>
                    </div>
                </div>
                <div class="p-6 sm:p-7 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-brand-primary transition-colors mb-2">Hospitalización & UCI</h3>
                        <p class="text-sm text-slate-600 leading-relaxed mb-4">
                            Habitaciones privadas y compartidas diseñadas para una recuperación confortable y segura con personal de enfermería permanente.
                        </p>
                        <ul class="space-y-2 text-xs text-slate-600 font-medium mb-6">
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                <span>Habitaciones con climatización y baño privado</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                <span>Unidad de Cuidados Intensivos e Intermedios</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                <span>Atención nutricional y farmacia interna</span>
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('booking') }}" class="inline-flex items-center justify-center w-full py-3 rounded-xl bg-slate-50 hover:bg-brand-primary text-slate-800 hover:text-white font-bold text-xs border border-slate-200 hover:border-brand-primary transition-all">
                        Consultar Admisiones &rarr;
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- SECCIÓN: ESPECIALIDADES MÉDICAS (surhospital.net) -->
<section id="especialidades" class="py-20 bg-slate-50 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary border border-brand-primary/10">
                Consulta Externa
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-4 tracking-tight">
                Especialidades Médicas
            </h2>
            <p class="text-slate-600 mt-3 text-base">
                Nuestro staff de especialistas atiende de lunes a sábado previa cita o consulta presencial en Guayaquil.
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            
            <a href="{{ route('booking') }}" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-brand-primary hover:shadow-lg transition-all text-center group">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 mx-auto flex items-center justify-center font-bold mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                </div>
                <h4 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-brand-primary">Cardioangiología</h4>
                <p class="text-xs text-slate-500 mt-1">Intervencionista & Clínica</p>
            </a>

            <a href="{{ route('booking') }}" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-brand-primary hover:shadow-lg transition-all text-center group">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 mx-auto flex items-center justify-center font-bold mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                </div>
                <h4 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-brand-primary">Neurocirugía</h4>
                <p class="text-xs text-slate-500 mt-1">Cerebro y Columna</p>
            </a>

            <a href="{{ route('booking') }}" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-brand-primary hover:shadow-lg transition-all text-center group">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 mx-auto flex items-center justify-center font-bold mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                </div>
                <h4 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-brand-primary">Cirugía Digestiva</h4>
                <p class="text-xs text-slate-500 mt-1">Laparoscopía & Bariátrica</p>
            </a>

            <a href="{{ route('booking') }}" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-brand-primary hover:shadow-lg transition-all text-center group">
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 mx-auto flex items-center justify-center font-bold mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </div>
                <h4 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-brand-primary">Gastroenterología</h4>
                <p class="text-xs text-slate-500 mt-1">Endoscopía & Colonoscopía</p>
            </a>

            <a href="{{ route('booking') }}" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-brand-primary hover:shadow-lg transition-all text-center group">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center font-bold mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <h4 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-brand-primary">Urología & Litotricia</h4>
                <p class="text-xs text-slate-500 mt-1">Cálculos & Próstata</p>
            </a>

            <a href="{{ route('booking') }}" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-brand-primary hover:shadow-lg transition-all text-center group">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 mx-auto flex items-center justify-center font-bold mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h4 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-brand-primary">Nefrología</h4>
                <p class="text-xs text-slate-500 mt-1">Salud Renal & Diálisis</p>
            </a>

            <a href="{{ route('booking') }}" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-brand-primary hover:shadow-lg transition-all text-center group">
                <div class="w-12 h-12 rounded-2xl bg-pink-50 text-pink-600 mx-auto flex items-center justify-center font-bold mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                </div>
                <h4 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-brand-primary">Ginecología & Obst.</h4>
                <p class="text-xs text-slate-500 mt-1">Control Prenatal & Partos</p>
            </a>

            <a href="{{ route('booking') }}" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-brand-primary hover:shadow-lg transition-all text-center group">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 mx-auto flex items-center justify-center font-bold mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h4 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-brand-primary">Pediatría</h4>
                <p class="text-xs text-slate-500 mt-1">Atención Infantil Integral</p>
            </a>

            <a href="{{ route('booking') }}" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-brand-primary hover:shadow-lg transition-all text-center group">
                <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-600 mx-auto flex items-center justify-center font-bold mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <h4 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-brand-primary">Traumatología</h4>
                <p class="text-xs text-slate-500 mt-1">Ortopedia & Fracturas</p>
            </a>

            <a href="{{ route('booking') }}" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-brand-primary hover:shadow-lg transition-all text-center group">
                <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 mx-auto flex items-center justify-center font-bold mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z" /></svg>
                </div>
                <h4 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-brand-primary">Otorrinolaringología</h4>
                <p class="text-xs text-slate-500 mt-1">Oído, Nariz y Garganta</p>
            </a>

            <a href="{{ route('booking') }}" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-brand-primary hover:shadow-lg transition-all text-center group">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 mx-auto flex items-center justify-center font-bold mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                </div>
                <h4 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-brand-primary">Medicina Interna</h4>
                <p class="text-xs text-slate-500 mt-1">Diagnóstico Integral Adultos</p>
            </a>

            <a href="{{ route('booking') }}" class="p-6 rounded-2xl bg-white border border-slate-200 hover:border-brand-primary hover:shadow-lg transition-all text-center group">
                <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-600 mx-auto flex items-center justify-center font-bold mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h4 class="font-bold text-slate-900 text-sm sm:text-base group-hover:text-brand-primary">Cuidados Críticos</h4>
                <p class="text-xs text-slate-500 mt-1">Terapia Intensiva & Anestesia</p>
            </a>

        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('booking') }}" class="inline-flex items-center space-x-2 px-8 py-4 rounded-2xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-sm shadow-lg shadow-brand-primary/25 transition-all">
                <span>Seleccionar Especialidad y Agendar Cita</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>
</section>

<!-- SECCIÓN: STAFF MÉDICO DESTACADO -->
<section id="medicos" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary border border-brand-primary/10">
                Staff Médico
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-4 tracking-tight">
                Nuestros Médicos Especialistas
            </h2>
            <p class="text-slate-600 mt-3 text-base">
                Profesionales certificados con amplia trayectoria y vocación de servicio para el cuidado de tu salud.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Doctor 1 -->
            <div class="rounded-3xl bg-slate-50 border border-slate-200 overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 group flex flex-col justify-between">
                <div>
                    <div class="relative h-72 overflow-hidden bg-slate-200">
                        <img src="{{ asset('images/surhospital/doctor-vargas.jpg') }}" alt="Dr. Alberto Vargas Cardiología SurHospital" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute bottom-3 left-3 bg-white/95 backdrop-blur-md px-3 py-1 rounded-xl text-xs font-bold text-brand-primary border border-slate-200">
                            Cardioangiología Intervencionista
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Dr. Alberto Vargas</h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Especialista en Hemodinamia y Cateterismo</p>
                        <p class="text-xs text-slate-600 mt-3 line-clamp-2">
                            Más de 15 años de experiencia en tratamiento de afecciones coronarias y procedimientos endovasculares en Guayaquil.
                        </p>
                    </div>
                </div>
                <div class="px-6 pb-6">
                    <a href="{{ route('booking') }}" class="block w-full text-center py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs transition-all shadow-xs">
                        Agendar con Dr. Vargas
                    </a>
                </div>
            </div>

            <!-- Doctor 2 -->
            <div class="rounded-3xl bg-slate-50 border border-slate-200 overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 group flex flex-col justify-between">
                <div>
                    <div class="relative h-72 overflow-hidden bg-slate-200">
                        <img src="{{ asset('images/surhospital/doctor-ramirez.jpg') }}" alt="Dra. Elena Ramírez Neurocirugía SurHospital" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute bottom-3 left-3 bg-white/95 backdrop-blur-md px-3 py-1 rounded-xl text-xs font-bold text-indigo-700 border border-slate-200">
                            Neurocirugía & Columna
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Dra. Elena Ramírez</h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Especialista en Neurocirugía de Alta Complejidad</p>
                        <p class="text-xs text-slate-600 mt-3 line-clamp-2">
                            Experta en microcirugía craneal, patologías de la columna vertebral y neurotrauma con enfoque mínimamente invasivo.
                        </p>
                    </div>
                </div>
                <div class="px-6 pb-6">
                    <a href="{{ route('booking') }}" class="block w-full text-center py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs transition-all shadow-xs">
                        Agendar con Dra. Ramírez
                    </a>
                </div>
            </div>

            <!-- Doctor 3 / Staff General -->
            <div class="rounded-3xl bg-slate-50 border border-slate-200 overflow-hidden shadow-xs hover:shadow-xl transition-all duration-300 group flex flex-col justify-between">
                <div>
                    <div class="relative h-72 overflow-hidden bg-slate-200">
                        <img src="{{ asset('images/surhospital/docencia-medica.jpg') }}" alt="Staff Quirúrgico SurHospital" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute bottom-3 left-3 bg-white/95 backdrop-blur-md px-3 py-1 rounded-xl text-xs font-bold text-emerald-700 border border-slate-200">
                            Equipo Quirúrgico Multidisciplinario
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-slate-900">Staff Quirúrgico & Consulta</h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Cirugía General, Gastroenterología y Urología</p>
                        <p class="text-xs text-slate-600 mt-3 line-clamp-2">
                            Equipo multidisciplinario disponible para valoraciones prequirúrgicas, endoscopías y seguimiento postoperatorio continuo.
                        </p>
                    </div>
                </div>
                <div class="px-6 pb-6">
                    <a href="{{ route('booking') }}" class="block w-full text-center py-2.5 rounded-xl bg-slate-900 hover:bg-brand-primary text-white font-bold text-xs transition-all shadow-xs">
                        Ver Directorio Completo
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- SECCIÓN: PAQUETES QUIRÚRGICOS DESTACADOS -->
<section id="paquetes" class="py-20 bg-slate-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-primary/30 text-brand-soft border border-brand-primary/40">
                Cirugías Programadas
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-white mt-4 tracking-tight">
                Paquetes Quirúrgicos Integrales
            </h2>
            <p class="text-slate-400 mt-3 text-base">
                Procedimientos con quirófano, honorarios médicos, medicamentos base y estancia en habitación privada con tarifas transparentes.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <div class="rounded-3xl bg-slate-800/80 border border-slate-700 p-8 flex flex-col justify-between hover:border-brand-primary transition-all">
                <div>
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-brand-soft text-brand-primary">Laparoscopía</span>
                    <h3 class="text-xl font-bold text-white mt-4">Apendicectomía Laparoscópica</h3>
                    <p class="text-xs text-slate-400 mt-2">Cirugía mínimamente invasiva con rápida recuperación e incisiones milimétricas.</p>
                    <ul class="space-y-2 text-xs text-slate-300 mt-6">
                        <li class="flex items-center gap-2">✓ Quirófano equipado con torre HD</li>
                        <li class="flex items-center gap-2">✓ Honorarios cirujano y anestesiólogo</li>
                        <li class="flex items-center gap-2">✓ 1 día de hospitalización y medicación</li>
                    </ul>
                </div>
                <div class="mt-8 pt-6 border-t border-slate-700">
                    <a href="{{ route('booking') }}" class="block text-center py-3 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs transition-colors">
                        Solicitar Valoración Quirúrgica
                    </a>
                </div>
            </div>

            <div class="rounded-3xl bg-gradient-to-b from-slate-800 to-slate-800/90 border-2 border-brand-primary p-8 flex flex-col justify-between shadow-2xl relative">
                <div class="absolute -top-3.5 right-8 bg-brand-primary text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-md">
                    Más Solicitado
                </div>
                <div>
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-500/20 text-emerald-300">Vesícula Biliar</span>
                    <h3 class="text-xl font-bold text-white mt-4">Colecistectomía Laparoscópica</h3>
                    <p class="text-xs text-slate-400 mt-2">Extracción segura de vesícula con tecnología laparoscópica de alta definición.</p>
                    <ul class="space-y-2 text-xs text-slate-300 mt-6">
                        <li class="flex items-center gap-2">✓ Equipo quirúrgico especializado</li>
                        <li class="flex items-center gap-2">✓ Quirófano inteligente y recuperación</li>
                        <li class="flex items-center gap-2">✓ Insumos laparoscópicos completos</li>
                    </ul>
                </div>
                <div class="mt-8 pt-6 border-t border-slate-700">
                    <a href="{{ route('booking') }}" class="block text-center py-3 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs transition-colors shadow-lg">
                        Solicitar Valoración Quirúrgica
                    </a>
                </div>
            </div>

            <div class="rounded-3xl bg-slate-800/80 border border-slate-700 p-8 flex flex-col justify-between hover:border-brand-primary transition-all">
                <div>
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-pink-500/20 text-pink-300">Maternidad</span>
                    <h3 class="text-xl font-bold text-white mt-4">Cesárea / Parto Humanizado</h3>
                    <p class="text-xs text-slate-400 mt-2">Atención obstétrica y neonatal cálida, segura y con apego precoz.</p>
                    <ul class="space-y-2 text-xs text-slate-300 mt-6">
                        <li class="flex items-center gap-2">✓ Ginecólogo, pediatra y anestesiólogo</li>
                        <li class="flex items-center gap-2">✓ Suite de hospitalización para madre y bebé</li>
                        <li class="flex items-center gap-2">✓ Monitoreo fetal continuo y termo-cuna</li>
                    </ul>
                </div>
                <div class="mt-8 pt-6 border-t border-slate-700">
                    <a href="{{ route('booking') }}" class="block text-center py-3 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs transition-colors">
                        Solicitar Valoración Quirúrgica
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- SECCIÓN: DOCENCIA MÉDICA E INVESTIGACIÓN -->
<section id="docencia" class="py-20 bg-slate-50 border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="text-center max-w-3xl mx-auto mb-14">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary border border-brand-primary/20">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                Academia & Desarrollo Científico
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 mt-4 tracking-tight">
                Docencia Médica e Investigación Clínica
            </h2>
            <p class="text-slate-600 mt-3 text-sm sm:text-base leading-relaxed">
                SurHospital es un centro formador de excelencia acreditado para la capacitación continua de especialistas de la salud, prácticas hospitalarias tuteladas y desarrollo de investigación biomédica.
            </p>
        </div>

        <!-- 4 Pillars Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Card 1: Internado Rotativo -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:shadow-xl hover:border-brand-primary transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold text-2xl mb-5 group-hover:scale-110 transition-transform">
                        🎓
                    </div>
                    <h3 class="font-bold text-slate-900 text-lg">Internado & Posgrados</h3>
                    <p class="text-xs text-slate-600 mt-2.5 leading-relaxed">
                        Prácticas hospitalarias tuteladas en áreas críticas, cirugía laparoscópica, cardiología y medicina interna con rotaciones clínicas supervisadas.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center text-xs font-bold text-brand-primary">
                    <span>Rotaciones Hospitalarias</span>
                </div>
            </div>

            <!-- Card 2: Bioética e Investigación -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:shadow-xl hover:border-brand-primary transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold text-2xl mb-5 group-hover:scale-110 transition-transform">
                        🔬
                    </div>
                    <h3 class="font-bold text-slate-900 text-lg">Comité de Bioética</h3>
                    <p class="text-xs text-slate-600 mt-2.5 leading-relaxed">
                        Evaluación y conducción de estudios clínicos, ensayos biomédicos y publicaciones indexadas con estrictos estándares de ética médica.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center text-xs font-bold text-brand-primary">
                    <span>Investigación Clínica</span>
                </div>
            </div>

            <!-- Card 3: Educación Continua -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs hover:shadow-xl hover:border-brand-primary transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold text-2xl mb-5 group-hover:scale-110 transition-transform">
                        📚
                    </div>
                    <h3 class="font-bold text-slate-900 text-lg">Educación Continua</h3>
                    <p class="text-xs text-slate-600 mt-2.5 leading-relaxed">
                        Simposios médicos, talleres de simulación laparoscópica, cursos de reanimación avanzada y conferencias magistrales internacionales.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center text-xs font-bold text-brand-primary">
                    <span>Simposios & Talleres</span>
                </div>
            </div>

            <!-- Card 4: Convenios Universitarios (Featured) -->
            <div class="bg-gradient-to-br from-brand-primary to-brand-primary-dark text-white rounded-3xl p-6 shadow-lg flex flex-col justify-between group">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-white/20 text-white flex items-center justify-center font-bold text-2xl mb-5 group-hover:scale-110 transition-transform backdrop-blur-sm">
                        🏛️
                    </div>
                    <h3 class="font-bold text-white text-lg">Convenios Universitarios</h3>
                    <p class="text-xs text-brand-soft/90 mt-2.5 leading-relaxed">
                        Alianzas estratégicas con las principales facultades de medicina y enfermería del Ecuador acreditadas por el MSP y CES.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-white/20 flex items-center justify-between text-xs font-bold text-white">
                    <span>Dirección Académica</span>
                    <span>&rarr;</span>
                </div>
            </div>

        </div>

        <!-- Stats Bar -->
        <div class="mt-10 p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/80 shadow-xs grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
            <div class="border-b sm:border-b-0 sm:border-r border-slate-100 pb-4 sm:pb-0">
                <div class="text-3xl sm:text-4xl font-black text-brand-primary">+15 Años</div>
                <div class="text-xs sm:text-sm text-slate-600 font-semibold mt-1">Formando profesionales de la salud</div>
            </div>
            <div class="border-b sm:border-b-0 sm:border-r border-slate-100 pb-4 sm:pb-0">
                <div class="text-3xl sm:text-4xl font-black text-brand-primary">100%</div>
                <div class="text-xs sm:text-sm text-slate-600 font-semibold mt-1">Convenios universitarios y MSP vigentes</div>
            </div>
            <div>
                <div class="text-3xl sm:text-4xl font-black text-brand-primary">+50 Cursos</div>
                <div class="text-xs sm:text-sm text-slate-600 font-semibold mt-1">Talleres y simposios anuales</div>
            </div>
        </div>

    </div>
</section>

<!-- SECCIÓN: CONTACTO & UBICACIÓN EXACTA (José Mascote 2123) -->
<section id="contacto" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Contact Details -->
            <div class="lg:col-span-6 space-y-6">
                <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-brand-soft text-brand-primary border border-brand-primary/10">
                    Ubicación & Contacto
                </span>
                
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    Estamos en el corazón de Guayaquil
                </h2>

                <p class="text-slate-600 text-base leading-relaxed">
                    Visítanos en nuestras instalaciones principales. Contamos con acceso rápido para emergencias, parqueo y atención 24 horas.
                </p>

                <div class="space-y-4 pt-2">
                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Dirección Principal</h4>
                            <p class="text-xs text-slate-600 mt-0.5">{{ config('branding.contact.address') }}</p>
                            <span class="text-[11px] text-slate-400 font-medium">Guayaquil, Ecuador</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Central Telefónica PBX</h4>
                            <p class="text-xs text-slate-600 mt-0.5">PBX: {{ config('branding.contact.phone') }} (Ext. 100 / Ext. 0)</p>
                            <span class="text-[11px] text-emerald-600 font-semibold">WhatsApp Citas: {{ config('branding.contact.whatsapp_display') }}</span>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                        <div class="w-10 h-10 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900 text-sm">Correos Electrónicos</h4>
                            <p class="text-xs text-slate-600 mt-0.5">Información general: <span class="font-medium text-brand-primary">{{ config('branding.contact.email') }}</span></p>
                            <p class="text-xs text-slate-600">Trabaja con nosotros: <span class="font-medium text-slate-700">{{ config('branding.contact.careers_email') }}</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Action Box -->
            <div class="lg:col-span-6">
                <div class="rounded-3xl bg-gradient-to-br from-brand-primary to-brand-primary-dark p-8 sm:p-10 text-white shadow-2xl relative overflow-hidden">
                    <div class="relative z-10 space-y-6">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-md">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Sistema de Citas Online
                        </span>
                        
                        <h3 class="text-2xl sm:text-3xl font-black leading-tight text-white">
                            Agenda tu consulta médica en menos de 2 minutos.
                        </h3>
                        
                        <p class="text-sm text-brand-soft/90 leading-relaxed">
                            Selecciona especialidad, médico, fecha y hora de tu preferencia. Recibe confirmación inmediata por WhatsApp y correo electrónico.
                        </p>

                        <div class="pt-2">
                            <a href="{{ route('booking') }}" class="inline-flex items-center justify-center w-full py-4 rounded-2xl bg-white hover:bg-slate-50 text-brand-primary font-black text-base shadow-xl transition-all transform hover:-translate-y-0.5">
                                Iniciar Agendamiento Ahora &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
