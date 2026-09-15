<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6">
    <!-- Stepper Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between relative">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-slate-200 w-full z-0"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-brand-primary transition-all duration-500 z-0" style="width: {{ (($step - 1) / 6) * 100 }}%"></div>

            @php
                $steps = [
                    1 => 'Especialidad',
                    2 => 'Servicio',
                    3 => 'Médico',
                    4 => 'Horario',
                    5 => 'Paciente',
                    6 => 'Pago',
                    7 => 'Confirmación',
                ];
            @endphp

            @foreach($steps as $num => $title)
                <div class="relative z-10 flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all duration-300 {{ $step >= $num ? 'bg-brand-primary text-white shadow-md shadow-brand-primary/30 ring-4 ring-white' : 'bg-slate-200 text-slate-500 ring-4 ring-white' }}">
                        @if($step > $num)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        @else
                            {{ $num }}
                        @endif
                    </div>
                    <span class="hidden md:block text-xs font-semibold mt-2 {{ $step >= $num ? 'text-brand-primary' : 'text-slate-400' }}">{{ $title }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Error Display -->
    @if($errorMessage)
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start space-x-3 text-rose-800 animate-fadeIn">
            <svg class="w-5 h-5 mt-0.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="text-sm font-medium">{{ $errorMessage }}</div>
        </div>
    @endif

    <!-- Wizard Card -->
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
        
        <!-- STEP 1: ESPECIALIDAD -->
        @if($step === 1)
            <div class="p-6 md:p-8 animate-fadeIn">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-slate-900">1. Selecciona la Especialidad Médica</h2>
                    <p class="text-slate-500 text-sm mt-1">Elige el área de atención que requieres para tu consulta.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @forelse($specialties as $specialty)
                        <button type="button" wire:click="selectSpecialty({{ $specialty->id }})" class="p-5 rounded-2xl border-2 text-left transition-all duration-200 flex flex-col justify-between hover:border-brand-primary hover:shadow-lg hover:shadow-brand-primary/10 group {{ $specialtyId === $specialty->id ? 'border-brand-primary bg-brand-soft/40' : 'border-slate-100 bg-slate-50/50' }}">
                            <div>
                                <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-brand-primary mb-4 group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                </div>
                                <h3 class="font-bold text-slate-800 group-hover:text-brand-primary transition-colors text-base">{{ $specialty->name }}</h3>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $specialty->description }}</p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs">
                                <span class="text-slate-500">{{ $specialty->doctors_count }} médicos</span>
                                <span class="font-bold text-brand-primary">${{ number_format($specialty->base_price, 0) }}</span>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full text-center py-12 text-slate-400">No hay especialidades disponibles en este momento.</div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- STEP 2: SERVICIO / PROCEDIMIENTO -->
        @if($step === 2)
            <div class="p-6 md:p-8 animate-fadeIn">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">2. Tipo de Consulta o Procedimiento</h2>
                        <p class="text-slate-500 text-sm mt-1">Especialidad: <span class="font-semibold text-brand-primary">{{ $selectedSpecialty->name }}</span></p>
                    </div>
                    <button wire:click="previousStep" class="text-sm font-semibold text-slate-500 hover:text-slate-800 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        <span>Atrás</span>
                    </button>
                </div>

                <div class="space-y-3">
                    <!-- Default General Consultation Option -->
                    <button type="button" wire:click="selectProcedure(0)" class="w-full p-5 rounded-2xl border-2 text-left transition-all duration-200 flex items-center justify-between hover:border-brand-primary hover:bg-brand-soft/20 {{ is_null($procedureId) || $procedureId === 0 ? 'border-brand-primary bg-brand-soft/40' : 'border-slate-100 bg-slate-50/50' }}">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-brand-primary shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800">Consulta Médica General / Especializada</h4>
                                <p class="text-xs text-slate-500">Valoración clínica regular ({{ $selectedSpecialty->default_duration_minutes ?? 30 }} min)</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block font-bold text-brand-primary text-base">${{ number_format($selectedSpecialty->base_price, 0) }}</span>
                            <span class="text-xs text-slate-400">Tarifa base</span>
                        </div>
                    </button>

                    @foreach($procedures as $proc)
                        <button type="button" wire:click="selectProcedure({{ $proc->id }})" class="w-full p-5 rounded-2xl border-2 text-left transition-all duration-200 flex items-center justify-between hover:border-brand-primary hover:bg-brand-soft/20 {{ $procedureId === $proc->id ? 'border-brand-primary bg-brand-soft/40' : 'border-slate-100 bg-slate-50/50' }}">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-brand-primary shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800">{{ $proc->name }}</h4>
                                    <p class="text-xs text-slate-500">{{ $proc->description ?: 'Procedimiento especializado' }} ({{ $proc->duration_minutes }} min)</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="block font-bold text-brand-primary text-base">${{ number_format($proc->price, 0) }}</span>
                                <span class="text-xs text-slate-400">Total</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- STEP 3: SELECCIONAR MÉDICO -->
        @if($step === 3)
            <div class="p-6 md:p-8 animate-fadeIn">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">3. Selecciona tu Especialista</h2>
                        <p class="text-slate-500 text-sm mt-1">Médicos disponibles para {{ $selectedSpecialty->name }}</p>
                    </div>
                    <button wire:click="previousStep" class="text-sm font-semibold text-slate-500 hover:text-slate-800 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        <span>Atrás</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($doctors as $doc)
                        <button type="button" wire:click="selectDoctor({{ $doc->id }})" class="p-5 rounded-2xl border-2 text-left transition-all duration-200 flex items-start space-x-4 hover:border-brand-primary hover:shadow-lg hover:shadow-brand-primary/10 {{ $doctorId === $doc->id ? 'border-brand-primary bg-brand-soft/40' : 'border-slate-100 bg-slate-50/50' }}">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-brand-primary to-brand-primary-dark text-white flex items-center justify-center font-bold text-xl shadow-md shrink-0">
                                {{ substr($doc->user->name, 0, 2) }}
                            </div>
                            <div class="flex-1">
                                <span class="inline-block px-2 py-0.5 rounded-full text-[11px] font-semibold bg-brand-soft text-brand-primary mb-1">
                                    Reg. {{ $doc->license_number }}
                                </span>
                                <h4 class="font-bold text-slate-900 text-base leading-tight">{{ $doc->user->name }}</h4>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $doc->biography ?: 'Especialista certificado de SurHospital con amplia experiencia clínica.' }}</p>
                                <div class="mt-3 flex items-center space-x-2 text-xs text-emerald-600 font-medium">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Horarios disponibles esta semana</span>
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full text-center py-12 text-slate-400">No hay médicos registrados actualmente para esta especialidad.</div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- STEP 4: FECHA Y TIME SLOTS -->
        @if($step === 4)
            <div class="p-6 md:p-8 animate-fadeIn">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">4. Elige Fecha y Hora</h2>
                        <p class="text-slate-500 text-sm mt-1">Con <span class="font-semibold text-slate-800">{{ $selectedDoctor->user->name }}</span> ({{ $selectedSpecialty->name }})</p>
                    </div>
                    <button wire:click="previousStep" class="text-sm font-semibold text-slate-500 hover:text-slate-800 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        <span>Atrás</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Date Picker Column -->
                    <div class="md:col-span-1 bg-slate-50 p-5 rounded-2xl border border-slate-100">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Fecha de Consulta</label>
                        <input type="date" wire:model.live="selectedDate" min="{{ now()->format('Y-m-d') }}" class="w-full rounded-xl border-slate-200 text-slate-800 font-semibold focus:border-brand-primary focus:ring-brand-primary text-sm p-3 bg-white shadow-sm" />

                        <div class="mt-4 p-3 rounded-xl bg-blue-50 border border-blue-100 text-xs text-blue-800 space-y-1">
                            <p class="font-bold flex items-center space-x-1">
                                <svg class="w-4 h-4 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Información</span>
                            </p>
                            <p>Los turnos son calculados en tiempo real según la agenda del especialista y libres de doble reserva.</p>
                        </div>
                    </div>

                    <!-- Slots Column -->
                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Horarios Disponibles</label>
                            <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d \d\e F') }}</span>
                        </div>

                        @if(count($availableSlots) > 0)
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2.5 max-h-72 overflow-y-auto pr-1">
                                @foreach($availableSlots as $slot)
                                    @if($slot->isAvailable)
                                        <button type="button" wire:click="selectSlot('{{ $slot->startTime }}', '{{ $slot->endTime }}')" class="py-3 px-2 rounded-xl text-center font-bold text-sm transition-all duration-150 {{ $selectedTime === $slot->startTime ? 'bg-brand-primary text-white shadow-md shadow-brand-primary/30 ring-2 ring-brand-primary ring-offset-1' : 'bg-slate-50 text-slate-700 border border-slate-200/80 hover:border-brand-primary hover:bg-brand-soft/30 hover:text-brand-primary' }}">
                                            {{ $slot->startTime }}
                                        </button>
                                    @else
                                        <button disabled class="py-3 px-2 rounded-xl text-center text-xs font-semibold bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed line-through opacity-60">
                                            {{ $slot->startTime }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 px-4 rounded-2xl bg-slate-50 border border-dashed border-slate-200">
                                <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-sm font-semibold text-slate-600">No hay turnos disponibles para esta fecha</p>
                                <p class="text-xs text-slate-400 mt-1">El especialista no atiende en este día o los cupos están completos. Intenta seleccionar otra fecha.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        @if($selectedTime)
                            <span class="text-xs text-slate-500">Horario seleccionado:</span>
                            <span class="block text-base font-bold text-brand-primary">{{ $selectedTime }} - {{ $selectedEndTime }}</span>
                        @endif
                    </div>
                    <button wire:click="nextStep" @if(!$selectedTime) disabled @endif class="px-6 py-3 rounded-xl font-bold text-sm text-white bg-brand-primary hover:bg-brand-primary-dark transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-md shadow-brand-primary/20">
                        Continuar
                    </button>
                </div>
            </div>
        @endif

        <!-- STEP 5: INFORMACIÓN DEL PACIENTE -->
        @if($step === 5)
            <div class="p-6 md:p-8 animate-fadeIn">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">5. Datos del Paciente</h2>
                        <p class="text-slate-500 text-sm mt-1">Ingresa tus datos personales para registrar la historia de la cita.</p>
                    </div>
                    <button wire:click="previousStep" class="text-sm font-semibold text-slate-500 hover:text-slate-800 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        <span>Atrás</span>
                    </button>
                </div>

                @if(Auth::check())
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-emerald-600">Sesión iniciada como:</span>
                                <h4 class="font-bold text-slate-900 text-sm">{{ Auth::user()->name }} ({{ Auth::user()->email }})</h4>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nombres *</label>
                            <input type="text" wire:model="firstName" class="w-full rounded-xl border-slate-200 text-sm p-2.5 focus:border-brand-primary focus:ring-brand-primary" placeholder="Ej: Carlos" />
                            @error('firstName') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Apellidos *</label>
                            <input type="text" wire:model="lastName" class="w-full rounded-xl border-slate-200 text-sm p-2.5 focus:border-brand-primary focus:ring-brand-primary" placeholder="Ej: Perez" />
                            @error('lastName') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tipo de Identificación *</label>
                            <select wire:model="identificationType" class="w-full rounded-xl border-slate-200 text-sm p-2.5 focus:border-brand-primary focus:ring-brand-primary">
                                <option value="CC">Cédula de Ciudadanía (CC)</option>
                                <option value="TI">Tarjeta de Identidad (TI)</option>
                                <option value="CE">Cédula de Extranjería (CE)</option>
                                <option value="PASSPORT">Pasaporte</option>
                                <option value="OTHER">Otro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Número de Documento *</label>
                            <input type="text" wire:model="identificationNumber" class="w-full rounded-xl border-slate-200 text-sm p-2.5 focus:border-brand-primary focus:ring-brand-primary" placeholder="Ej: 1098765432" />
                            @error('identificationNumber') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Correo Electrónico *</label>
                            <input type="email" wire:model="email" class="w-full rounded-xl border-slate-200 text-sm p-2.5 focus:border-brand-primary focus:ring-brand-primary" placeholder="correo@ejemplo.com" />
                            @error('email') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Teléfono / WhatsApp *</label>
                            <input type="tel" wire:model="phone" class="w-full rounded-xl border-slate-200 text-sm p-2.5 focus:border-brand-primary focus:ring-brand-primary" placeholder="3001234567" />
                            @error('phone') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Fecha de Nacimiento *</label>
                            <input type="date" wire:model="birthDate" class="w-full rounded-xl border-slate-200 text-sm p-2.5 focus:border-brand-primary focus:ring-brand-primary" />
                            @error('birthDate') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Género *</label>
                            <select wire:model="gender" class="w-full rounded-xl border-slate-200 text-sm p-2.5 focus:border-brand-primary focus:ring-brand-primary">
                                <option value="MALE">Masculino</option>
                                <option value="FEMALE">Femenino</option>
                                <option value="OTHER">Otro / Prefiero no decir</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Crear Contraseña para tu Portal Paciente *</label>
                            <input type="password" wire:model="password" class="w-full rounded-xl border-slate-200 text-sm p-2.5 focus:border-brand-primary focus:ring-brand-primary" placeholder="Mínimo 8 caracteres" />
                            @error('password') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Motivo de la Consulta / Síntomas (Opcional)</label>
                    <textarea wire:model="reason" rows="2" class="w-full rounded-xl border-slate-200 text-sm p-2.5 focus:border-brand-primary focus:ring-brand-primary" placeholder="Describe brevemente tus síntomas o el motivo del agendamiento..."></textarea>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                    <button wire:click="nextStep" class="px-6 py-3 rounded-xl font-bold text-sm text-white bg-brand-primary hover:bg-brand-primary-dark transition-all shadow-md shadow-brand-primary/20">
                        Ir al Pago
                    </button>
                </div>
            </div>
        @endif

        <!-- STEP 6: PAGO -->
        @if($step === 6)
            <div class="p-6 md:p-8 animate-fadeIn">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">6. Método de Pago</h2>
                        <p class="text-slate-500 text-sm mt-1">Selecciona la forma de pago para formalizar tu reserva médica.</p>
                    </div>
                    <button wire:click="previousStep" class="text-sm font-semibold text-slate-500 hover:text-slate-800 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        <span>Atrás</span>
                    </button>
                </div>

                <!-- Resumen de Orden -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 mb-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Resumen del Agendamiento</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-xs text-slate-400 block">Especialidad:</span>
                            <span class="font-bold text-slate-800">{{ $selectedSpecialty->name }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block">Especialista:</span>
                            <span class="font-bold text-slate-800">{{ $selectedDoctor->user->name }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block">Fecha y Hora:</span>
                            <span class="font-bold text-brand-primary">{{ $selectedDate }} | {{ $selectedTime }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block">Total a Pagar:</span>
                            <span class="text-lg font-black text-brand-primary">${{ number_format($amount, 0) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Opciones de Pago -->
                <div class="space-y-4">
                    <label class="block p-5 rounded-2xl border-2 transition-all cursor-pointer {{ $paymentMethod === 'ONLINE' ? 'border-brand-primary bg-brand-soft/40 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <input type="radio" wire:model="paymentMethod" value="ONLINE" class="text-brand-primary focus:ring-brand-primary h-4 w-4" />
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm">Pago en Línea Inmediato (Tarjeta / PSE / Wompi)</h4>
                                    <p class="text-xs text-slate-500">Confirmación instantánea de tu cita con pasarela segura.</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">Automático</span>
                        </div>
                    </label>

                    <label class="block p-5 rounded-2xl border-2 transition-all cursor-pointer {{ $paymentMethod === 'TRANSFER' ? 'border-brand-primary bg-brand-soft/40 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <input type="radio" wire:model="paymentMethod" value="TRANSFER" class="text-brand-primary focus:ring-brand-primary h-4 w-4" />
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm">Transferencia Bancaria</h4>
                                    <p class="text-xs text-slate-500">Transfiere y adjunta tu comprobante desde el portal para validación de recepción.</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-md">Manual</span>
                        </div>
                    </label>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end">
                    <button wire:click="confirmBooking" wire:loading.attr="disabled" class="px-8 py-3.5 rounded-xl font-bold text-sm text-white bg-brand-primary hover:bg-brand-primary-dark transition-all shadow-lg shadow-brand-primary/30 flex items-center space-x-2">
                        <span wire:loading.remove>Confirmar y Reservar Cita</span>
                        <span wire:loading class="flex items-center space-x-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Procesando reserva segura...</span>
                        </span>
                    </button>
                </div>
            </div>
        @endif

        <!-- STEP 7: CONFIRMACIÓN EXITOSA -->
        @if($step === 7)
            <div class="p-8 md:p-12 text-center animate-fadeIn">
                <div class="w-20 h-20 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-6 shadow-lg shadow-emerald-500/20">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>

                <span class="px-3 py-1 rounded-full text-xs font-bold bg-brand-soft text-brand-primary uppercase tracking-wider">Cita Registrada Exitosamente</span>
                <h2 class="text-3xl font-extrabold text-slate-900 mt-2">¡Tu cita ha sido reservada!</h2>
                <p class="text-slate-500 text-sm mt-2 max-w-md mx-auto">Código de Reserva: <strong class="text-slate-900 font-mono text-base">{{ $confirmedAppointmentCode }}</strong></p>

                <div class="mt-8 max-w-md mx-auto p-5 rounded-2xl bg-slate-50 border border-slate-200/80 text-left text-sm space-y-2">
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Especialidad:</span>
                        <span class="font-bold text-slate-800">{{ $selectedSpecialty->name }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Médico:</span>
                        <span class="font-bold text-slate-800">{{ $selectedDoctor->user->name }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Fecha y Hora:</span>
                        <span class="font-bold text-brand-primary">{{ $selectedDate }} | {{ $selectedTime }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500">Estado de Pago:</span>
                        <span class="font-bold {{ $paymentMethod === 'ONLINE' ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $paymentMethod === 'ONLINE' ? 'Aprobado Online' : 'Pendiente Comprobante' }}
                        </span>
                    </div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('patient.dashboard') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl font-bold text-sm text-white bg-brand-primary hover:bg-brand-primary-dark transition-all shadow-md shadow-brand-primary/20">
                        Ir a Mi Portal de Paciente
                    </a>
                    <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl font-bold text-sm text-slate-700 bg-slate-100 hover:bg-slate-200 transition-all">
                        Volver al Inicio
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>
