@extends('layouts.admin')

@section('title', 'Sugerencias y Valoraciones Médicas — ' . config('branding.app_name'))

@section('content')
<div class="space-y-8 animate-fadeIn" x-data="{
    inviteModal: false,
    selectedAppointmentCode: '',
    selectedAppointmentId: '',
    patientName: '',
    patientPhone: '',
    whatsappUrl: '',
    reviewUrl: '',
    copySuccess: false,
    sendingEmail: false,
    emailSentMessage: '',
    openInviteModal(appId, appCode, pName, pPhone, waUrl, revUrl) {
        this.selectedAppointmentId = appId;
        this.selectedAppointmentCode = appCode;
        this.patientName = pName;
        this.patientPhone = pPhone;
        this.whatsappUrl = waUrl;
        this.reviewUrl = revUrl;
        this.copySuccess = false;
        this.emailSentMessage = '';
        this.inviteModal = true;
    },
    copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            this.copySuccess = true;
            setTimeout(() => this.copySuccess = false, 2500);
        });
    },
    sendEmail(appId) {
        this.sendingEmail = true;
        this.emailSentMessage = '';
        fetch(`{{ url('admin/citas') }}/${appId}/enviar-invitacion-valoracion`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ channel: 'EMAIL' })
        })
        .then(res => res.json())
        .then(data => {
            this.sendingEmail = false;
            this.emailSentMessage = data.message || 'Invitación enviada por correo.';
        })
        .catch(err => {
            this.sendingEmail = false;
            this.emailSentMessage = 'Error al enviar el correo.';
        });
    }
}">
    <!-- Page Title & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-amber-100 text-amber-800 border border-amber-200">
                    ⭐ Calidad & Experiencia
                </span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 mt-1">Sugerencias y Valoraciones del Médico</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Supervisión del índice de satisfacción del paciente, comentarios, sugerencias y valoraciones por especialista.
            </p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.appointments') }}" class="px-4 py-2.5 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span>Ver Citas Atendidas</span>
            </a>
        </div>
    </div>

    <!-- METRICS & KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        
        <!-- Card 1: Overall Average Rating -->
        <div class="bg-gradient-to-br from-amber-500 via-amber-600 to-amber-700 text-white rounded-3xl p-6 shadow-xl shadow-amber-500/20 relative overflow-hidden flex flex-col justify-between">
            <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-white/10 rounded-full blur-xl"></div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-100">Índice General</span>
                <div class="flex items-baseline space-x-2 mt-2">
                    <span class="text-4xl font-black">{{ number_format($stats['average_rating'], 1) }}</span>
                    <span class="text-base font-bold text-amber-200">/ 5.0</span>
                </div>
                <div class="flex items-center space-x-1 mt-1 text-amber-200 text-lg">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($stats['average_rating']))
                            <span>★</span>
                        @else
                            <span class="text-amber-300/40">★</span>
                        @endif
                    @endfor
                </div>
            </div>
            <p class="text-[11px] text-amber-100 mt-4 pt-3 border-t border-white/20">
                Basado en <strong>{{ $stats['total_reviews'] }}</strong> opiniones registradas.
            </p>
        </div>

        <!-- Card 2: Total Reviews & Comments -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-200/50 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Evaluaciones</span>
                    <div class="w-8 h-8 rounded-xl bg-brand-soft text-brand-primary flex items-center justify-center font-bold text-sm">
                        💬
                    </div>
                </div>
                <div class="flex items-baseline space-x-2 mt-2">
                    <span class="text-3xl font-black text-slate-900">{{ $stats['total_reviews'] }}</span>
                    <span class="text-xs font-bold text-slate-400">recibidas</span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    <strong class="text-brand-primary">{{ $stats['total_comments'] }}</strong> incluyen comentarios o sugerencias de mejora.
                </p>
            </div>
            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                <span>Canales:</span>
                <span class="font-bold text-slate-700">WhatsApp &bull; Portal &bull; Correo</span>
            </div>
        </div>

        <!-- Card 3: Categorías Específicas -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-200/50 space-y-3">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Criterios de Atención</span>
            
            <div class="space-y-2 text-xs">
                <div>
                    <div class="flex justify-between text-slate-700 font-semibold mb-1">
                        <span>Trato y Empatía:</span>
                        <span class="font-black text-slate-900">{{ number_format($stats['avg_attention'], 1) }} ★</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ ($stats['avg_attention'] / 5) * 100 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-slate-700 font-semibold mb-1">
                        <span>Puntualidad:</span>
                        <span class="font-black text-slate-900">{{ number_format($stats['avg_punctuality'], 1) }} ★</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ ($stats['avg_punctuality'] / 5) * 100 }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-slate-700 font-semibold mb-1">
                        <span>Instalaciones:</span>
                        <span class="font-black text-slate-900">{{ number_format($stats['avg_facilities'], 1) }} ★</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ ($stats['avg_facilities'] / 5) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Desglose por Estrellas -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-200/50 space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Desglose de Calificaciones</span>
            
            <div class="space-y-1.5 text-[11px]">
                @for($star = 5; $star >= 1; $star--)
                    <div class="flex items-center space-x-2">
                        <span class="w-4 font-bold text-slate-700 text-right">{{ $star }}★</span>
                        <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-amber-400 h-2 rounded-full transition-all" style="width: {{ $stats['star_percentages'][$star] ?? 0 }}%"></div>
                        </div>
                        <span class="w-8 text-[10px] font-semibold text-slate-400 text-right">{{ $stats['star_counts'][$star] ?? 0 }}</span>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- TOP DOCTORS BANNER (Si hay varios) -->
    @if(!$isDoctor && $stats['top_doctors']->isNotEmpty())
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="text-base">🏆</span>
                    <h3 class="text-sm font-black text-slate-900">Especialistas Destacados por los Pacientes</h3>
                </div>
                <span class="text-[11px] text-slate-400">Mejores promedios acumulados</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
                @foreach($stats['top_doctors'] as $index => $topDoc)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center space-x-3 hover:border-brand-primary/30 transition-colors">
                        <div class="w-10 h-10 rounded-xl {{ $index === 0 ? 'bg-amber-100 text-amber-800 font-black' : ($index === 1 ? 'bg-slate-200 text-slate-700 font-bold' : 'bg-brand-soft text-brand-primary font-bold') }} flex items-center justify-center text-xs shrink-0">
                            #{{ $index + 1 }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-bold text-slate-900 text-xs truncate">{{ $topDoc->name }}</h4>
                            <p class="text-[10px] text-slate-500 truncate">{{ $topDoc->primary_specialty_name ?? 'Médico' }}</p>
                            <div class="flex items-center space-x-1 mt-0.5 text-[11px]">
                                <span class="text-amber-500 font-black">★ {{ number_format($topDoc->avg_rating ?: 5.0, 1) }}</span>
                                <span class="text-[10px] text-slate-400">({{ $topDoc->total_reviews }})</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- FILTERS BAR -->
    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
        <form method="GET" action="{{ route('admin.reviews') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <!-- Search Text -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Buscar en sugerencias / nombres</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Ej: excelente, doctor, #SUR-..."
                    class="w-full rounded-2xl border-slate-200 text-xs p-2.5 focus:border-brand-primary focus:ring-brand-primary shadow-2xs"
                />
            </div>

            <!-- Doctor Filter (Only if not doctor) -->
            @if(!$isDoctor)
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Médico Especialista</label>
                    <select name="doctor_id" class="w-full rounded-2xl border-slate-200 text-xs p-2.5 focus:border-brand-primary focus:ring-brand-primary shadow-2xs">
                        <option value="">Todos los médicos</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" {{ $doctorId == $doc->id ? 'selected' : '' }}>
                                {{ $doc->name }} ({{ $doc->primary_specialty_name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Star Rating Filter -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Calificación de Estrellas</label>
                <select name="rating" class="w-full rounded-2xl border-slate-200 text-xs p-2.5 focus:border-brand-primary focus:ring-brand-primary shadow-2xs">
                    <option value="">Todas las estrellas</option>
                    <option value="5" {{ $rating == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5 estrellas)</option>
                    <option value="4" {{ $rating == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4 estrellas)</option>
                    <option value="3" {{ $rating == '3' ? 'selected' : '' }}>⭐⭐⭐ (3 estrellas)</option>
                    <option value="2" {{ $rating == '2' ? 'selected' : '' }}>⭐⭐ (2 estrellas)</option>
                    <option value="1" {{ $rating == '1' ? 'selected' : '' }}>⭐ (1 estrella)</option>
                </select>
            </div>

            <!-- Channel Filter -->
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1">Canal de Origen</label>
                <select name="channel" class="w-full rounded-2xl border-slate-200 text-xs p-2.5 focus:border-brand-primary focus:ring-brand-primary shadow-2xs">
                    <option value="">Todos los canales</option>
                    <option value="WHATSAPP" {{ strtoupper($channel) == 'WHATSAPP' ? 'selected' : '' }}>📲 WhatsApp</option>
                    <option value="EMAIL" {{ strtoupper($channel) == 'EMAIL' ? 'selected' : '' }}>✉️ Correo Electrónico</option>
                    <option value="PORTAL" {{ strtoupper($channel) == 'PORTAL' ? 'selected' : '' }}>💻 Portal del Paciente</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-2xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 transition-all">
                    Filtrar
                </button>
                @if($search || $doctorId || $rating || $channel || $dateFrom || $dateTo)
                    <a href="{{ route('admin.reviews') }}" class="py-2.5 px-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition-colors" title="Limpiar Filtros">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- REVIEWS AND SUGGESTIONS LIST -->
    <div class="space-y-4">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-base font-black text-slate-900">Listado de Valoraciones y Sugerencias Recibidas</h2>
            <span class="text-xs text-slate-400">Total: {{ $reviews->total() }} registros</span>
        </div>

        @if($reviews->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
                @foreach($reviews as $rev)
                    <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-200/40 hover:shadow-2xl hover:border-slate-200 transition-all flex flex-col justify-between space-y-4 relative overflow-hidden">
                        
                        <!-- Top Row: Doctor Info & Rating Badge -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-brand-primary to-blue-600 text-white flex items-center justify-center font-black text-sm shadow-xs shrink-0">
                                    {{ substr($rev->doctor?->user?->name ?? 'Dr', 0, 2) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-black text-slate-900 text-sm truncate leading-tight">{{ $rev->doctor?->user?->name }}</h3>
                                    <p class="text-[11px] text-slate-500 truncate">{{ $rev->appointment?->specialty?->name ?? $rev->doctor?->primary_specialty_name }}</p>
                                </div>
                            </div>

                            <!-- Rating Big Badge -->
                            <div class="px-3 py-1.5 rounded-2xl {{ $rev->rating >= 4 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($rev->rating === 3 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200') }} flex items-center space-x-1 shrink-0 font-black text-xs">
                                <span>★</span>
                                <span>{{ $rev->rating }}.0</span>
                            </div>
                        </div>

                        <!-- Sub-Ratings Row -->
                        @if($rev->attention_rating || $rev->punctuality_rating || $rev->facilities_rating)
                            <div class="grid grid-cols-3 gap-1.5 bg-slate-50/80 p-2.5 rounded-2xl text-[10px] text-slate-600 text-center">
                                <div>
                                    <span class="text-slate-400 block text-[9px]">Trato:</span>
                                    <span class="font-bold text-slate-800">{{ $rev->attention_rating ?? '-' }}/5 ★</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[9px]">Puntualidad:</span>
                                    <span class="font-bold text-slate-800">{{ $rev->punctuality_rating ?? '-' }}/5 ★</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[9px]">Instalaciones:</span>
                                    <span class="font-bold text-slate-800">{{ $rev->facilities_rating ?? '-' }}/5 ★</span>
                                </div>
                            </div>
                        @endif

                        <!-- Comment / Suggestion Text Box -->
                        @if($rev->comment)
                            <div class="bg-blue-50/40 p-3.5 rounded-2xl border border-blue-100/60 text-xs text-slate-800 space-y-1">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-brand-primary flex items-center gap-1">
                                    <span>✍️</span> Sugerencia / Opinión del Paciente:
                                </span>
                                <p class="italic leading-relaxed">
                                    "{{ $rev->comment }}"
                                </p>
                            </div>
                        @else
                            <div class="p-3 rounded-2xl bg-slate-50/50 border border-slate-100 text-center text-[11px] text-slate-400 italic">
                                Sin sugerencia de texto añadida
                            </div>
                        @endif

                        <!-- Bottom Footer Info: Patient, Channel, Date & Code -->
                        <div class="pt-3 border-t border-slate-100 flex flex-col space-y-2 text-[11px]">
                            <div class="flex items-center justify-between text-slate-500">
                                <div class="flex items-center space-x-1.5 font-semibold">
                                    <span>👤</span>
                                    <span class="{{ $rev->is_anonymous ? 'italic text-slate-400' : 'text-slate-700' }}">
                                        {{ $rev->patient_display_name }}
                                    </span>
                                </div>

                                <!-- Channel Badge -->
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase {{ $rev->channel === 'WHATSAPP' ? 'bg-emerald-100 text-emerald-800' : ($rev->channel === 'EMAIL' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700') }}">
                                    @if($rev->channel === 'WHATSAPP')
                                        📲 WhatsApp
                                    @elseif($rev->channel === 'EMAIL')
                                        ✉️ Correo
                                    @else
                                        💻 Portal
                                    @endif
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-[10px] text-slate-400">
                                <span>Cita: <strong class="font-mono text-slate-600">#{{ $rev->appointment?->code ?? 'N/A' }}</strong></span>
                                <span>{{ $rev->submitted_at ? $rev->submitted_at->translatedFormat('d M Y, H:i') : $rev->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        @else
            <x-empty-state
                title="No se encontraron sugerencias o valoraciones"
                description="Cuando los pacientes califiquen sus citas médicas o envíen sugerencias desde el portal o los enlaces de WhatsApp y correo, aparecerán organizadas en este panel."
                action-text="Ver Gestión de Citas"
                action-url="{{ route('admin.appointments') }}"
            />
        @endif
    </div>
</div>
@endsection
