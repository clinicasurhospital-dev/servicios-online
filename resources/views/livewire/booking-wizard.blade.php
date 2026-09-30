<div class="w-full">
    <!-- Stepper Header (5 Pasos Optimizados) -->
    <div class="mb-6 sm:mb-8 px-2">
        <div class="flex items-center justify-between relative max-w-2xl mx-auto">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-slate-200 w-full z-0 rounded-full"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-brand-primary transition-all duration-500 z-0 rounded-full" style="width: {{ (($step - 1) / 4) * 100 }}%"></div>

            @php
                $steps = [
                    1 => 'Especialidad & Servicio',
                    2 => 'Médico & Horario',
                    3 => 'Datos del Paciente',
                    4 => 'Método de Pago',
                    5 => 'Confirmación',
                ];
            @endphp

            @foreach($steps as $num => $title)
                <div class="relative z-10 flex flex-col items-center">
                    @if($step > $num)
                        <button type="button" wire:click="goToStep({{ $num }})" title="Volver al paso {{ $num }}: {{ $title }}" class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-bold text-xs sm:text-sm transition-all duration-300 bg-brand-primary text-white shadow-md shadow-brand-primary/30 ring-4 ring-white hover:scale-110 cursor-pointer">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </button>
                    @else
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center font-bold text-xs sm:text-sm transition-all duration-300 {{ $step === $num ? 'bg-brand-primary text-white shadow-md shadow-brand-primary/30 ring-4 ring-white scale-105' : 'bg-slate-200 text-slate-500 ring-4 ring-white' }}">
                            {{ $num }}
                        </div>
                    @endif
                    <span class="hidden md:block text-[11px] font-semibold mt-2 text-center max-w-[90px] leading-tight {{ $step >= $num ? 'text-brand-primary font-bold' : 'text-slate-400' }}">{{ $title }}</span>
                </div>
            @endforeach
        </div>
        <div class="text-center md:hidden mt-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-soft text-brand-primary text-xs font-black uppercase tracking-wider">
                Paso {{ $step }} de 5: {{ $steps[$step] ?? '' }}
            </span>
        </div>
    </div>

    <!-- Error Message Alert -->
    @if($errorMessage)
        <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 flex items-start space-x-3 text-rose-800 animate-fadeIn shadow-xs">
            <svg class="w-5 h-5 mt-0.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="text-xs sm:text-sm font-semibold flex-1">{{ $errorMessage }}</div>
        </div>
    @endif

    <!-- Wizard Main Container -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- ========================================== -->
        <!-- PASO 1: ESPECIALIDAD Y TIPO DE ATENCIÓN     -->
        <!-- ========================================== -->
        @if($step == 1)
            <div class="p-5 sm:p-8 animate-fadeIn">
                <!-- Top Header & Action Bar -->
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-extrabold uppercase tracking-wider text-brand-primary bg-brand-soft px-3 py-1 rounded-md">Paso 1 de 5</span>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-1.5">Selecciona Especialidad y Servicio</h2>
                        <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Elige el área médica requerida y el tipo de consulta o procedimiento.</p>
                    </div>

                    <!-- Top Action Bar (Always Blue with Hover) -->
                    <div class="flex items-center gap-3 self-end sm:self-center shrink-0">
                        @if($selectedSpecialty)
                            <div class="text-right">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Total Estimado</span>
                                <span class="text-base font-black text-brand-primary">${{ number_format($amount, 0) }}</span>
                            </div>
                        @endif
                        <button type="button" wire:click="proceedToStep2" class="px-6 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs sm:text-sm shadow-md shadow-brand-primary/25 hover:shadow-brand-primary/40 transition-all cursor-pointer flex items-center gap-2 transform active:scale-95">
                            <span>Continuar</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </button>
                    </div>
                </div>

                <!-- Specialties Grid with Radio Select Indicator -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4 mb-8">
                    @forelse($specialties as $specialty)
                        <button type="button" wire:key="specialty-{{ $specialty->id }}" wire:click="selectSpecialty({{ $specialty->id }})" wire:loading.class="opacity-70 animate-pulse" wire:target="selectSpecialty({{ $specialty->id }})" class="p-4 sm:p-5 rounded-2xl border-2 text-left transition-all duration-200 flex flex-col justify-between hover:border-brand-primary hover:shadow-lg hover:shadow-brand-primary/10 group cursor-pointer relative {{ $specialtyId == $specialty->id ? 'border-brand-primary bg-brand-soft/60 shadow-md ring-2 ring-brand-primary/30' : 'border-slate-200 bg-white hover:bg-slate-50/60' }}">
                            <div>
                                <div class="flex items-start justify-between mb-3">
                                    <!-- Medical Specialty Clinic Icon (No Heart) -->
                                    <div class="w-11 h-11 rounded-xl shadow-xs border flex items-center justify-center transition-all shrink-0 {{ $specialtyId == $specialty->id ? 'bg-brand-primary text-white border-brand-primary shadow-brand-primary/20' : 'bg-white text-brand-primary border-slate-100 group-hover:scale-105 group-hover:bg-brand-soft' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    </div>
                                    <!-- Radio Option Indicator & Selected Badge -->
                                    <div class="flex items-center gap-2">
                                        @if($specialtyId == $specialty->id)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-brand-primary bg-white px-2 py-0.5 rounded-md border border-brand-primary/25 shadow-xs">
                                                ✓ Seleccionado
                                            </span>
                                        @endif
                                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center shrink-0 transition-all {{ $specialtyId == $specialty->id ? 'border-brand-primary bg-brand-primary ring-4 ring-brand-primary/20 shadow-xs' : 'border-slate-300 bg-white group-hover:border-slate-400' }}">
                                            @if($specialtyId == $specialty->id)
                                                <span class="w-2.5 h-2.5 rounded-full bg-white"></span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <h3 class="font-bold {{ $specialtyId == $specialty->id ? 'text-brand-primary font-black' : 'text-slate-900 group-hover:text-brand-primary' }} transition-colors text-sm sm:text-base leading-snug">{{ $specialty->name }}</h3>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ $specialty->description ?: 'Atención especializada en Clínica Sur Hospital.' }}</p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs">
                                <span class="text-slate-500 font-medium">{{ $specialty->doctors_count }} especialista{{ $specialty->doctors_count === 1 ? '' : 's' }}</span>
                                <span class="font-extrabold text-brand-primary text-sm sm:text-base">${{ number_format($specialty->base_price, 0) }}</span>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full text-center py-12 text-slate-400">No hay especialidades médicas disponibles en este momento.</div>
                    @endforelse
                </div>

                <!-- Procedure / Consultation Selection if Specialty Selected -->
                @if($selectedSpecialty)
                    <div class="mt-6 pt-6 border-t border-slate-100 animate-fadeIn">
                        <div class="mb-4">
                            <h3 class="text-sm sm:text-base font-bold text-slate-900">Tipo de Consulta para <span class="text-brand-primary">{{ $selectedSpecialty->name }}</span>:</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Selecciona consulta médica regular o un procedimiento específico si aplica.</p>
                        </div>

                        <div class="space-y-2.5">
                            <!-- Base consultation option with Radio Indicator -->
                            <button type="button" wire:click="selectProcedure(0)" wire:loading.class="opacity-70 animate-pulse" wire:target="selectProcedure(0)" class="w-full p-4 rounded-2xl border-2 text-left transition-all duration-200 flex items-center justify-between cursor-pointer group {{ ($procedureId == 0 || is_null($procedureId)) ? 'border-brand-primary bg-brand-soft/60 ring-2 ring-brand-primary/20 shadow-xs' : 'border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300' }}">
                                <div class="flex items-center gap-3.5">
                                    <!-- Radio Option Indicator -->
                                    <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center shrink-0 transition-all {{ ($procedureId == 0 || is_null($procedureId)) ? 'border-brand-primary bg-brand-primary ring-4 ring-brand-primary/20 shadow-xs' : 'border-slate-300 bg-white group-hover:border-slate-400' }}">
                                        @if($procedureId == 0 || is_null($procedureId))
                                            <span class="w-2.5 h-2.5 rounded-full bg-white"></span>
                                        @endif
                                    </div>
                                    <div class="w-9 h-9 rounded-xl shadow-xs border flex items-center justify-center shrink-0 {{ ($procedureId == 0 || is_null($procedureId)) ? 'bg-brand-primary text-white border-brand-primary' : 'bg-white text-brand-primary border-slate-100' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">Consulta Médica Especializada (General)</div>
                                        <div class="text-xs text-slate-500">Valoración y diagnóstico clínico ({{ $selectedSpecialty->default_duration_minutes ?? 30 }} min)</div>
                                    </div>
                                </div>
                                <div class="text-right flex items-center gap-3">
                                    @if($procedureId == 0 || is_null($procedureId))
                                        <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-brand-primary bg-white px-2 py-0.5 rounded-md border border-brand-primary/20 shadow-xs">✓ Seleccionado</span>
                                    @endif
                                    <div>
                                        <span class="block font-black text-brand-primary text-base">${{ number_format($selectedSpecialty->base_price, 0) }}</span>
                                        <span class="text-[10px] text-slate-400 uppercase font-bold">Tarifa Base</span>
                                    </div>
                                </div>
                            </button>

                            <!-- Specific procedures with Radio Indicators -->
                            @foreach($procedures as $proc)
                                <button type="button" wire:key="proc-{{ $proc->id }}" wire:click="selectProcedure({{ $proc->id }})" wire:loading.class="opacity-70 animate-pulse" wire:target="selectProcedure({{ $proc->id }})" class="w-full p-4 rounded-2xl border-2 text-left transition-all duration-200 flex items-center justify-between cursor-pointer group {{ $procedureId == $proc->id ? 'border-brand-primary bg-brand-soft/60 ring-2 ring-brand-primary/20 shadow-xs' : 'border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300' }}">
                                    <div class="flex items-center gap-3.5">
                                        <!-- Radio Option Indicator -->
                                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center shrink-0 transition-all {{ $procedureId == $proc->id ? 'border-brand-primary bg-brand-primary ring-4 ring-brand-primary/20 shadow-xs' : 'border-slate-300 bg-white group-hover:border-slate-400' }}">
                                            @if($procedureId == $proc->id)
                                                <span class="w-2.5 h-2.5 rounded-full bg-white"></span>
                                            @endif
                                        </div>
                                        <div class="w-9 h-9 rounded-xl shadow-xs border flex items-center justify-center shrink-0 {{ $procedureId == $proc->id ? 'bg-brand-primary text-white border-brand-primary' : 'bg-white text-brand-primary border-slate-100' }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 text-sm">{{ $proc->name }}</div>
                                            <div class="text-xs text-slate-500">{{ $proc->description ?: 'Estudio / Procedimiento' }} ({{ $proc->duration_minutes }} min)</div>
                                        </div>
                                    </div>
                                    <div class="text-right flex items-center gap-3">
                                        @if($procedureId == $proc->id)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-brand-primary bg-white px-2 py-0.5 rounded-md border border-brand-primary/20 shadow-xs">✓ Seleccionado</span>
                                        @endif
                                        <div>
                                            <span class="block font-black text-brand-primary text-base">${{ number_format($proc->price, 0) }}</span>
                                            <span class="text-[10px] text-slate-400 uppercase font-bold">Procedimiento</span>
                                        </div>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        <!-- Bottom Continue CTA -->
                        <div class="mt-7 flex items-center justify-between pt-5 border-t border-slate-100">
                            <div class="text-xs text-slate-500">
                                Total estimado: <strong class="text-brand-primary font-black text-base">${{ number_format($amount, 0) }}</strong>
                            </div>
                            <button type="button" wire:click="proceedToStep2" class="px-6 py-3 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs sm:text-sm shadow-md shadow-brand-primary/25 hover:shadow-brand-primary/40 transition-all cursor-pointer flex items-center gap-2">
                                <span>Continuar a Especialista y Horario</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- ========================================== -->
        <!-- PASO 2: ESPECIALISTA Y SELECCIÓN DE HORARIO -->
        <!-- ========================================== -->
        @if($step == 2)
            <div class="p-5 sm:p-8 animate-fadeIn">
                <!-- Top Header & Action Bar -->
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-extrabold uppercase tracking-wider text-brand-primary bg-brand-soft px-3 py-1 rounded-md">Paso 2 de 5</span>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-1.5">Elige tu Especialista y Horario</h2>
                        <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Especialidad: <strong class="text-brand-primary">{{ $selectedSpecialty->name }}</strong></p>
                    </div>
                    
                    <div class="flex items-center gap-2.5 self-end sm:self-center shrink-0">
                        <button type="button" wire:click="previousStep" class="text-xs sm:text-sm font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1 px-3 py-2 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            <span>Atrás</span>
                        </button>
                        <button type="button" wire:click="proceedToStep3" @if(!$selectedTime) disabled @endif class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-primary hover:bg-brand-primary-dark transition-all disabled:opacity-40 disabled:cursor-not-allowed shadow-md shadow-brand-primary/20 hover:shadow-brand-primary/35 cursor-pointer flex items-center gap-1.5">
                            <span>Continuar</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </button>
                    </div>
                </div>

                <!-- Doctors Grid with Radio Option Indicator -->
                <div class="mb-6">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-3">1. Selecciona el Médico Tratante:</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                        @forelse($doctors as $doc)
                            <button type="button" wire:key="doctor-{{ $doc->id }}" wire:click="selectDoctor({{ $doc->id }})" class="p-4 sm:p-5 rounded-2xl border-2 text-left transition-all duration-200 flex items-start space-x-3.5 hover:border-brand-primary hover:shadow-md hover:shadow-brand-primary/10 cursor-pointer group relative {{ $doctorId == $doc->id ? 'border-brand-primary bg-brand-soft/40 shadow-sm ring-2 ring-brand-primary/20' : 'border-slate-100 bg-slate-50/60 hover:bg-white' }}">
                                <div class="w-13 h-13 rounded-2xl bg-gradient-to-br from-brand-primary to-brand-primary-dark text-white flex items-center justify-center font-black text-lg shadow-sm shrink-0">
                                    {{ substr($doc->name, 0, 2) }}
                                </div>
                                <div class="flex-1 min-w-0 pr-6">
                                    <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-brand-soft text-brand-primary mb-1">
                                        Reg. {{ $doc->license_number }}
                                    </span>
                                    <h4 class="font-bold {{ $doctorId == $doc->id ? 'text-brand-primary' : 'text-slate-900' }} text-sm sm:text-base leading-tight truncate">{{ $doc->name }}</h4>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ $doc->bio ?: 'Especialista certificado en SurHospital.' }}</p>
                                    <div class="mt-2.5 flex items-center space-x-1.5 text-xs text-emerald-600 font-bold">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Consultorio: {{ $doc->room_office ?: 'Cons. Principal' }}</span>
                                    </div>
                                </div>
                                <!-- Radio Option Indicator & Selected Badge -->
                                <div class="absolute top-4 right-4 flex items-center gap-2">
                                    @if($doctorId == $doc->id)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-brand-primary bg-white px-2 py-0.5 rounded-md border border-brand-primary/20 shadow-xs">
                                            ✓ Seleccionado
                                        </span>
                                    @endif
                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all {{ $doctorId == $doc->id ? 'border-brand-primary bg-brand-primary ring-2 ring-brand-primary/20' : 'border-slate-300 bg-white group-hover:border-slate-400' }}">
                                        @if($doctorId == $doc->id)
                                            <span class="w-2 h-2 rounded-full bg-white"></span>
                                        @endif
                                    </div>
                                </div>
                            </button>
                        @empty
                            <div class="col-span-full text-center py-8 text-slate-400">No hay médicos registrados activos para esta especialidad.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Calendar & Slots Selection (if doctor chosen) -->
                @if($selectedDoctor)
                    <div class="mt-6 pt-6 border-t border-slate-100 animate-fadeIn">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-3">2. Selecciona Fecha y Turno de Atención:</label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <!-- Date picker box -->
                            <div class="md:col-span-1 bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-100">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Fecha de Cita</label>
                                <input type="date" wire:model.live="selectedDate" min="{{ now()->format('Y-m-d') }}" class="w-full rounded-xl border-slate-200 text-slate-800 font-bold focus:border-brand-primary focus:ring-brand-primary text-sm p-3 bg-white shadow-xs" />

                                <div class="mt-4 p-3 rounded-xl bg-blue-50 border border-blue-100 text-xs text-blue-800 space-y-1">
                                    <p class="font-bold flex items-center gap-1 text-brand-primary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Turnos en Tiempo Real</span>
                                    </p>
                                    <p class="text-[11px] text-blue-700">Agenda sincronizada en vivo con la disponibilidad del Dr. {{ $selectedDoctor->name }}.</p>
                                </div>
                            </div>

                            <!-- Slots Grid with Radio Pill Option -->
                            <div class="md:col-span-2">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Turnos Disponibles</span>
                                    <span class="text-xs text-brand-primary font-bold">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d \d\e F') }}</span>
                                </div>

                                @if(count($availableSlots) > 0)
                                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2.5 max-h-64 overflow-y-auto pr-1 custom-scrollbar">
                                        @foreach($availableSlots as $slot)
                                            @if($slot->isAvailable)
                                                <button type="button" wire:key="slot-{{ $slot->startTime }}" wire:click="selectSlot('{{ $slot->startTime }}', '{{ $slot->endTime }}')" class="py-2.5 px-2 rounded-xl text-center font-bold text-xs sm:text-sm transition-all duration-150 cursor-pointer flex items-center justify-center gap-1.5 {{ $selectedTime === $slot->startTime ? 'bg-brand-primary text-white shadow-md shadow-brand-primary/30 ring-2 ring-brand-primary ring-offset-1 scale-105' : 'bg-slate-50 text-slate-700 border border-slate-200/80 hover:border-brand-primary hover:bg-brand-soft/30 hover:text-brand-primary' }}">
                                                    <!-- Mini radio bullet inside slot -->
                                                    <span class="w-2 h-2 rounded-full {{ $selectedTime === $slot->startTime ? 'bg-white' : 'bg-slate-300' }}"></span>
                                                    <span>{{ $slot->startTime }}</span>
                                                </button>
                                            @else
                                                <button disabled wire:key="slot-disabled-{{ $slot->startTime }}" class="py-2.5 px-2 rounded-xl text-center text-xs font-semibold bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed line-through opacity-50">
                                                    {{ $slot->startTime }}
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-10 px-4 rounded-2xl bg-slate-50 border border-dashed border-slate-200">
                                        <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p class="text-xs sm:text-sm font-semibold text-slate-600">No hay turnos disponibles para esta fecha</p>
                                        <p class="text-[11px] text-slate-400 mt-1">El especialista no tiene agenda activa o los cupos están ocupados. Por favor selecciona otro día.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Bottom Continue Button -->
                        <div class="mt-7 pt-5 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                @if($selectedTime)
                                    <span class="text-[11px] text-slate-500">Horario seleccionado:</span>
                                    <span class="block text-sm sm:text-base font-extrabold text-brand-primary">{{ $selectedTime }} - {{ $selectedEndTime }}</span>
                                @endif
                            </div>
                            <button type="button" wire:click="proceedToStep3" @if(!$selectedTime) disabled @endif class="px-6 py-3 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-primary hover:bg-brand-primary-dark transition-all disabled:opacity-40 disabled:cursor-not-allowed shadow-md shadow-brand-primary/20 cursor-pointer flex items-center gap-2">
                                <span>Continuar a Datos del Paciente</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- ========================================== -->
        <!-- PASO 3: DATOS DEL PACIENTE                 -->
        <!-- ========================================== -->
        @if($step === 3)
            <div class="p-5 sm:p-8 animate-fadeIn">
                <!-- Top Header & Action Bar -->
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-extrabold uppercase tracking-wider text-brand-primary bg-brand-soft px-3 py-1 rounded-md">Paso 3 de 5</span>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-1.5">{{ $isManual ? 'Datos del Paciente (Registro Manual)' : 'Datos del Paciente' }}</h2>
                        <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Ingresa la información personal para generar la historia y reserva clínica.</p>
                    </div>

                    <div class="flex items-center gap-2.5 self-end sm:self-center shrink-0">
                        <button type="button" wire:click="previousStep" class="text-xs sm:text-sm font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1 px-3 py-2 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            <span>Atrás</span>
                        </button>
                        <button type="button" wire:click="proceedToStep4" class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-primary hover:bg-brand-primary-dark transition-all shadow-md shadow-brand-primary/20 hover:shadow-brand-primary/35 cursor-pointer flex items-center gap-1.5">
                            <span>Continuar</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </button>
                    </div>
                </div>

                @if(!$isManual && Auth::check() && Auth::user()->patient)
                    <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between mb-6 shadow-xs">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-lg">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div>
                                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Sesión Activa — Paciente Registrado</span>
                                <h4 class="font-bold text-slate-900 text-base">{{ Auth::user()->name }}</h4>
                                <p class="text-xs text-slate-600 font-medium">{{ Auth::user()->email }} • {{ $phone ?: 'WhatsApp configurado' }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Nombres *</label>
                            <input type="text" wire:model="firstName" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50/50 focus:bg-white shadow-xs" placeholder="Ej: Carlos Alfredo" />
                            @error('firstName') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Apellidos *</label>
                            <input type="text" wire:model="lastName" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50/50 focus:bg-white shadow-xs" placeholder="Ej: Mendoza Vera" />
                            @error('lastName') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Tipo de Documento *</label>
                            <select wire:model="identificationType" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50/50 focus:bg-white font-medium shadow-xs">
                                <option value="CEDULA">Cédula de Identidad (Ecuador)</option>
                                <option value="PASAPORTE">Pasaporte Extranjero</option>
                                <option value="RUC">RUC</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Número de Cédula / Documento *</label>
                            <input type="text" wire:model="identificationNumber" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50/50 focus:bg-white font-mono shadow-xs" placeholder="0928374651" />
                            @error('identificationNumber') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Correo Electrónico *</label>
                            <input type="email" wire:model="email" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50/50 focus:bg-white shadow-xs" placeholder="paciente@correo.com" />
                            @error('email') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Teléfono / WhatsApp *</label>
                            <input type="tel" wire:model="phone" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50/50 focus:bg-white shadow-xs" placeholder="0991234567" />
                            @error('phone') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Fecha de Nacimiento *</label>
                            <input type="date" wire:model="birthDate" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50/50 focus:bg-white shadow-xs" />
                            @error('birthDate') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Género *</label>
                            <select wire:model="gender" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50/50 focus:bg-white font-medium shadow-xs">
                                <option value="MALE">Masculino</option>
                                <option value="FEMALE">Femenino</option>
                                <option value="OTHER">Otro / No Especificar</option>
                            </select>
                        </div>
                        @if(!$isManual && !Auth::check())
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Crear Contraseña para tu Portal Paciente *</label>
                                <input type="password" wire:model="password" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50/50 focus:bg-white shadow-xs" placeholder="Mínimo 8 caracteres" />
                                <p class="text-[11px] text-slate-500 mt-1">Podrás iniciar sesión con tu correo y esta clave para ver recetas, certificados y tus citas agendadas.</p>
                                @error('password') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Motivo de Consulta u Observaciones Clínicas (Opcional)</label>
                    <textarea wire:model="reason" rows="2" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50/50 focus:bg-white shadow-xs" placeholder="Describe brevemente tus síntomas o notas para el médico tratante..."></textarea>
                </div>

                <!-- Bottom Continue CTA -->
                <div class="mt-7 pt-5 border-t border-slate-100 flex justify-end">
                    <button type="button" wire:click="proceedToStep4" class="px-7 py-3 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-primary hover:bg-brand-primary-dark transition-all shadow-md shadow-brand-primary/20 cursor-pointer flex items-center gap-2">
                        <span>Continuar al Método de Pago</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- PASO 4: MÉTODO DE PAGO (PASARELA MODO PRUEBA) -->
        <!-- ========================================== -->
        @if($step === 4)
            <div class="p-5 sm:p-8 animate-fadeIn">
                <!-- Top Header & Action Bar -->
                <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                    <div>
                        <span class="text-xs font-extrabold uppercase tracking-wider text-brand-primary bg-brand-soft px-3 py-1 rounded-md">Paso 4 de 5</span>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-1.5">Método de Pago</h2>
                        <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Selecciona la forma de pago para formalizar y garantizar tu turno.</p>
                    </div>

                    <div class="flex items-center gap-2.5 self-end sm:self-center shrink-0">
                        <button type="button" wire:click="previousStep" class="text-xs sm:text-sm font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1 px-3 py-2 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            <span>Atrás</span>
                        </button>
                        <button type="button" wire:click="confirmBooking" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-primary hover:bg-brand-primary-dark transition-all shadow-md shadow-brand-primary/20 hover:shadow-brand-primary/35 cursor-pointer flex items-center gap-1.5">
                            <span wire:loading.remove>Confirmar Cita (${{ number_format($amount, 0) }})</span>
                            <span wire:loading class="flex items-center space-x-1">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Procesando...</span>
                            </span>
                            <svg class="w-4 h-4" wire:loading.remove fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </button>
                    </div>
                </div>

                <!-- Resumen Compacto de la Reserva -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80 mb-6 shadow-xs">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Resumen de la Cita Médica</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 text-xs sm:text-sm">
                        <div>
                            <span class="text-[11px] text-slate-400 block">Especialidad:</span>
                            <span class="font-bold text-slate-800">{{ $selectedSpecialty->name }}</span>
                        </div>
                        <div>
                            <span class="text-[11px] text-slate-400 block">Especialista:</span>
                            <span class="font-bold text-slate-800">{{ $selectedDoctor->user->name }}</span>
                        </div>
                        <div>
                            <span class="text-[11px] text-slate-400 block">Fecha y Hora:</span>
                            <span class="font-bold text-brand-primary">{{ $selectedDate }} | {{ $selectedTime }}</span>
                        </div>
                        <div>
                            <span class="text-[11px] text-slate-400 block">Valor Total:</span>
                            <span class="text-base sm:text-xl font-black text-brand-primary">${{ number_format($amount, 0) }} USD</span>
                        </div>
                    </div>
                </div>

                <!-- Tabs / Opciones de Pago con Radio Indicator -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                    <!-- Opción 1: Tarjeta de Débito/Crédito -->
                    <button type="button" wire:click="$set('paymentMethod', 'CREDIT_CARD')" class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer group relative {{ $paymentMethod === 'CREDIT_CARD' ? 'border-brand-primary bg-brand-soft/50 ring-2 ring-brand-primary/20 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">💳</span>
                            <div class="flex items-center gap-1.5">
                                @if($paymentMethod === 'CREDIT_CARD')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-brand-primary bg-white px-2 py-0.5 rounded-md border border-brand-primary/20 shadow-xs">
                                        ✓ Seleccionado
                                    </span>
                                @endif
                                <!-- Radio Option Indicator -->
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all {{ $paymentMethod === 'CREDIT_CARD' ? 'border-brand-primary bg-brand-primary ring-2 ring-brand-primary/20' : 'border-slate-300 bg-white group-hover:border-slate-400' }}">
                                    @if($paymentMethod === 'CREDIT_CARD')
                                        <span class="w-2 h-2 rounded-full bg-white"></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold {{ $paymentMethod === 'CREDIT_CARD' ? 'text-brand-primary' : 'text-slate-900' }} text-sm">Tarjeta Débito / Crédito</h4>
                        </div>
                        <span class="inline-block text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 mt-1">Inmediato</span>
                        <p class="text-[11px] text-slate-500 mt-1.5">Pasarela segura Visa, Mastercard, Diners.</p>
                    </button>

                    <!-- Opción 2: Generar Link de Pago -->
                    <button type="button" wire:click="$set('paymentMethod', 'PAYMENT_LINK')" class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer group relative {{ $paymentMethod === 'PAYMENT_LINK' ? 'border-brand-primary bg-brand-soft/50 ring-2 ring-brand-primary/20 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-8 h-8 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm">🔗</span>
                            <div class="flex items-center gap-1.5">
                                @if($paymentMethod === 'PAYMENT_LINK')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-brand-primary bg-white px-2 py-0.5 rounded-md border border-brand-primary/20 shadow-xs">
                                        ✓ Seleccionado
                                    </span>
                                @endif
                                <!-- Radio Option Indicator -->
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all {{ $paymentMethod === 'PAYMENT_LINK' ? 'border-brand-primary bg-brand-primary ring-2 ring-brand-primary/20' : 'border-slate-300 bg-white group-hover:border-slate-400' }}">
                                    @if($paymentMethod === 'PAYMENT_LINK')
                                        <span class="w-2 h-2 rounded-full bg-white"></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold {{ $paymentMethod === 'PAYMENT_LINK' ? 'text-brand-primary' : 'text-slate-900' }} text-sm">Generar Link de Pago</h4>
                        </div>
                        <span class="inline-block text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-purple-100 text-purple-800 mt-1">Email / Link</span>
                        <p class="text-[11px] text-slate-500 mt-1.5">Recibe el link en tu correo para pagar con app móvil.</p>
                    </button>

                    <!-- Opción 3: Transferencia Bancaria -->
                    <button type="button" wire:click="$set('paymentMethod', 'BANK_TRANSFER')" class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer group relative {{ ($paymentMethod === 'BANK_TRANSFER' || $paymentMethod === 'TRANSFER') ? 'border-brand-primary bg-brand-soft/50 ring-2 ring-brand-primary/20 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                        <div class="flex items-center justify-between mb-2">
                            <span class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">🏦</span>
                            <div class="flex items-center gap-1.5">
                                @if($paymentMethod === 'BANK_TRANSFER' || $paymentMethod === 'TRANSFER')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-brand-primary bg-white px-2 py-0.5 rounded-md border border-brand-primary/20 shadow-xs">
                                        ✓ Seleccionado
                                    </span>
                                @endif
                                <!-- Radio Option Indicator -->
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all {{ ($paymentMethod === 'BANK_TRANSFER' || $paymentMethod === 'TRANSFER') ? 'border-brand-primary bg-brand-primary ring-2 ring-brand-primary/20' : 'border-slate-300 bg-white group-hover:border-slate-400' }}">
                                    @if($paymentMethod === 'BANK_TRANSFER' || $paymentMethod === 'TRANSFER')
                                        <span class="w-2 h-2 rounded-full bg-white"></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold {{ ($paymentMethod === 'BANK_TRANSFER' || $paymentMethod === 'TRANSFER') ? 'text-brand-primary' : 'text-slate-900' }} text-sm">Transferencia Bancaria</h4>
                        </div>
                        <span class="inline-block text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-amber-100 text-amber-800 mt-1">Banca Móvil</span>
                        <p class="text-[11px] text-slate-500 mt-1.5">Depósito o transferencia con número de comprobante.</p>
                    </button>
                </div>

                <!-- CONTENIDO SEGÚN MÉTODO DE PAGO SELECCIONADO -->

                <!-- 1. FORMULARIO PASARELA TARJETA (MODO PRUEBA) -->
                @if($paymentMethod === 'CREDIT_CARD')
                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-900 text-white shadow-xl animate-fadeIn relative overflow-hidden">
                        <!-- Background glow effect -->
                        <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-brand-primary/30 blur-3xl pointer-events-none"></div>

                        <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-800 relative z-10">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                    Modo Prueba / Gateway Simulator
                                </span>
                            </div>
                            <button type="button" wire:click="fillDemoCard" class="text-xs font-bold text-brand-soft hover:text-white bg-slate-800 hover:bg-slate-700 px-3 py-1.5 rounded-xl border border-slate-700 transition-colors cursor-pointer flex items-center gap-1.5">
                                <span>⚡ Llenar Datos de Prueba</span>
                            </button>
                        </div>

                        <!-- Card preview UI -->
                        <div class="max-w-sm mx-auto mb-6 p-5 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-700 border border-slate-600 shadow-2xl text-white relative">
                            <div class="flex justify-between items-center mb-6">
                                <span class="text-xs font-black tracking-widest text-slate-300 uppercase">SURHOSPITAL PAY</span>
                                <span class="text-lg font-black tracking-wider italic text-amber-400">VISA / MC</span>
                            </div>
                            <div class="font-mono text-base sm:text-lg tracking-widest text-slate-100 mb-4 font-bold">
                                {{ $cardNumber ?: '•••• •••• •••• ••••' }}
                            </div>
                            <div class="flex justify-between items-end text-xs">
                                <div>
                                    <span class="text-[9px] uppercase tracking-wider text-slate-400 block font-bold">Titular</span>
                                    <span class="font-bold tracking-wider text-slate-200 truncate max-w-[180px] block">{{ $cardHolder ?: 'NOMBRE DEL TITULAR' }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[9px] uppercase tracking-wider text-slate-400 block font-bold">Vence</span>
                                    <span class="font-mono font-bold text-slate-200">{{ $cardExpiry ?: 'MM/AA' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card Form Inputs -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 relative z-10 text-slate-900">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-300 mb-1 uppercase tracking-wider">Nombre en la Tarjeta *</label>
                                <input type="text" wire:model="cardHolder" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white text-sm p-3 focus:border-brand-soft focus:ring-brand-soft shadow-xs" placeholder="Ej: CARLOS A MENDOZA" />
                                @error('cardHolder') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-300 mb-1 uppercase tracking-wider">Número de Tarjeta (16 Dígitos) *</label>
                                <input type="text" wire:model="cardNumber" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white text-sm p-3 font-mono focus:border-brand-soft focus:ring-brand-soft shadow-xs" placeholder="4532 8900 1234 5678" />
                                @error('cardNumber') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-300 mb-1 uppercase tracking-wider">Fecha de Expiración *</label>
                                <input type="text" wire:model="cardExpiry" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white text-sm p-3 font-mono focus:border-brand-soft focus:ring-brand-soft shadow-xs" placeholder="MM/AA (Ej: 12/28)" />
                                @error('cardExpiry') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-300 mb-1 uppercase tracking-wider">Código de Seguridad CVV *</label>
                                <input type="password" maxlength="4" wire:model="cardCvv" class="w-full rounded-xl border-slate-700 bg-slate-800 text-white text-sm p-3 font-mono focus:border-brand-soft focus:ring-brand-soft shadow-xs" placeholder="•••" />
                                @error('cardCvv') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between text-[11px] text-slate-400 pt-3 border-t border-slate-800 relative z-10">
                            <span class="flex items-center gap-1">🔒 Encriptación TLS 256-bit y Cumplimiento PCI-DSS</span>
                            <span class="text-emerald-400 font-bold">Simulación 100% segura</span>
                        </div>
                    </div>
                @endif

                <!-- 2. GENERAR LINK DE PAGO (MODO PRUEBA) -->
                @if($paymentMethod === 'PAYMENT_LINK')
                    <div class="p-6 rounded-3xl bg-purple-50/70 border border-purple-200/80 animate-fadeIn">
                        <div class="flex items-start gap-3.5 mb-5">
                            <div class="w-10 h-10 rounded-2xl bg-purple-600 text-white flex items-center justify-center font-bold text-lg shrink-0 shadow-md shadow-purple-600/20">
                                🔗
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-purple-950">Pasarela Digital con Enlace de Pago</h3>
                                <p class="text-xs text-purple-800 mt-0.5">Se generará un link protegido para pagar desde tu teléfono celular o reenviarlo por WhatsApp/correo.</p>
                            </div>
                        </div>

                        @if($paymentLinkSent)
                            <div class="p-4 rounded-2xl bg-white border border-purple-200 shadow-xs space-y-3 mb-4">
                                <div class="flex items-center gap-2 text-xs font-bold text-emerald-700">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                    <span>¡Link de pago generado exitosamente y enviado a <strong>{{ $email ?: 'tu correo' }}</strong>!</span>
                                </div>
                                <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 border border-slate-200 font-mono text-xs text-slate-800 break-all select-all">
                                    <span>{{ $generatedPaymentLink }}</span>
                                </div>
                                <p class="text-[11px] text-slate-500">Haz clic en <strong>Confirmar Cita</strong> para guardar tu turno; el estado quedará registrado pendiente de acreditación.</p>
                            </div>
                        @else
                            <div class="p-5 rounded-2xl bg-white border border-purple-100 text-center space-y-3">
                                <p class="text-xs text-slate-600">Presiona el botón a continuación para generar tu enlace en modo prueba.</p>
                                <button type="button" wire:click="generateDemoPaymentLink" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md transition-colors cursor-pointer inline-flex items-center gap-2">
                                    <span>Generar Enlace de Pago Demo</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- 3. TRANSFERENCIA BANCARIA -->
                @if($paymentMethod === 'BANK_TRANSFER' || $paymentMethod === 'TRANSFER')
                    <div class="p-6 rounded-3xl bg-amber-50/60 border border-amber-200/80 animate-fadeIn space-y-6">
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-2xl bg-amber-600 text-white flex items-center justify-center font-bold text-lg shrink-0 shadow-md shadow-amber-600/20">
                                🏦
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-amber-950">Datos Bancarios para Transferencia</h3>
                                <p class="text-xs text-amber-800 mt-0.5">Realiza la transferencia desde tu banca en línea e ingresa el comprobante o referencia de pago.</p>
                            </div>
                        </div>

                        <!-- Cuentas Bancarias Institucionales -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div class="p-4 rounded-2xl bg-white border border-amber-200 shadow-xs relative overflow-hidden group hover:border-amber-300 transition-colors">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black text-amber-700 uppercase tracking-wider">Banco Pichincha</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-100 text-amber-800">Cta. Corriente</span>
                                </div>
                                <div class="text-sm font-black text-slate-900 mt-1 font-mono">2100849201</div>
                                <div class="text-xs text-slate-600 mt-0.5 font-medium">Titular: SurHospital C.A.</div>
                                <div class="text-xs text-slate-500 font-mono mt-0.5">RUC: 0992384756001</div>
                            </div>
                            <div class="p-4 rounded-2xl bg-white border border-amber-200 shadow-xs relative overflow-hidden group hover:border-blue-300 transition-colors">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-black text-blue-700 uppercase tracking-wider">Banco Guayaquil</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-100 text-blue-800">Cta. Corriente</span>
                                </div>
                                <div class="text-sm font-black text-slate-900 mt-1 font-mono">1004829102</div>
                                <div class="text-xs text-slate-600 mt-0.5 font-medium">Titular: SurHospital C.A.</div>
                                <div class="text-xs text-slate-500 font-mono mt-0.5">RUC: 0992384756001</div>
                            </div>
                        </div>

                        <!-- Datos del Comprobante / Formulario de Transferencia -->
                        <div class="p-5 rounded-2xl bg-white border border-amber-200 shadow-xs space-y-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <span>Registro del Comprobante de Pago</span>
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Banco Emisor *</label>
                                    <select wire:model="transferBank" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-amber-500 focus:ring-amber-500 bg-slate-50 focus:bg-white font-medium shadow-xs">
                                        <option value="Banco Pichincha">Banco Pichincha</option>
                                        <option value="Banco Guayaquil">Banco Guayaquil</option>
                                        <option value="Banco del Pacífico">Banco del Pacífico</option>
                                        <option value="Banco Bolivariano">Banco Bolivariano</option>
                                        <option value="Banco Internacional">Banco Internacional</option>
                                        <option value="Produbanco">Produbanco</option>
                                        <option value="Banco General Rumiñahui">Banco General Rumiñahui</option>
                                        <option value="Cooperativa JEP">Cooperativa JEP</option>
                                        <option value="Cooperativa Policía Nacional">Cooperativa Policía Nacional</option>
                                        <option value="Mutualista Pichincha">Mutualista Pichincha</option>
                                        <option value="Otro Banco / Cooperativa">Otro Banco / Cooperativa</option>
                                    </select>
                                    @error('transferBank') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Nº de Comprobante / Referencia</label>
                                    <input type="text" wire:model="transferReference" class="w-full rounded-xl border-slate-200 text-sm p-3 font-mono focus:border-amber-500 focus:ring-amber-500 bg-slate-50 focus:bg-white shadow-xs" placeholder="Ej: 98347102" />
                                    @error('transferReference') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Fecha de Transferencia</label>
                                    <input type="date" wire:model="transferDate" max="{{ now()->format('Y-m-d') }}" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-amber-500 focus:ring-amber-500 bg-slate-50 focus:bg-white shadow-xs" />
                                    @error('transferDate') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Notas Adicionales (Opcional)</label>
                                    <input type="text" wire:model="transferNotes" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-amber-500 focus:ring-amber-500 bg-slate-50 focus:bg-white shadow-xs" placeholder="Ej: Pago realizado por cuenta de familiar" />
                                </div>
                            </div>

                            <!-- File Upload Zone for Comprobante -->
                            <div class="pt-2">
                                <label class="block text-xs font-bold text-slate-700 mb-2 uppercase tracking-wider">
                                    Adjuntar Comprobante de Pago (Imagen o PDF)
                                </label>

                                @if($uploadedReceiptName)
                                    <!-- Uploaded File Card Preview -->
                                    <div class="p-4 rounded-2xl bg-emerald-50/80 border-2 border-emerald-300 flex items-center justify-between gap-3 animate-fadeIn">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shrink-0 shadow-xs">
                                                @if(str_ends_with(strtolower($uploadedReceiptName), '.pdf'))
                                                    📄
                                                @else
                                                    🖼️
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-slate-900 truncate">{{ $uploadedReceiptName }}</span>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800">
                                                        ✓ Adjunto
                                                    </span>
                                                </div>
                                                <p class="text-[11px] text-slate-500 mt-0.5">Tamaño: {{ $uploadedReceiptSize ?: 'Listo' }} • Archivo validado</p>
                                            </div>
                                        </div>

                                        <button type="button" wire:click="removeReceipt" class="px-3 py-1.5 rounded-xl text-xs font-bold text-rose-700 hover:text-rose-900 bg-rose-100 hover:bg-rose-200 transition-colors cursor-pointer shrink-0 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            <span>Quitar</span>
                                        </button>
                                    </div>
                                @else
                                    <!-- File Dropzone -->
                                    <div class="relative border-2 border-dashed border-amber-300 hover:border-amber-500 rounded-2xl p-5 sm:p-6 text-center bg-amber-50/30 hover:bg-amber-50/60 transition-colors">
                                        <input type="file" wire:model="transferReceipt" accept=".jpg,.jpeg,.png,.webp,.pdf,image/jpeg,image/png,image/webp,application/pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />

                                        <div wire:loading.remove wire:target="transferReceipt" class="space-y-2">
                                            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto text-xl shadow-xs">
                                                📤
                                            </div>
                                            <div>
                                                <span class="text-xs sm:text-sm font-bold text-slate-800">Haz clic para subir o arrastra tu comprobante</span>
                                                <p class="text-[11px] text-slate-500 mt-0.5">Formatos soportados: PDF, JPG, JPEG, PNG, WEBP (Hasta 10MB)</p>
                                            </div>
                                            <div class="pt-1">
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-800 bg-amber-100 px-2.5 py-1 rounded-full">
                                                    📎 Puedes adjuntarlo ahora o más tarde en tu portal
                                                </span>
                                            </div>
                                        </div>

                                        <div wire:loading wire:target="transferReceipt" class="space-y-2 py-2">
                                            <svg class="animate-spin h-8 w-8 text-amber-600 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            <p class="text-xs font-bold text-amber-900">Cargando y validando comprobante...</p>
                                        </div>
                                    </div>
                                @endif

                                @error('transferReceipt')
                                    <span class="text-xs text-rose-500 font-semibold mt-1.5 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                                <span>🔒 Almacenamiento seguro y privado de comprobantes</span>
                                <span class="text-amber-700 font-semibold">Validación por admisiones</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Bottom Confirm & Book CTA -->
                <div class="mt-7 pt-5 border-t border-slate-100 flex items-center justify-between">
                    <div class="text-xs text-slate-500">
                        Total a Procesar: <strong class="text-brand-primary font-black text-base sm:text-lg">${{ number_format($amount, 0) }} USD</strong>
                    </div>
                    <button type="button" wire:click="confirmBooking" wire:loading.attr="disabled" class="px-8 py-3.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-primary hover:bg-brand-primary-dark transition-all shadow-lg shadow-brand-primary/25 hover:shadow-brand-primary/40 cursor-pointer flex items-center gap-2">
                        <span wire:loading.remove>Formalizar y Confirmar Cita</span>
                        <span wire:loading class="flex items-center space-x-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Procesando reserva...</span>
                        </span>
                    </button>
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- PASO 5: CONFIRMACIÓN Y COMPROBANTE DIGITAL -->
        <!-- ========================================== -->
        @if($step === 5)
            <div class="p-6 sm:p-10 text-center animate-fadeIn">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-4 sm:mb-5 shadow-lg shadow-emerald-500/20">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>

                <span class="px-3.5 py-1 rounded-full text-xs font-black bg-brand-soft text-brand-primary uppercase tracking-wider">
                    Cita Médica Agendada Exitosamente
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">¡Tu Reserva ha sido Registrada!</h2>
                <p class="text-slate-500 text-xs sm:text-sm mt-1.5 max-w-md mx-auto">Código Único de Turno: <strong class="text-slate-900 font-mono text-base sm:text-lg font-bold">#{{ $confirmedAppointmentCode }}</strong></p>

                <!-- Digital Voucher Ticket -->
                <div class="mt-6 max-w-lg mx-auto p-5 sm:p-6 rounded-3xl bg-slate-50 border border-slate-200/80 text-left text-xs sm:text-sm space-y-3 shadow-xs">
                    <div class="flex justify-between py-1.5 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Especialidad:</span>
                        <span class="font-bold text-slate-800">{{ $selectedSpecialty->name }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Médico Especialista:</span>
                        <span class="font-bold text-slate-800">{{ $selectedDoctor->user->name }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Fecha y Horario:</span>
                        <span class="font-bold text-brand-primary">{{ $selectedDate }} | {{ $selectedTime }} - {{ $selectedEndTime }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Paciente:</span>
                        <span class="font-bold text-slate-800">{{ $firstName }} {{ $lastName }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-200/60">
                        <span class="text-slate-500 font-medium">Método de Pago:</span>
                        <span class="font-bold text-slate-800">
                            @if($paymentMethod === 'CREDIT_CARD')
                                💳 Tarjeta Débito / Crédito (Simulado)
                            @elseif($paymentMethod === 'PAYMENT_LINK')
                                🔗 Link de Pago Enviado al Correo
                            @elseif($paymentMethod === 'BANK_TRANSFER' || $paymentMethod === 'TRANSFER')
                                🏦 Transferencia Bancaria
                            @else
                                {{ $paymentMethod }}
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <span class="text-slate-500 font-medium">Monto Total:</span>
                        <span class="font-black text-brand-primary text-base">${{ number_format($amount, 0) }} USD</span>
                    </div>

                    @if($uploadedReceiptName)
                        <div class="mt-3 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                ✓
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-emerald-900 uppercase tracking-wider">Comprobante Adjuntado</div>
                                <div class="text-xs font-medium text-slate-800 truncate mt-0.5">{{ $uploadedReceiptName }}</div>
                                <div class="text-[11px] text-emerald-700 mt-0.5">Comprobante enviado a recepción para verificación y validación del turno.</div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Payment Link Highlight Card & Email Notification (if PAYMENT_LINK) -->
                @if($paymentMethod === 'PAYMENT_LINK')
                    <div class="mt-6 max-w-lg mx-auto p-5 rounded-3xl bg-purple-50/80 border border-purple-200 text-left space-y-3.5 shadow-sm animate-fadeIn">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-purple-600 text-white flex items-center justify-center font-bold text-lg shrink-0 shadow-sm">
                                ✉️
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-purple-950 text-sm">Enlace de Pago Enviado por Correo</h4>
                                <p class="text-xs text-purple-800 mt-0.5 leading-relaxed">
                                    Hemos enviado el enlace seguro a <strong class="text-purple-950">{{ $email }}</strong>. Tu cita quedará confirmada tan pronto se procese el pago.
                                </p>
                            </div>
                        </div>

                        <div class="p-3 rounded-2xl bg-white border border-purple-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <a href="{{ $generatedPaymentLink }}" target="_blank" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-sm transition-colors text-center inline-flex items-center justify-center gap-2">
                                <span>💳 Abrir Enlace de Pago</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            </a>
                            <button type="button" wire:click="resendPaymentLinkEmail" wire:loading.attr="disabled" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-purple-100 hover:bg-purple-200 text-purple-900 font-bold text-xs transition-colors cursor-pointer text-center inline-flex items-center justify-center gap-1.5">
                                <span wire:loading.remove wire:target="resendPaymentLinkEmail">📩 Reenviar Correo</span>
                                <span wire:loading wire:target="resendPaymentLinkEmail">Enviando...</span>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Navigation CTAs -->
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                    @if($isManual)
                        <button type="button" x-on:click="$dispatch('close-modal', 'quick-appointment-modal'); window.location.reload()" class="w-full sm:w-auto px-6 py-3 rounded-xl font-bold text-xs sm:text-sm text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-md shadow-emerald-600/20 cursor-pointer">
                            Listo / Cerrar Ventana
                        </button>
                        <a href="{{ route('admin.appointments') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl font-bold text-xs sm:text-sm text-slate-700 bg-slate-100 hover:bg-slate-200 transition-all">
                            Ver en Listado de Citas
                        </a>
                    @else
                        <a href="{{ route('patient.dashboard') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-primary hover:bg-brand-primary-dark transition-all shadow-lg shadow-brand-primary/25 cursor-pointer flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            <span>Ir a Mi Portal de Paciente</span>
                        </a>
                        <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl font-bold text-xs sm:text-sm text-slate-700 bg-slate-100 hover:bg-slate-200 transition-all">
                            Volver al Inicio
                        </a>
                    @endif
                </div>
            </div>
        @endif

    </div>
</div>
