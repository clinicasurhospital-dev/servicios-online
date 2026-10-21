@extends('layouts.app')

@section('title', 'Registro de Paciente — ' . config('branding.app_name'))

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50/60"
     x-data="{
        cedula: '{{ old('identification_number', '') }}',
        firstName: '{{ old('first_name', '') }}',
        lastName: '{{ old('last_name', '') }}',
        birthDate: '{{ old('birth_date', '') }}',
        gender: '{{ old('gender', 'OTHER') }}',
        email: '{{ old('email', '') }}',
        phone: '{{ old('phone', '') }}',
        password: '',
        passwordConfirmation: '',
        dataConsent: {{ old('data_consent') ? 'true' : 'false' }},
        
        showPassword: false,
        showConsentModal: false,
        loadingCedula: false,
        cedulaStatus: null, // null, 'valid', 'invalid'
        cedulaMessage: '',
        sourceTag: '',

        async checkCedula() {
            const clean = this.cedula.trim().replace(/\D/g, '');
            this.cedula = clean;

            if (clean.length === 0) {
                this.cedulaStatus = null;
                this.cedulaMessage = '';
                return;
            }

            if (clean.length !== 10) {
                this.cedulaStatus = 'invalid';
                this.cedulaMessage = 'La cédula debe contener exactamente 10 dígitos numéricos (' + clean.length + '/10).';
                return;
            }

            this.loadingCedula = true;
            this.cedulaStatus = null;
            this.cedulaMessage = 'Consultando en Registro Civil / Base de Datos...';

            try {
                const response = await fetch('/consultar-cedula/' + encodeURIComponent(clean), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();

                if (response.ok && data.is_valid) {
                    this.cedulaStatus = 'valid';
                    this.sourceTag = data.source || 'REGISTRO_CIVIL';
                    this.cedulaMessage = data.message || '✓ Identidad verificada con éxito.';

                    if (data.first_name) {
                        this.firstName = data.first_name;
                    }
                    if (data.last_name) {
                        this.lastName = data.last_name;
                    }
                    if (data.birth_date) {
                        this.birthDate = data.birth_date;
                    }
                    if (data.gender && data.gender !== 'OTHER') {
                        this.gender = data.gender;
                    }
                    if (data.phone && !this.phone) {
                        this.phone = data.phone;
                    }
                    if (data.email && !this.email) {
                        this.email = data.email;
                    }

                    // Focus next field if empty
                    this.$nextTick(() => {
                        if (!this.email && this.$refs.emailInput) {
                            this.$refs.emailInput.focus();
                        }
                    });
                } else {
                    this.cedulaStatus = 'invalid';
                    this.cedulaMessage = data.error || 'El número de cédula ingresado no es válido.';
                }
            } catch (err) {
                this.cedulaStatus = 'invalid';
                this.cedulaMessage = 'Error de conexión al validar la cédula. Por favor verifica los 10 dígitos.';
            } finally {
                this.loadingCedula = false;
            }
        }
     }"
     x-init="if (cedula.length === 10) checkCedula()">

    <div class="max-w-xl w-full space-y-6 bg-white p-6 sm:p-10 rounded-3xl shadow-2xl shadow-slate-200/50 border border-slate-100 animate-fadeIn">
        
        <!-- Header -->
        <div class="text-center">
            <a href="{{ route('home') }}" class="inline-block mb-3 transition-transform hover:scale-105">
                <x-brand.logo height="40" />
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Registro de Paciente</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">Ingresa tu cédula de identidad para autocompletar tus datos y crear tu portal médico</p>
        </div>

        {{-- Global Validation Errors --}}
        @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1 shadow-xs animate-fadeIn">
                <div class="flex items-center gap-2 text-rose-900 font-bold mb-1">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Por favor verifica los siguientes campos:</span>
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="space-y-4" action="{{ route('register') }}" method="POST">
            @csrf

            <!-- 1. CÉDULA DE IDENTIDAD (VALIDACIÓN Y REGISTRO CIVIL) -->
            <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/70 space-y-2">
                <div class="flex items-center justify-between">
                    <label for="identification_number" class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                        1. Cédula de Identidad *
                    </label>
                    <span class="text-[11px] font-semibold text-emerald-600" x-show="cedulaStatus === 'valid'">✓ Cédula verificada</span>
                </div>

                <div class="relative">
                    <input
                        type="text"
                        id="identification_number"
                        name="identification_number"
                        x-model="cedula"
                        @input="cedula = cedula.replace(/\D/g, '').slice(0, 10); if(cedula.length === 10) checkCedula(); else if(cedula.length > 0 && cedula.length < 10) { cedulaStatus = null; cedulaMessage = ''; }"
                        @blur="if(cedula.length > 0) checkCedula()"
                        maxlength="10"
                        required
                        class="w-full rounded-xl border-slate-200 text-base font-mono font-bold p-3 pr-12 focus:border-brand-primary focus:ring-brand-primary tracking-widest bg-white shadow-xs"
                        placeholder="0912345678"
                        autocomplete="off"
                    />

                    <!-- Status Icon / Loading Spinner -->
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <template x-if="loadingCedula">
                            <svg class="animate-spin h-5 w-5 text-brand-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </template>
                        <template x-if="!loadingCedula && cedulaStatus === 'valid'">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-xs">
                                ✓
                            </div>
                        </template>
                        <template x-if="!loadingCedula && cedulaStatus === 'invalid'">
                            <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-xs">
                                ✕
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Live Feedback Message -->
                <div x-show="cedulaMessage" x-transition class="text-xs font-semibold flex items-center gap-1.5"
                     :class="cedulaStatus === 'valid' ? 'text-emerald-700' : (cedulaStatus === 'invalid' ? 'text-rose-600' : 'text-slate-500')">
                    <span x-show="loadingCedula">⏳</span>
                    <span x-show="!loadingCedula && cedulaStatus === 'valid'">✓</span>
                    <span x-show="!loadingCedula && cedulaStatus === 'invalid'">⚠️</span>
                    <span x-text="cedulaMessage"></span>
                </div>
            </div>

            <!-- 2. NOMBRES Y APELLIDOS (AUTOCOMPLETADOS DESDE CÉDULA) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-xs font-semibold text-slate-700 mb-1">Nombres *</label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        x-model="firstName"
                        required
                        class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-white shadow-xs"
                        placeholder="Ej: Juan Carlos"
                    />
                </div>
                <div>
                    <label for="last_name" class="block text-xs font-semibold text-slate-700 mb-1">Apellidos *</label>
                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        x-model="lastName"
                        required
                        class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-white shadow-xs"
                        placeholder="Ej: Pérez Rodríguez"
                    />
                </div>
            </div>

            <!-- 3. FECHA DE NACIMIENTO Y GÉNERO -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="birth_date" class="block text-xs font-semibold text-slate-700 mb-1">Fecha de Nacimiento</label>
                    <input
                        type="date"
                        id="birth_date"
                        name="birth_date"
                        x-model="birthDate"
                        max="{{ now()->format('Y-m-d') }}"
                        class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-white shadow-xs text-slate-700"
                    />
                </div>
                <div>
                    <label for="gender" class="block text-xs font-semibold text-slate-700 mb-1">Género</label>
                    <select
                        id="gender"
                        name="gender"
                        x-model="gender"
                        class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-white shadow-xs text-slate-700"
                    >
                        <option value="MALE">Masculino</option>
                        <option value="FEMALE">Femenino</option>
                        <option value="OTHER">Otro / No especificado</option>
                    </select>
                </div>
            </div>

            <!-- 4. CORREO ELECTRÓNICO -->
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Correo Electrónico *</label>
                <div class="relative">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        x-ref="emailInput"
                        x-model="email"
                        required
                        class="w-full rounded-xl border-slate-200 text-sm p-3 pl-10 focus:border-brand-primary focus:ring-brand-primary bg-white shadow-xs"
                        placeholder="ejemplo@correo.com"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Recibirás confirmaciones y recordatorios de tus citas médicas en este correo.</p>
            </div>

            <!-- 5. TELÉFONO MÓVIL -->
            <div>
                <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1">Teléfono Móvil (WhatsApp) *</label>
                <div class="relative">
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        x-model="phone"
                        required
                        class="w-full rounded-xl border-slate-200 text-sm p-3 pl-10 focus:border-brand-primary focus:ring-brand-primary bg-white shadow-xs"
                        placeholder="0991234567"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- 6. CONTRASEÑA Y CONFIRMACIÓN -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Contraseña *</label>
                    <div class="relative">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            name="password"
                            x-model="password"
                            required
                            minlength="8"
                            class="w-full rounded-xl border-slate-200 text-sm p-3 pr-10 focus:border-brand-primary focus:ring-brand-primary bg-white shadow-xs"
                            placeholder="Mínimo 8 caracteres"
                        />
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!showPassword"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="showPassword" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Confirmar Contraseña *</label>
                    <input
                        :type="showPassword ? 'text' : 'password'"
                        id="password_confirmation"
                        name="password_confirmation"
                        x-model="passwordConfirmation"
                        required
                        class="w-full rounded-xl border-slate-200 text-sm p-3 focus:border-brand-primary focus:ring-brand-primary bg-white shadow-xs"
                        placeholder="Repite tu contraseña"
                    />
                </div>
            </div>

            <!-- 7. CHECKBOX DE PROTECCIÓN DE USO DE DATOS PERSONALES (LOPDP) -->
            <div class="p-4 rounded-2xl bg-brand-soft/30 border border-brand-primary/20 space-y-2">
                <label class="flex items-start gap-3 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="data_consent"
                        value="1"
                        x-model="dataConsent"
                        required
                        class="mt-1 h-5 w-5 rounded-lg border-slate-300 text-brand-primary focus:ring-brand-primary focus:ring-offset-0 cursor-pointer shrink-0"
                    />
                    <span class="text-xs text-slate-700 leading-relaxed">
                        Declaro que he leído y autorizo el tratamiento de mis datos personales y clínicos conforme a la 
                        <strong>Ley Orgánica de Protección de Datos Personales (LOPDP)</strong> del Ecuador y la 
                        <button type="button" @click="showConsentModal = true" class="text-brand-primary underline font-bold hover:text-brand-primary-dark">
                            Política de Privacidad y Consentimiento Informado
                        </button> de SurHospital.
                    </span>
                </label>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                :disabled="!dataConsent || loadingCedula || cedulaStatus === 'invalid'"
                class="w-full mt-2 py-4 px-6 rounded-2xl bg-brand-primary hover:bg-brand-primary-dark disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-black text-sm sm:text-base shadow-lg shadow-brand-primary/25 transition-all transform active:scale-[0.99] flex items-center justify-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span>Crear Mi Cuenta de Paciente</span>
            </button>
        </form>

        <!-- Footer -->
        <div class="pt-2 text-center text-xs text-slate-500">
            ¿Ya tienes una cuenta registrada?
            <a href="{{ route('login') }}" class="font-bold text-brand-primary hover:underline ml-1">Inicia sesión aquí</a>
        </div>
    </div>

    <!-- MODAL DE POLÍTICA DE PROTECCIÓN DE DATOS PERSONALES (LOPDP) -->
    <div x-show="showConsentModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl space-y-4 max-h-[85vh] flex flex-col border border-slate-100 animate-scaleUp"
             @click.outside="showConsentModal = false">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold">
                        ⚖️
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Política de Protección de Datos Personales</h3>
                        <p class="text-[11px] text-slate-500">Conforme a la Ley Orgánica de Protección de Datos Personales del Ecuador (LOPDP)</p>
                    </div>
                </div>
                <button @click="showConsentModal = false" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="overflow-y-auto custom-scrollbar flex-1 pr-2 space-y-3 text-xs text-slate-600 leading-relaxed">
                <div class="p-3 rounded-xl bg-blue-50/70 border border-blue-100 text-blue-900">
                    <p class="font-bold mb-1">🔒 Compromiso de Confidencialidad y Secreto Médico</p>
                    <p class="text-[11px]">En <strong>SurHospital</strong> tratamos tus datos de salud y personales con estricta confidencialidad bajo los estándares médicos y la legislación ecuatoriana vigente.</p>
                </div>

                <h4 class="font-bold text-slate-800 text-xs">1. Finalidad del Tratamiento</h4>
                <p>Los datos personales (cédula, nombres, fecha de nacimiento, contacto) y los datos sensibles de salud recabados serán utilizados exclusivamente para:</p>
                <ul class="list-disc list-inside pl-2 space-y-1 text-[11px]">
                    <li>Gestión y reserva de consultas y citas médicas.</li>
                    <li>Apertura y mantenimiento de tu Historia Clínica Electrónica (EHR).</li>
                    <li>Envío de confirmaciones, recordatorios de citas y comprobantes de pago.</li>
                    <li>Emisión de prescripciones y resultados de exámenes o procedimientos.</li>
                </ul>

                <h4 class="font-bold text-slate-800 text-xs">2. Base Legal</h4>
                <p>El tratamiento se fundamenta en tu consentimiento libre, previo, explícito e informado, así como en la Ley Orgánica de Protección de Datos Personales y la Ley Orgánica de Salud de la República del Ecuador.</p>

                <h4 class="font-bold text-slate-800 text-xs">3. Derechos del Titular (Derechos ARCO)</h4>
                <p>Como titular de los datos, tienes derecho a solicitar en cualquier momento el <strong>Acceso, Rectificación, Cancelación, Oposición y Portabilidad</strong> de tu información, comunicándote con nuestro departamento de administración.</p>

                <h4 class="font-bold text-slate-800 text-xs">4. Seguridad y Registro</h4>
                <p>Tu aceptación queda registrada con fecha, hora y dirección IP de origen para garantizar la trazabilidad legal y seguridad de tu expediente.</p>
            </div>

            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                <a href="https://www.abogadoslatam.com/politicas/surhospital/salud" target="_blank" rel="noopener noreferrer" class="text-xs text-brand-primary font-bold hover:underline inline-flex items-center gap-1">
                    Ver documento legal en línea
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" @click="showConsentModal = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors">
                        Cerrar
                    </button>
                    <button type="button" @click="dataConsent = true; showConsentModal = false" class="px-5 py-2.5 rounded-xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 transition-all">
                        Acepto la Política y Términos
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
