<div class="w-full">
    <!-- Stepper Header -->
    <div class="mb-6 sm:mb-8 px-2">
        @php
            if ($isManual) {
                $steps = [
                    1 => 'Especialidad & Servicio',
                    2 => 'Médico & Horario',
                    3 => 'Datos del Paciente',
                ];
                $maxSteps = 3;
                $progressWidth = min(100, (($step - 1) / 2) * 100);
            } else {
                $steps = [
                    1 => 'Especialidad & Servicio',
                    2 => 'Médico & Horario',
                    3 => 'Datos del Paciente',
                    4 => 'Método de Pago',
                    5 => 'Confirmación',
                ];
                $maxSteps = 5;
                $progressWidth = min(100, (($step - 1) / 4) * 100);
            }
        @endphp

        <div class="flex items-center justify-between relative max-w-2xl mx-auto">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-slate-200 w-full z-0 rounded-full"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-brand-primary transition-all duration-500 z-0 rounded-full" style="width: {{ $progressWidth }}%"></div>

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
                Paso {{ min($step, $maxSteps) }} de {{ $maxSteps }}: {{ $steps[min($step, $maxSteps)] ?? '' }}
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

                            <!-- Specific procedures with Checkbox Indicators -->
                            @foreach($procedures as $proc)
                                <div class="w-full rounded-2xl border-2 text-left transition-all duration-200 overflow-hidden {{ in_array($proc->id, $selectedProceduresList) ? 'border-brand-primary bg-brand-soft/60 ring-2 ring-brand-primary/20 shadow-xs' : 'border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300' }}">
                                    <button type="button" wire:key="proc-{{ $proc->id }}" wire:click="toggleProcedure({{ $proc->id }})" wire:loading.class="opacity-70 animate-pulse" wire:target="toggleProcedure({{ $proc->id }})" class="w-full p-4 flex items-center justify-between cursor-pointer group">
                                        <div class="flex items-center gap-3.5">
                                            <!-- Checkbox Option Indicator -->
                                            <div class="w-6 h-6 rounded-md border-2 flex items-center justify-center shrink-0 transition-all {{ in_array($proc->id, $selectedProceduresList) ? 'border-brand-primary bg-brand-primary shadow-xs' : 'border-slate-300 bg-white group-hover:border-slate-400' }}">
                                                @if(in_array($proc->id, $selectedProceduresList))
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                @endif
                                            </div>
                                            <div class="w-9 h-9 rounded-xl shadow-xs border flex items-center justify-center shrink-0 {{ in_array($proc->id, $selectedProceduresList) ? 'bg-brand-primary text-white border-brand-primary' : 'bg-white text-brand-primary border-slate-100' }}">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 text-sm">{{ $proc->name }}</div>
                                                <div class="text-xs text-slate-500">{{ $proc->description ?: 'Estudio / Procedimiento' }} ({{ $proc->duration_minutes }} min)</div>
                                            </div>
                                        </div>
                                        <div class="text-right flex items-center gap-3">
                                            @if(in_array($proc->id, $selectedProceduresList))
                                                <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-brand-primary bg-white px-2 py-0.5 rounded-md border border-brand-primary/20 shadow-xs">✓ Sel.</span>
                                            @endif
                                            <div>
                                                <span class="block font-black text-brand-primary text-base">${{ number_format($proc->price, 0) }}</span>
                                                <span class="text-[10px] text-slate-400 uppercase font-bold">Procedimiento</span>
                                            </div>
                                        </div>
                                    </button>

                                    <!-- Subprocedures if they exist -->
                                    @if($proc->subprocedures && $proc->subprocedures->count() > 0 && in_array($proc->id, $selectedProceduresList))
                                        <div class="px-5 pb-5 pt-2 ml-9 border-t border-slate-100">
                                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Subprocedimientos Disponibles</h4>
                                            <div class="space-y-2">
                                                @foreach($proc->subprocedures as $sub)
                                                    <button type="button" wire:key="sub-{{ $sub->id }}" wire:click="toggleSubprocedure({{ $sub->id }})" class="w-full p-3 rounded-xl border flex items-center justify-between cursor-pointer transition-all {{ in_array($sub->id, $selectedSubproceduresList) ? 'border-brand-primary bg-white ring-1 ring-brand-primary/30 shadow-sm' : 'border-slate-200 bg-slate-50 hover:bg-white' }}">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-5 h-5 rounded border-2 flex items-center justify-center shrink-0 {{ in_array($sub->id, $selectedSubproceduresList) ? 'border-brand-primary bg-brand-primary' : 'border-slate-300' }}">
                                                                @if(in_array($sub->id, $selectedSubproceduresList))
                                                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                                @endif
                                                            </div>
                                                            <div class="text-left">
                                                                <div class="text-sm font-semibold text-slate-800">{{ $sub->name }}</div>
                                                                @if($sub->duration_minutes)
                                                                    <div class="text-[10px] text-slate-500">{{ $sub->duration_minutes }} min</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="font-bold text-brand-primary text-sm">
                                                            ${{ number_format($sub->price, 0) }}
                                                        </div>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
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
                        <span class="text-xs font-extrabold uppercase tracking-wider text-brand-primary bg-brand-soft px-3 py-1 rounded-md">
                            {{ $isManual ? 'Paso 3 de 3' : 'Paso 3 de 5' }}
                        </span>
                        <h2 class="text-xl sm:text-2xl font-black text-slate-900 mt-1.5">{{ $isManual ? 'Datos del Paciente (Registro de Cita)' : 'Datos del Paciente' }}</h2>
                        <p class="text-slate-500 text-xs sm:text-sm mt-0.5">
                            {{ $isManual ? 'Ingresa los datos del paciente para generar la reserva pendiente de pago.' : 'Ingresa la información personal para generar la historia y reserva clínica.' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2.5 self-end sm:self-center shrink-0">
                        <button type="button" wire:click="previousStep" class="text-xs sm:text-sm font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1 px-3 py-2 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            <span>Atrás</span>
                        </button>
                        @if($isManual)
                            <button type="button" wire:click="confirmBooking" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-md shadow-emerald-600/20 cursor-pointer flex items-center gap-1.5">
                                <span wire:loading.remove wire:target="confirmBooking">Agendar Cita</span>
                                <span wire:loading wire:target="confirmBooking">Procesando...</span>
                                <svg wire:loading.remove wire:target="confirmBooking" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                            </button>
                        @else
                            <button type="button" wire:click="proceedToStep4" class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-primary hover:bg-brand-primary-dark transition-all shadow-md shadow-brand-primary/20 hover:shadow-brand-primary/35 cursor-pointer flex items-center gap-1.5">
                                <span>Continuar</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </button>
                        @endif
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
                    <div class="space-y-4 mb-4">
                        <!-- 1. Cédula de Identidad -->
                        <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/70 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">1. Cédula de Identidad *</label>
                                @if($cedulaStatus === 'valid')
                                    <span class="text-[11px] font-semibold text-emerald-600">✓ Cédula verificada</span>
                                @endif
                            </div>
                            <div class="relative">
                                <input type="text" wire:model.live.debounce.400ms="identificationNumber" maxlength="10" class="w-full rounded-xl border-slate-200 text-base font-mono font-bold p-3 pr-12 focus:border-brand-primary focus:ring-brand-primary tracking-widest bg-white shadow-xs" placeholder="0912345678" />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <div wire:loading wire:target="identificationNumber">
                                        <svg class="animate-spin h-5 w-5 text-brand-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                    </div>
                                    @if($cedulaStatus === 'valid')
                                        <div wire:loading.remove wire:target="identificationNumber" class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs">✓</div>
                                    @elseif($cedulaStatus === 'invalid')
                                        <div wire:loading.remove wire:target="identificationNumber" class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xs">✕</div>
                                    @endif
                                </div>
                            </div>
                            @if($cedulaMessage)
                                <div class="text-xs font-semibold flex items-center gap-1.5 {{ $cedulaStatus === 'valid' ? 'text-emerald-700' : 'text-rose-600' }}">
                                    <span>{{ $cedulaStatus === 'valid' ? '🏥' : '⚠️' }}</span>
                                    <span>{{ $cedulaMessage }}</span>
                                </div>
                            @endif
                            @error('identificationNumber') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- 2. Nombres y Apellidos -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                        </div>

                        <!-- 3. Fecha de Nacimiento y Género -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Fecha de Nacimiento *</label>
                                <input type="date" wire:model="birthDate" max="{{ now()->format('Y-m-d') }}" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50/50 focus:bg-white shadow-xs" />
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
                        </div>

                        <!-- 4. Correo Electrónico y Teléfono -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                        </div>

                        <!-- 5. Contraseña (solo para usuarios nuevos del portal público) -->
                        @if(!$isManual && !Auth::check())
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Crear Contraseña para tu Portal Paciente *</label>
                                <input type="password" wire:model="password" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50/50 focus:bg-white shadow-xs" placeholder="Mínimo 8 caracteres" />
                                <p class="text-[11px] text-slate-500 mt-1">Podrás iniciar sesión con tu correo y esta clave para ver recetas, certificados y tus citas agendadas.</p>
                                @error('password') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        <!-- 6. Protección y Tratamiento de Datos Personales (LOPDP) -->
                        @if($isManual)
                            <!-- Consentimiento informado registrado por Recepción / Admin -->
                            <div class="p-4 rounded-2xl bg-brand-soft/40 border border-brand-primary/25 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-extrabold uppercase tracking-wider text-brand-primary flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        Tratamiento de Datos Personales (LOPDP) *
                                    </span>
                                    @if($patientExists && $dataConsent)
                                        <span class="text-[11px] font-bold text-emerald-700 bg-emerald-100/90 px-2.5 py-0.5 rounded-md flex items-center gap-1">
                                            <span>✓</span>
                                            <span>Consentimiento previo registrado</span>
                                        </span>
                                    @endif
                                </div>
                                <label class="flex items-start gap-2.5 cursor-pointer select-none">
                                    <input type="checkbox" wire:model="dataConsent" class="mt-0.5 h-4 w-4 rounded text-brand-primary focus:ring-brand-primary cursor-pointer" />
                                    <span class="text-xs text-slate-700 leading-relaxed">
                                        El paciente ha sido informado y <strong>autoriza el tratamiento de sus datos personales y de salud</strong> conforme a la <strong>Ley Orgánica de Protección de Datos Personales (LOPDP)</strong> del Ecuador para la gestión de su cita médica y ficha clínica institucional.
                                        <a href="https://www.abogadoslatam.com/politicas/surhospital/salud" target="_blank" rel="noopener noreferrer" class="text-brand-primary font-bold hover:underline inline-flex items-center gap-1 ml-1">
                                            Ver Políticas de Privacidad
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </span>
                                </label>
                                @error('dataConsent') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @elseif(!Auth::check())
                            <!-- Flujo Público de Agendamiento Online -->
                            <div class="p-3.5 rounded-2xl bg-brand-soft/30 border border-brand-primary/20">
                                <label class="flex items-start gap-2.5 cursor-pointer select-none">
                                    <input type="checkbox" wire:model="dataConsent" class="mt-0.5 h-4 w-4 rounded text-brand-primary focus:ring-brand-primary cursor-pointer" />
                                    <span class="text-xs text-slate-700 leading-relaxed">
                                        He leído y autorizo el tratamiento de mis datos personales y de salud conforme a la <strong>Ley Orgánica de Protección de Datos Personales (LOPDP)</strong> del Ecuador.
                                        <a href="https://www.abogadoslatam.com/politicas/surhospital/salud" target="_blank" rel="noopener noreferrer" class="text-brand-primary font-bold hover:underline inline-flex items-center gap-1 ml-1">
                                            Ver Políticas de Privacidad
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        </a>
                                    </span>
                                </label>
                                @error('dataConsent') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Motivo de Consulta u Observaciones Clínicas (Opcional)</label>
                    <textarea wire:model="reason" rows="2" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50/50 focus:bg-white shadow-xs" placeholder="Describe brevemente los síntomas o notas para el médico tratante..."></textarea>
                </div>

                <!-- Bottom Continue CTA -->
                @if($isManual)
                    <div class="mt-7 pt-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="p-3 rounded-xl bg-amber-50/80 border border-amber-200 text-amber-900 text-xs flex items-center space-x-2.5 w-full sm:w-auto">
                            <span class="text-base">💳</span>
                            <span>La cita se guardará como <strong class="font-bold">Pendiente de Pago</strong> para que el paciente complete el pago desde su portal.</span>
                        </div>
                        <button type="button" wire:click="confirmBooking" wire:loading.attr="disabled" class="w-full sm:w-auto px-7 py-3.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-emerald-600 hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/25 cursor-pointer flex items-center justify-center gap-2 shrink-0">
                            <span wire:loading.remove wire:target="confirmBooking">Agendar Cita (Pendiente de Pago)</span>
                            <span wire:loading wire:target="confirmBooking" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Creando reserva...</span>
                            </span>
                            <svg wire:loading.remove wire:target="confirmBooking" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                        </button>
                    </div>
                @else
                    <div class="mt-7 pt-5 border-t border-slate-100 flex justify-end">
                        <button type="button" wire:click="proceedToStep4" class="px-7 py-3 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-primary hover:bg-brand-primary-dark transition-all shadow-md shadow-brand-primary/20 cursor-pointer flex items-center gap-2">
                            <span>Continuar al Método de Pago</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </button>
                    </div>
                @endif
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
                <!-- Tabs / Opciones de Pago con Radio Indicator -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 mb-6">
                    <!-- Opción 1: Tarjeta de Débito/Crédito -->
                    <button type="button" wire:click="$set('paymentMethod', 'CREDIT_CARD')" class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer group relative {{ $paymentMethod === 'CREDIT_CARD' ? 'border-brand-primary bg-brand-soft/60 ring-2 ring-brand-primary/25 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                        <div class="flex items-center justify-between mb-2.5">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center font-bold text-base shadow-sm">
                                💳
                            </div>
                            <div class="flex items-center gap-1.5">
                                @if($paymentMethod === 'CREDIT_CARD')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-brand-primary bg-white px-2 py-0.5 rounded-md border border-brand-primary/20 shadow-xs">
                                        ✓ Activo
                                    </span>
                                @endif
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all {{ $paymentMethod === 'CREDIT_CARD' ? 'border-brand-primary bg-brand-primary ring-2 ring-brand-primary/20' : 'border-slate-300 bg-white group-hover:border-slate-400' }}">
                                    @if($paymentMethod === 'CREDIT_CARD')
                                        <span class="w-2 h-2 rounded-full bg-white"></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <h4 class="font-bold {{ $paymentMethod === 'CREDIT_CARD' ? 'text-brand-primary' : 'text-slate-900' }} text-sm">Tarjeta Débito / Crédito</h4>
                        <span class="inline-block text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 mt-1">En Línea • Inmediato</span>
                        <p class="text-[11px] text-slate-500 mt-1.5 leading-snug">Pasarela segura con confirmación directa y automática de tu turno.</p>
                    </button>

                    <!-- Opción 2: Enlace de Pago DataLink (Manual Asistido) -->
                    <button type="button" wire:click="$set('paymentMethod', 'PAYMENT_LINK')" class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer group relative {{ $paymentMethod === 'PAYMENT_LINK' ? 'border-brand-primary bg-brand-soft/60 ring-2 ring-brand-primary/25 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                        <div class="flex items-center justify-between mb-2.5">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-purple-600 to-violet-500 text-white flex items-center justify-center font-bold text-base shadow-sm">
                                🔗
                            </div>
                            <div class="flex items-center gap-1.5">
                                @if($paymentMethod === 'PAYMENT_LINK')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-brand-primary bg-white px-2 py-0.5 rounded-md border border-brand-primary/20 shadow-xs">
                                        ✓ Activo
                                    </span>
                                @endif
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all {{ $paymentMethod === 'PAYMENT_LINK' ? 'border-brand-primary bg-brand-primary ring-2 ring-brand-primary/20' : 'border-slate-300 bg-white group-hover:border-slate-400' }}">
                                    @if($paymentMethod === 'PAYMENT_LINK')
                                        <span class="w-2 h-2 rounded-full bg-white"></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <h4 class="font-bold {{ $paymentMethod === 'PAYMENT_LINK' ? 'text-brand-primary' : 'text-slate-900' }} text-sm">Enlace de Pago DataLink</h4>
                        <span class="inline-block text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-purple-100 text-purple-800 mt-1">Datafast • Asistido</span>
                        <p class="text-[11px] text-slate-500 mt-1.5 leading-snug">Emisión personalizada por recepción enviada a tu WhatsApp y correo.</p>
                    </button>

                    <!-- Opción 3: Transferencia Bancaria -->
                    <button type="button" wire:click="$set('paymentMethod', 'BANK_TRANSFER')" class="p-4 rounded-2xl border-2 text-left transition-all cursor-pointer group relative {{ ($paymentMethod === 'BANK_TRANSFER' || $paymentMethod === 'TRANSFER') ? 'border-brand-primary bg-brand-soft/60 ring-2 ring-brand-primary/25 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                        <div class="flex items-center justify-between mb-2.5">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-yellow-600 text-white flex items-center justify-center font-bold text-base shadow-sm">
                                🏦
                            </div>
                            <div class="flex items-center gap-1.5">
                                @if($paymentMethod === 'BANK_TRANSFER' || $paymentMethod === 'TRANSFER')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-brand-primary bg-white px-2 py-0.5 rounded-md border border-brand-primary/20 shadow-xs">
                                        ✓ Activo
                                    </span>
                                @endif
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition-all {{ ($paymentMethod === 'BANK_TRANSFER' || $paymentMethod === 'TRANSFER') ? 'border-brand-primary bg-brand-primary ring-2 ring-brand-primary/20' : 'border-slate-300 bg-white group-hover:border-slate-400' }}">
                                    @if($paymentMethod === 'BANK_TRANSFER' || $paymentMethod === 'TRANSFER')
                                        <span class="w-2 h-2 rounded-full bg-white"></span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <h4 class="font-bold {{ ($paymentMethod === 'BANK_TRANSFER' || $paymentMethod === 'TRANSFER') ? 'text-brand-primary' : 'text-slate-900' }} text-sm">Transferencia Bancaria</h4>
                        <span class="inline-block text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-amber-100 text-amber-800 mt-1">Banca Móvil • Voucher</span>
                        <p class="text-[11px] text-slate-500 mt-1.5 leading-snug">Depósito o transferencia con número de referencia o foto de recibo.</p>
                    </button>
                </div>

                <!-- CONTENIDO SEGÚN MÉTODO DE PAGO SELECCIONADO -->

                <!-- 1. PASARELA TARJETA CON DISEÑO PROFESIONAL Y VIRTUAL CARD PREVIEW -->
                @if($paymentMethod === 'CREDIT_CARD')
                    <div class="p-6 sm:p-7 rounded-3xl bg-white border border-slate-200/90 shadow-sm animate-fadeIn space-y-6">
                        <!-- Selector de Pasarela Integrada -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Pasarela de Procesamiento</span>
                                <p class="text-xs text-slate-400">Selecciona el procesador autorizado para esta transacción</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="$set('cardGateway', 'payphone')" class="px-3.5 py-2 rounded-xl border-2 transition-all flex items-center gap-2 cursor-pointer {{ $cardGateway === 'payphone' ? 'border-orange-500 bg-orange-50/70 text-orange-800 shadow-xs ring-1 ring-orange-200' : 'border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                                    <span class="w-5 h-5 rounded-md bg-orange-500 text-white flex items-center justify-center font-bold text-[10px]">P</span>
                                    <span class="text-xs font-bold">Payphone</span>
                                    @if($cardGateway === 'payphone') <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> @endif
                                </button>
                                <button type="button" wire:click="$set('cardGateway', 'kushki')" class="px-3.5 py-2 rounded-xl border-2 transition-all flex items-center gap-2 cursor-pointer {{ $cardGateway === 'kushki' ? 'border-brand-primary bg-brand-soft text-brand-primary shadow-xs ring-1 ring-brand-primary/20' : 'border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                                    <span class="w-5 h-5 rounded-md bg-brand-primary text-white flex items-center justify-center font-bold text-[10px]">K</span>
                                    <span class="text-xs font-bold">Kushki</span>
                                    @if($cardGateway === 'kushki') <span class="w-1.5 h-1.5 rounded-full bg-brand-primary"></span> @endif
                                </button>
                            </div>
                        </div>

                        <!-- Card Visualizer & Form Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                            <!-- Left: Interactive Virtual Credit Card -->
                            <div class="lg:col-span-5 flex flex-col items-center">
                                <div class="w-full max-w-[340px] aspect-[1.586/1] rounded-2xl p-5 text-white shadow-2xl relative overflow-hidden transition-transform transform hover:scale-[1.02] duration-300 flex flex-col justify-between"
                                     style="background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #047857 100%);">
                                    <!-- Glossy Sheen Overlay -->
                                    <div class="absolute -top-12 -right-12 w-44 h-44 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
                                    <div class="absolute inset-0 bg-gradient-to-tr from-white/5 via-transparent to-white/10 pointer-events-none"></div>

                                    <!-- Top Card Row: EMV Chip & Contactless -->
                                    <div class="flex items-center justify-between relative z-10">
                                        <div class="flex items-center gap-3">
                                            <!-- Gold EMV Chip Graphic -->
                                            <div class="w-11 h-8 rounded-md bg-gradient-to-tr from-amber-300 via-amber-200 to-yellow-400 p-1 shadow-md border border-amber-400/80 flex flex-col justify-between">
                                                <div class="w-full h-px bg-amber-700/30"></div>
                                                <div class="flex justify-between w-full h-full items-center">
                                                    <div class="w-2.5 h-3 border border-amber-700/30 rounded-xs"></div>
                                                    <div class="w-2.5 h-3 border border-amber-700/30 rounded-xs"></div>
                                                </div>
                                                <div class="w-full h-px bg-amber-700/30"></div>
                                            </div>
                                            <!-- Contactless Waves Icon -->
                                            <svg class="w-5 h-5 text-slate-300/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 7a7 7 0 010 10m4-12a10 10 0 010 14m4-16a13 13 0 010 18" />
                                            </svg>
                                        </div>
                                        <div class="text-right">
                                            @php
                                                $cardFirst = substr(preg_replace('/[^0-9]/', '', $cardNumber), 0, 1);
                                                $isVisa = $cardFirst === '4';
                                                $isMastercard = $cardFirst === '5';
                                                $isAmex = $cardFirst === '3';
                                            @endphp
                                            @if($isVisa)
                                                <span class="font-black italic text-lg tracking-wider text-blue-300">VISA</span>
                                            @elseif($isMastercard)
                                                <div class="flex -space-x-2 justify-end">
                                                    <span class="w-5 h-5 rounded-full bg-rose-500 opacity-90 inline-block"></span>
                                                    <span class="w-5 h-5 rounded-full bg-amber-400 opacity-90 inline-block"></span>
                                                </div>
                                            @elseif($isAmex)
                                                <span class="font-black text-xs tracking-wider text-cyan-300 border border-cyan-400/50 px-1 py-0.5 rounded">AMEX</span>
                                            @else
                                                <span class="text-xs font-black uppercase tracking-wider text-slate-300/80">DEBIT / CREDIT</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Middle Card Row: 16 Digits Number -->
                                    <div class="my-auto py-2 relative z-10">
                                        <div class="font-mono text-base sm:text-lg tracking-widest text-white/95 font-black drop-shadow-sm">
                                            @php
                                                $cleanNum = preg_replace('/[^0-9]/', '', $cardNumber);
                                                $paddedNum = str_pad($cleanNum, 16, '•');
                                                $formattedNum = implode(' ', str_split(substr($paddedNum, 0, 16), 4));
                                            @endphp
                                            {{ $cardNumber ? $formattedNum : '•••• •••• •••• ••••' }}
                                        </div>
                                    </div>

                                    <!-- Bottom Card Row: Cardholder & Expiry -->
                                    <div class="flex items-end justify-between relative z-10 text-[11px]">
                                        <div class="min-w-0 pr-2">
                                            <span class="text-[9px] uppercase tracking-wider text-slate-400 block font-semibold">Titular</span>
                                            <span class="font-bold tracking-wider uppercase text-white truncate block max-w-[190px]">
                                                {{ !empty($cardHolder) ? $cardHolder : 'NOMBRE DEL TITULAR' }}
                                            </span>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="text-[9px] uppercase tracking-wider text-slate-400 block font-semibold">Vence</span>
                                            <span class="font-mono font-bold tracking-wider text-white">
                                                {{ !empty($cardExpiry) ? $cardExpiry : 'MM/AA' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Trust & Security Badges -->
                                <div class="mt-4 flex flex-wrap items-center justify-center gap-2 text-[11px] text-slate-500">
                                    <span class="inline-flex items-center gap-1 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-lg text-slate-600 font-medium">
                                        🔒 TLS 256-Bit SSL
                                    </span>
                                    <span class="inline-flex items-center gap-1 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-lg text-slate-600 font-medium">
                                        🛡️ PCI-DSS Level 1
                                    </span>
                                    <span class="inline-flex items-center gap-1 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-lg text-slate-600 font-medium">
                                        ✓ 3D Secure 2.0
                                    </span>
                                </div>
                            </div>

                            <!-- Right: Card Form Inputs -->
                            <div class="lg:col-span-7 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Datos de la Tarjeta</span>
                                    <button type="button" wire:click="fillDemoCard" class="text-xs font-bold text-brand-primary hover:text-brand-primary-dark bg-brand-soft hover:bg-brand-soft/80 px-3 py-1.5 rounded-xl border border-brand-primary/20 transition-colors cursor-pointer flex items-center gap-1.5 shadow-2xs">
                                        <span>⚡ Llenar Datos de Prueba</span>
                                    </button>
                                </div>

                                <div class="space-y-3.5">
                                    <!-- Titular -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Nombre del Titular (como figura en la tarjeta) *</label>
                                        <input type="text" wire:model.live="cardHolder" class="w-full rounded-xl border-slate-200 text-sm p-3 font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:border-brand-primary focus:ring-brand-primary shadow-xs transition-colors" placeholder="Ej: CARLOS A MENDOZA" />
                                        @error('cardHolder') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Número Tarjeta -->
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Número de Tarjeta (16 Dígitos) *</label>
                                        <div class="relative">
                                            <input type="text" wire:model.live="cardNumber" maxlength="19" class="w-full rounded-xl border-slate-200 text-sm p-3 font-mono font-bold text-slate-800 bg-slate-50/70 focus:bg-white focus:border-brand-primary focus:ring-brand-primary shadow-xs transition-colors pr-10" placeholder="4532 8900 1234 5678" />
                                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                                                💳
                                            </div>
                                        </div>
                                        @error('cardNumber') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="grid grid-cols-2 gap-3.5">
                                        <!-- Fecha Expiración -->
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Expiración *</label>
                                            <input type="text" wire:model.live="cardExpiry" maxlength="5" class="w-full rounded-xl border-slate-200 text-sm p-3 font-mono font-bold text-slate-800 bg-slate-50/70 focus:bg-white focus:border-brand-primary focus:ring-brand-primary shadow-xs transition-colors" placeholder="MM/AA (Ej: 12/28)" />
                                            @error('cardExpiry') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- CVV -->
                                        <div>
                                            <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Código CVV / CVC *</label>
                                            <div class="relative">
                                                <input type="password" maxlength="4" wire:model.live="cardCvv" class="w-full rounded-xl border-slate-200 text-sm p-3 font-mono font-bold text-slate-800 bg-slate-50/70 focus:bg-white focus:border-brand-primary focus:ring-brand-primary shadow-xs transition-colors" placeholder="•••" />
                                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400">3-4 Díg.</span>
                                            </div>
                                            @error('cardCvv') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs text-slate-600">
                                    <span class="font-medium">Total a debitar:</span>
                                    <span class="font-black text-brand-primary text-base">${{ number_format($amount, 0) }} USD</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 2. ENLACE DE PAGO DATALINK (MANUAL POR RECEPCIÓN / ADMISIONES) -->
                @if($paymentMethod === 'PAYMENT_LINK')
                    <div class="p-6 sm:p-7 rounded-3xl bg-purple-50/50 border-2 border-purple-200/90 shadow-sm animate-fadeIn space-y-5">
                        <!-- Header with Datafast / DataLink Branding -->
                        <div class="flex items-start justify-between gap-4 pb-4 border-b border-purple-200/70">
                            <div class="flex items-start gap-3.5">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center font-bold text-xl shrink-0 shadow-md shadow-purple-600/20">
                                    🔗
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-base sm:text-lg font-bold text-purple-950">Pago Asistido con Enlace DataLink (Datafast)</h3>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-emerald-800 bg-emerald-100 border border-emerald-300 px-2 py-0.5 rounded-md">
                                            ✓ Seleccionado
                                        </span>
                                    </div>
                                    <p class="text-xs text-purple-900 mt-0.5 leading-relaxed">
                                        Emisión y gestión directa por nuestro personal de admisiones a través del portal oficial de pagos Datafast.
                                    </p>
                                </div>
                            </div>
                            <span class="hidden sm:inline-block px-3 py-1 rounded-xl text-xs font-black bg-purple-200 text-purple-900 uppercase tracking-wider shrink-0">
                                Generación Manual
                            </span>
                        </div>

                        <!-- Info Callout: How it works -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-purple-200/80 shadow-xs space-y-3.5">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-purple-900 flex items-center gap-1.5">
                                <span>📋 ¿Cómo funciona este método de pago?</span>
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                                <div class="p-3 rounded-xl bg-purple-50/70 border border-purple-100 flex flex-col justify-between">
                                    <div class="w-7 h-7 rounded-lg bg-purple-600 text-white font-black text-xs flex items-center justify-center mb-2">1</div>
                                    <div class="font-bold text-purple-950">Reserva Tu Cita</div>
                                    <p class="text-[11px] text-slate-500 mt-1">El turno queda formalizado temporalmente a tu nombre.</p>
                                </div>
                                <div class="p-3 rounded-xl bg-purple-50/70 border border-purple-100 flex flex-col justify-between">
                                    <div class="w-7 h-7 rounded-lg bg-purple-600 text-white font-black text-xs flex items-center justify-center mb-2">2</div>
                                    <div class="font-bold text-purple-950">Recepción Emite Link</div>
                                    <p class="text-[11px] text-slate-500 mt-1">El personal de clínica crea el enlace en Datafast.</p>
                                </div>
                                <div class="p-3 rounded-xl bg-purple-50/70 border border-purple-100 flex flex-col justify-between">
                                    <div class="w-7 h-7 rounded-lg bg-purple-600 text-white font-black text-xs flex items-center justify-center mb-2">3</div>
                                    <div class="font-bold text-purple-950">Recibes Link</div>
                                    <p class="text-[11px] text-slate-500 mt-1">Te contactamos por WhatsApp y correo con el enlace seguro.</p>
                                </div>
                                <div class="p-3 rounded-xl bg-purple-50/70 border border-purple-100 flex flex-col justify-between">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white font-black text-xs flex items-center justify-center mb-2">4</div>
                                    <div class="font-bold text-purple-950">Confirmación Total</div>
                                    <p class="text-[11px] text-slate-500 mt-1">Pagas con tarjeta y tu cita se confirma automáticamente.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Canales de Envío Registrados -->
                        <div class="p-4 rounded-2xl bg-white border border-purple-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="space-y-1">
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Canales para el envío del DataLink:</span>
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="inline-flex items-center gap-1.5 font-bold text-slate-800 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200">
                                        ✉️ {{ $email ?: 'Correo registrado del paciente' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 font-bold text-slate-800 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200">
                                        📱 {{ $phone ?: 'Teléfono / WhatsApp' }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right sm:border-l sm:border-slate-100 sm:pl-4">
                                <span class="text-[11px] text-slate-400 block font-semibold">Valor a emitir:</span>
                                <span class="text-base font-black text-purple-950">${{ number_format($amount, 0) }} USD</span>
                            </div>
                        </div>

                        <!-- Aviso Legal y de Seguimiento -->
                        <div class="p-3.5 rounded-xl bg-purple-100/70 border border-purple-200 text-purple-900 text-xs flex items-start gap-2.5">
                            <span class="text-base">📌</span>
                            <div class="leading-relaxed">
                                <strong>Importante:</strong> Al presionar <strong>Confirmar Cita</strong>, se notificará a recepción y admisiones de la clínica para que generen el enlace de pago DataLink y te lo compartan a la brevedad posible.
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 3. TRANSFERENCIA BANCARIA -->
                @if($paymentMethod === 'BANK_TRANSFER' || $paymentMethod === 'TRANSFER')
                    <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 animate-fadeIn space-y-6">
                        <div class="flex items-start gap-3.5 pb-5 border-b border-slate-200">
                            <div class="w-12 h-12 rounded-2xl bg-brand-primary text-white flex items-center justify-center font-bold text-xl shrink-0 shadow-lg shadow-brand-primary/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Transferencia Bancaria o Depósito</h3>
                                <p class="text-sm text-slate-500 mt-0.5">Realiza el pago a una de nuestras cuentas y sube el comprobante para confirmar tu cita.</p>
                            </div>
                        </div>

                        <!-- Cuentas Bancarias Institucionales -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Banco Pichincha -->
                            <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm relative overflow-hidden flex flex-col justify-between group hover:border-brand-primary transition-colors">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                                            <span class="text-sm font-black text-slate-700">BP</span>
                                        </div>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 uppercase tracking-wider">Cta. Corriente</span>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">Banco Pichincha</h4>
                                    <div class="text-lg font-black text-brand-primary mt-1 font-mono tracking-widest">2100849201</div>
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-100">
                                    <div class="text-xs text-slate-500 font-medium">Titular: <strong class="text-slate-700">SurHospital C.A.</strong></div>
                                    <div class="text-xs text-slate-500 font-medium mt-0.5">RUC: <strong class="text-slate-700 font-mono">0992384756001</strong></div>
                                </div>
                            </div>

                            <!-- Banco Guayaquil -->
                            <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm relative overflow-hidden flex flex-col justify-between group hover:border-brand-primary transition-colors">
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                                            <span class="text-sm font-black text-slate-700">BG</span>
                                        </div>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 uppercase tracking-wider">Cta. Corriente</span>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800">Banco Guayaquil</h4>
                                    <div class="text-lg font-black text-brand-primary mt-1 font-mono tracking-widest">1004829102</div>
                                </div>
                                <div class="mt-4 pt-3 border-t border-slate-100">
                                    <div class="text-xs text-slate-500 font-medium">Titular: <strong class="text-slate-700">SurHospital C.A.</strong></div>
                                    <div class="text-xs text-slate-500 font-medium mt-0.5">RUC: <strong class="text-slate-700 font-mono">0992384756001</strong></div>
                                </div>
                            </div>
                        </div>

                        <!-- Datos del Comprobante / Formulario de Transferencia -->
                        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm mt-4">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-slate-800 mb-4 flex items-center gap-2 pb-3 border-b border-slate-100">
                                <svg class="w-5 h-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <span>Registro de Comprobante</span>
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Banco Emisor *</label>
                                    <select wire:model="transferBank" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50 focus:bg-white font-medium shadow-xs transition-colors">
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
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Nº de Referencia / Doc.</label>
                                    <input type="text" wire:model="transferReference" class="w-full rounded-xl border-slate-200 text-sm p-3 font-mono focus:border-brand-primary focus:ring-brand-primary bg-slate-50 focus:bg-white shadow-xs transition-colors" placeholder="Ej: 98347102" />
                                    @error('transferReference') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Fecha de Transferencia</label>
                                    <input type="date" wire:model="transferDate" max="{{ now()->format('Y-m-d') }}" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50 focus:bg-white shadow-xs transition-colors" />
                                    @error('transferDate') <span class="text-xs text-rose-500 font-semibold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">Notas Adicionales</label>
                                    <input type="text" wire:model="transferNotes" class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-slate-50 focus:bg-white shadow-xs transition-colors" placeholder="Ej: Pago realizado por familiar" />
                                </div>
                            </div>

                            <!-- File Upload Zone for Comprobante -->
                            <div class="mt-5 pt-5 border-t border-slate-100">
                                <label class="block text-xs font-bold text-slate-700 mb-3 uppercase tracking-wider">
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

                @if($isManual)
                    <span class="px-3.5 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-800 border border-amber-200 uppercase tracking-wider">
                        🟡 Cita Creada — Pendiente de Pago por el Paciente
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">¡Cita Registrada en Recepción!</h2>
                    <p class="text-slate-500 text-xs sm:text-sm mt-1.5 max-w-md mx-auto">Código Único de Turno: <strong class="text-slate-900 font-mono text-base sm:text-lg font-bold">#{{ $confirmedAppointmentCode }}</strong></p>
                @else
                    <span class="px-3.5 py-1 rounded-full text-xs font-black bg-brand-soft text-brand-primary uppercase tracking-wider">
                        Cita Médica Agendada Exitosamente
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2">¡Tu Reserva ha sido Registrada!</h2>
                    <p class="text-slate-500 text-xs sm:text-sm mt-1.5 max-w-md mx-auto">Código Único de Turno: <strong class="text-slate-900 font-mono text-base sm:text-lg font-bold">#{{ $confirmedAppointmentCode }}</strong></p>
                @endif

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
                        <span class="text-slate-500 font-medium">Estado Inicial:</span>
                        <span class="font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-md text-xs">
                            Pendiente de Pago
                        </span>
                    </div>
                    <div class="flex justify-between py-1.5">
                        <span class="text-slate-500 font-medium">Monto a Pagar:</span>
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

                <!-- Payment Link & WhatsApp Share Box for Manual Admin/Reception or Online -->
                @if($isManual)
                    <div class="mt-6 max-w-lg mx-auto p-5 rounded-3xl bg-amber-50/70 border border-amber-200/90 text-left space-y-3.5 shadow-xs animate-fadeIn" x-data="{ copied: false }">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-bold text-lg shrink-0 shadow-xs">
                                🔗
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-slate-900 text-sm">Enlace del Portal / Pago para el Paciente</h4>
                                <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                                    Se ha enviado una notificación a <strong class="text-slate-900">{{ $email }}</strong>. También puedes compartirle el enlace directo por WhatsApp para que complete su pago.
                                </p>
                            </div>
                        </div>

                        <div class="p-3 rounded-2xl bg-white border border-amber-200/80 flex flex-col sm:flex-row items-center justify-between gap-2.5">
                            <div class="text-xs font-mono text-slate-700 truncate w-full sm:max-w-[240px] bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-200">
                                {{ $generatedPaymentLink }}
                            </div>
                            <div class="flex items-center gap-2 w-full sm:w-auto">
                                <button type="button" @click="navigator.clipboard.writeText('{{ $generatedPaymentLink }}'); copied = true; setTimeout(() => copied = false, 2500)" class="flex-1 sm:flex-none px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition-colors cursor-pointer inline-flex items-center justify-center gap-1.5 shrink-0">
                                    <span x-show="!copied">📋 Copiar</span>
                                    <span x-show="copied" class="text-emerald-600 font-bold">✓ Copiado</span>
                                </button>
                                @php
                                    $waPhone = preg_replace('/[^0-9]/', '', $phone);
                                    if (str_starts_with($waPhone, '0')) {
                                        $waPhone = '593' . substr($waPhone, 1);
                                    }
                                    $waMsg = rawurlencode("Hola {$firstName} {$lastName}, tu cita médica con {$selectedDoctor->user->name} ha sido reservada para el {$selectedDate} a las {$selectedTime}. Para completar el pago y confirmación de tu cita, por favor ingresa al siguiente enlace: " . $generatedPaymentLink);
                                @endphp
                                <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}" target="_blank" class="flex-1 sm:flex-none px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-colors inline-flex items-center justify-center gap-1.5 shrink-0">
                                    <span>💬 WhatsApp</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @elseif($paymentMethod === 'PAYMENT_LINK')
                    <div class="mt-6 max-w-lg mx-auto p-5 sm:p-6 rounded-3xl bg-purple-50/90 border-2 border-purple-200 text-left space-y-4 shadow-sm animate-fadeIn">
                        <div class="flex items-start gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-purple-600 text-white flex items-center justify-center font-bold text-xl shrink-0 shadow-md shadow-purple-600/20">
                                🔗
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-purple-950 text-base">Enlace DataLink en Gestión</h4>
                                    <span class="inline-flex items-center text-[10px] font-black uppercase bg-amber-100 text-amber-900 border border-amber-300 px-2 py-0.5 rounded-md">
                                        Seguimiento Activo
                                    </span>
                                </div>
                                <p class="text-xs text-purple-900 mt-1 leading-relaxed">
                                    Tu cita quedó reservada con éxito. Nuestro personal de recepción y admisiones ha recibido la alerta y está preparando tu <strong>Enlace de Pago DataLink personalizado</strong> a través de Datafast.
                                </p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-white border border-purple-100 space-y-2 text-xs text-slate-700">
                            <div class="flex items-center justify-between py-0.5">
                                <span class="text-slate-500">Notificación al paciente:</span>
                                <strong class="font-semibold text-slate-900 font-mono">{{ $email }}</strong>
                            </div>
                            <div class="flex items-center justify-between py-0.5">
                                <span class="text-slate-500">Contacto WhatsApp:</span>
                                <strong class="font-semibold text-slate-900 font-mono">{{ $phone }}</strong>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="text-slate-500">Estado de turno:</span>
                                <span class="font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-md text-[11px]">Reservado — Esperando Pago DataLink</span>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center gap-2 pt-1">
                            @php
                                $clinicPhone = config('branding.phone', '593987654321');
                                $clinicWa = preg_replace('/[^0-9]/', '', $clinicPhone);
                                if (str_starts_with($clinicWa, '0')) { $clinicWa = '593' . substr($clinicWa, 1); }
                                $patientWaMsg = rawurlencode("Hola recepción SurHospital, acabé de reservar mi cita médica (#{$confirmedAppointmentCode}) y seleccioné Enlace de Pago DataLink. ¿Me podrían ayudar por favor con el link para realizar el pago?");
                            @endphp
                            <a href="https://wa.me/{{ $clinicWa }}?text={{ $patientWaMsg }}" target="_blank" class="w-full sm:w-auto flex-1 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition-colors text-center inline-flex items-center justify-center gap-2 cursor-pointer">
                                <span>💬 Consultar a Recepción por WhatsApp</span>
                            </a>
                            <button type="button" wire:click="resendPaymentLinkEmail" wire:loading.attr="disabled" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-purple-100 hover:bg-purple-200 text-purple-900 font-bold text-xs transition-colors cursor-pointer text-center inline-flex items-center justify-center gap-1.5">
                                <span wire:loading.remove wire:target="resendPaymentLinkEmail">📩 Reenviar Alerta</span>
                                <span wire:loading wire:target="resendPaymentLinkEmail">Enviando...</span>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Navigation CTAs -->
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                    @if($isManual)
                        <button type="button" wire:click="resetWizard" class="w-full sm:w-auto px-5 py-3 rounded-xl font-bold text-xs sm:text-sm text-brand-primary bg-brand-soft hover:bg-brand-primary hover:text-white transition-all cursor-pointer inline-flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>Agendar Otra Cita</span>
                        </button>
                        <a href="{{ route('admin.appointments') }}" class="w-full sm:w-auto px-5 py-3 rounded-xl font-bold text-xs sm:text-sm text-slate-700 bg-slate-100 hover:bg-slate-200 transition-all text-center">
                            Ver en Gestión de Citas
                        </a>
                        <button type="button" x-on:click="$dispatch('close-modal', 'quick-appointment-modal'); window.location.reload()" class="w-full sm:w-auto px-6 py-3 rounded-xl font-bold text-xs sm:text-sm text-white bg-slate-900 hover:bg-slate-800 transition-all shadow-md cursor-pointer">
                            Listo / Cerrar
                        </button>
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
