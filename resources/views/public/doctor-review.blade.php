@extends('layouts.app')

@section('title', 'Valoración de Consulta Médica — ' . config('branding.client_name', 'Clínica Sur Hospital'))

@section('content')
<div class="min-h-[85vh] py-8 sm:py-14 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
    <div class="max-w-2xl w-full">
        
        <!-- Main Card Container -->
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
            
            <!-- Top Gradient Header Banner -->
            <div class="bg-gradient-to-r from-brand-primary via-indigo-600 to-blue-700 text-white p-6 sm:p-8 relative overflow-hidden text-center">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-brand-primary-dark/30 rounded-full blur-xl"></div>
                
                <div class="relative z-10 space-y-2">
                    <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-[11px] font-extrabold uppercase tracking-wider text-blue-100">
                        <span>⭐</span>
                        <span>Experiencia y Sugerencias Médicas</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Valoración al Especialista</h1>
                    <p class="text-blue-100 text-xs sm:text-sm max-w-md mx-auto">
                        Tu opinión es 100% confidencial y nos permite optimizar la atención de salud para ti.
                    </p>
                </div>
            </div>

            <div class="p-6 sm:p-8 space-y-8">
                
                <!-- Consultation Details Card -->
                <div class="bg-slate-50/80 rounded-2xl p-4 sm:p-5 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-primary to-blue-600 text-white flex items-center justify-center font-black text-xl shadow-md shrink-0">
                            {{ substr($doctor?->user?->name ?? 'Dr', 0, 2) }}
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-brand-primary uppercase tracking-wider">
                                {{ $appointment?->specialty?->name ?? 'Especialidad Médica' }}
                            </span>
                            <h2 class="text-base sm:text-lg font-black text-slate-900 leading-snug">
                                {{ $doctor?->user?->name ?? 'Dr(a). Médico Especialista' }}
                            </h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Cita: <span class="font-mono font-bold text-slate-700">#{{ $appointment?->code }}</span> &bull; 
                                {{ $appointment?->start_at ? $appointment->start_at->translatedFormat('d \d\e F \d\e Y') : 'Fecha reciente' }}
                            </p>
                        </div>
                    </div>

                    @if($patient && !$review->is_anonymous)
                        <div class="text-left sm:text-right border-t sm:border-t-0 pt-2 sm:pt-0 border-slate-200/60">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Paciente</span>
                            <p class="text-xs font-bold text-slate-700">{{ $patient->first_name }} {{ $patient->last_name }}</p>
                        </div>
                    @endif
                </div>

                <!-- ALREADY COMPLETED STATE -->
                @if($review->isCompleted())
                    <div class="text-center py-6 px-4 space-y-5 animate-fadeIn">
                        <div class="w-20 h-20 mx-auto rounded-3xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shadow-lg shadow-emerald-500/10 text-4xl">
                            ✅
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900">¡Valoración Registrada con Éxito!</h3>
                            <p class="text-xs sm:text-sm text-slate-600 max-w-md mx-auto">
                                Muchísimas gracias por tus comentarios y sugerencias sobre la atención recibida con el 
                                <strong>{{ $doctor?->user?->name }}</strong>. Nuestro equipo médico y directivo revisa cada aporte para seguir elevando la calidad asistencial.
                            </p>
                        </div>

                        <!-- Summary of Submitted Review -->
                        <div class="bg-blue-50/50 rounded-2xl p-5 border border-blue-100/70 max-w-lg mx-auto text-left space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-600">Calificación enviada:</span>
                                <div class="flex items-center space-x-1 text-amber-400 text-base">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <span>★</span>
                                        @else
                                            <span class="text-slate-200">★</span>
                                        @endif
                                    @endfor
                                    <span class="text-xs font-black text-slate-700 ml-1.5">({{ $review->rating }}/5)</span>
                                </div>
                            </div>

                            @if($review->comment)
                                <div class="pt-2 border-t border-blue-100/60">
                                    <span class="text-xs font-bold text-slate-500 block mb-1">Tus sugerencias / comentarios:</span>
                                    <p class="text-xs text-slate-700 italic bg-white p-3 rounded-xl border border-slate-100">
                                        "{{ $review->comment }}"
                                    </p>
                                </div>
                            @endif

                            <div class="text-[11px] text-slate-400 pt-1 flex items-center justify-between">
                                <span>Fecha: {{ $review->submitted_at?->translatedFormat('d \d\e F \d\e Y, H:i') }}</span>
                                <span class="font-medium text-brand-primary">{{ $review->is_anonymous ? 'Modo Anónimo' : 'Con Nombre' }}</span>
                            </div>
                        </div>

                        <div class="pt-4">
                            <a href="{{ route('home') }}" class="inline-flex items-center justify-center space-x-2 px-6 py-3 rounded-2xl bg-brand-primary hover:bg-brand-primary-dark text-white font-bold text-xs shadow-md shadow-brand-primary/20 transition-all">
                                <span>Ir a la Página Principal</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>

                <!-- EXPIRED STATE -->
                @elseif($isExpired)
                    <div class="text-center py-8 px-4 space-y-4">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center text-3xl">
                            ⏳
                        </div>
                        <h3 class="text-lg font-black text-slate-900">Enlace Expirado</h3>
                        <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                            El periodo de valoración para esta consulta médica ha concluido. Si deseas compartir tus sugerencias con la administración, por favor comunícate a nuestros canales de atención al paciente.
                        </p>
                        <a href="{{ route('home') }}" class="inline-block px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-xs">
                            Volver al Inicio
                        </a>
                    </div>

                <!-- RATING FORM STATE -->
                @else
                    <form 
                        method="POST" 
                        action="{{ route('reviews.public.store', ['token' => $review->token]) }}" 
                        class="space-y-8"
                        x-data="{
                            rating: {{ old('rating', 5) }},
                            hoverRating: 0,
                            attentionRating: {{ old('attention_rating', 5) }},
                            punctualityRating: {{ old('punctuality_rating', 5) }},
                            facilitiesRating: {{ old('facilities_rating', 5) }},
                            recommendationScore: {{ old('recommendation_score', 10) }},
                            isAnonymous: {{ old('is_anonymous') ? 'true' : 'false' }},
                            comment: '{{ old('comment', '') }}'
                        }"
                    >
                        @csrf

                        <!-- 1. General Star Rating (Grand Stars) -->
                        <div class="bg-gradient-to-b from-blue-50/60 to-white p-6 rounded-3xl border border-blue-100/80 text-center space-y-3">
                            <label class="block text-sm font-black text-slate-900">
                                ¿Cómo calificarías tu experiencia general con el especialista?
                            </label>
                            <p class="text-xs text-slate-500">Selecciona el número de estrellas</p>

                            <!-- Star Interactive Widget -->
                            <input type="hidden" name="rating" :value="rating">
                            
                            <div class="flex items-center justify-center space-x-2 py-2">
                                <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                    <button 
                                        type="button" 
                                        @click="rating = star" 
                                        @mouseenter="hoverRating = star" 
                                        @mouseleave="hoverRating = 0"
                                        class="p-1 sm:p-2 transition-all transform hover:scale-125 focus:outline-hidden"
                                        :title="star + ' estrellas'"
                                    >
                                        <svg 
                                            class="w-9 h-9 sm:w-11 sm:h-11 transition-colors duration-150" 
                                            :class="(hoverRating ? hoverRating >= star : rating >= star) ? 'text-amber-400 fill-amber-400 drop-shadow-xs' : 'text-slate-200 fill-slate-200'"
                                            viewBox="0 0 24 24" 
                                            stroke="currentColor" 
                                            stroke-width="1"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </button>
                                </template>
                            </div>

                            <!-- Rating Text Helper -->
                            <div class="text-xs font-black" :class="{
                                'text-emerald-600': (hoverRating || rating) === 5,
                                'text-blue-600': (hoverRating || rating) === 4,
                                'text-amber-600': (hoverRating || rating) === 3,
                                'text-orange-600': (hoverRating || rating) === 2,
                                'text-rose-600': (hoverRating || rating) === 1,
                            }">
                                <span x-show="(hoverRating || rating) === 5">⭐⭐⭐⭐⭐ ¡Excelente atención y profesionalismo!</span>
                                <span x-show="(hoverRating || rating) === 4">⭐⭐⭐⭐ Muy buena consulta médica</span>
                                <span x-show="(hoverRating || rating) === 3">⭐⭐⭐ Atención regular / aceptable</span>
                                <span x-show="(hoverRating || rating) === 2">⭐⭐ Atención por mejorar</span>
                                <span x-show="(hoverRating || rating) === 1">⭐ Experiencia insatisfactoria</span>
                            </div>
                        </div>

                        <!-- 2. Specific Criteria (Puntualidad, Trato, Instalaciones) -->
                        <div class="space-y-4 pt-2">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Aspectos Específicos del Servicio</h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- Trato y Atención -->
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-base">🤝</span>
                                        <span class="text-xs font-bold text-slate-800">Trato y Empatía</span>
                                    </div>
                                    <input type="hidden" name="attention_rating" :value="attentionRating">
                                    <div class="flex items-center justify-between pt-1">
                                        <template x-for="st in [1, 2, 3, 4, 5]" :key="st">
                                            <button type="button" @click="attentionRating = st" class="text-sm focus:outline-hidden">
                                                <span :class="attentionRating >= st ? 'text-amber-400' : 'text-slate-300'">★</span>
                                            </button>
                                        </template>
                                        <span class="text-[11px] font-bold text-slate-600" x-text="attentionRating + '/5'"></span>
                                    </div>
                                </div>

                                <!-- Puntualidad -->
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-base">⏱️</span>
                                        <span class="text-xs font-bold text-slate-800">Puntualidad</span>
                                    </div>
                                    <input type="hidden" name="punctuality_rating" :value="punctualityRating">
                                    <div class="flex items-center justify-between pt-1">
                                        <template x-for="st in [1, 2, 3, 4, 5]" :key="st">
                                            <button type="button" @click="punctualityRating = st" class="text-sm focus:outline-hidden">
                                                <span :class="punctualityRating >= st ? 'text-amber-400' : 'text-slate-300'">★</span>
                                            </button>
                                        </template>
                                        <span class="text-[11px] font-bold text-slate-600" x-text="punctualityRating + '/5'"></span>
                                    </div>
                                </div>

                                <!-- Instalaciones -->
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-base">🏥</span>
                                        <span class="text-xs font-bold text-slate-800">Instalaciones</span>
                                    </div>
                                    <input type="hidden" name="facilities_rating" :value="facilitiesRating">
                                    <div class="flex items-center justify-between pt-1">
                                        <template x-for="st in [1, 2, 3, 4, 5]" :key="st">
                                            <button type="button" @click="facilitiesRating = st" class="text-sm focus:outline-hidden">
                                                <span :class="facilitiesRating >= st ? 'text-amber-400' : 'text-slate-300'">★</span>
                                            </button>
                                        </template>
                                        <span class="text-[11px] font-bold text-slate-600" x-text="facilitiesRating + '/5'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Suggestions and Comments -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label for="comment" class="text-sm font-bold text-slate-900 flex items-center space-x-1.5">
                                    <span>✍️ Sugerencias y Comentarios al Doctor o Clínica</span>
                                </label>
                                <span class="text-[11px] text-slate-400">Opcional</span>
                            </div>
                            <textarea 
                                id="comment" 
                                name="comment" 
                                rows="4" 
                                x-model="comment"
                                placeholder="Cuéntanos qué fue lo más destacado de tu consulta o qué sugerencia de mejora tienes para el doctor o el centro médico..."
                                class="w-full px-4 py-3.5 rounded-2xl border border-slate-200 bg-white text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-brand-primary focus:border-brand-primary transition-all resize-none shadow-xs"
                            ></textarea>
                            <p class="text-[11px] text-slate-400 text-right">Tus sugerencias son leídas por el equipo directivo de la clínica.</p>
                        </div>

                        <!-- 4. Anonymous Toggle Option -->
                        <div class="bg-slate-50/70 p-4 rounded-2xl border border-slate-100 flex items-center justify-between cursor-pointer" @click="isAnonymous = !isAnonymous">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-xl bg-slate-200/70 text-slate-700 flex items-center justify-center font-bold text-sm">
                                    🕵️
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">Enviar de forma anónima</h4>
                                    <p class="text-[11px] text-slate-500">Ocultar mi nombre en el reporte del especialista.</p>
                                </div>
                            </div>
                            <div class="relative inline-flex items-center">
                                <input type="checkbox" name="is_anonymous" value="1" x-model="isAnonymous" class="sr-only">
                                <div class="w-11 h-6 bg-slate-200 rounded-full transition-colors" :class="{ 'bg-brand-primary': isAnonymous }">
                                    <div class="w-5 h-5 bg-white rounded-full shadow-md transform transition-transform" :class="{ 'translate-x-5': isAnonymous, 'translate-x-0.5': !isAnonymous }"></div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Submit Action -->
                        <div class="pt-2">
                            <button 
                                type="submit" 
                                class="w-full py-4 px-6 rounded-2xl bg-brand-primary hover:bg-brand-primary-dark text-white font-black text-sm sm:text-base shadow-xl shadow-brand-primary/25 transition-all transform hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center space-x-2"
                            >
                                <span>Enviar mi Valoración y Sugerencias</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                            <p class="text-center text-[11px] text-slate-400 mt-3">
                                Al enviar aceptas que tus valoraciones ayuden a optimizar la atención médica en {{ config('branding.client_name', 'Clínica Sur Hospital') }}.
                            </p>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- Safe Footer Note -->
        <div class="text-center mt-6 text-xs text-slate-400">
            <span>{{ config('branding.client_name', 'Clínica Sur Hospital') }} &bull; Sistema Nexaris Link</span>
        </div>
    </div>
</div>
@endsection
