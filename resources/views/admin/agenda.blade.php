@extends('layouts.admin')

@section('title', 'Agenda Médica — ' . config('branding.app_name'))

@section('content')
<div class="space-y-6 animate-fadeIn" x-data="{ selectedApp: null, showModal: false }">
    <!-- Top Agenda Toolbar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 sm:p-6 rounded-3xl border border-slate-100 shadow-sm">
        
        <!-- Date Navigation Controls -->
        <div class="flex items-center space-x-2">
            @php
                $prevDate = $viewMode === 'week' ? $selectedDate->copy()->subWeek()->format('Y-m-d') : $selectedDate->copy()->subDay()->format('Y-m-d');
                $nextDate = $viewMode === 'week' ? $selectedDate->copy()->addWeek()->format('Y-m-d') : $selectedDate->copy()->addDay()->format('Y-m-d');
            @endphp

            <a href="{{ route('admin.agenda', ['view' => $viewMode, 'date' => $prevDate, 'doctor_id' => $selectedDoctorId]) }}" class="p-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>

            <a href="{{ route('admin.agenda', ['view' => $viewMode, 'date' => now()->format('Y-m-d'), 'doctor_id' => $selectedDoctorId]) }}" class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                Hoy
            </a>

            <a href="{{ route('admin.agenda', ['view' => $viewMode, 'date' => $nextDate, 'doctor_id' => $selectedDoctorId]) }}" class="p-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>

            <div class="ml-2">
                <span class="text-sm font-extrabold text-slate-900 block">
                    @if($viewMode === 'week')
                        Semana del {{ $selectedDate->copy()->startOfWeek()->format('d M') }} al {{ $selectedDate->copy()->endOfWeek()->format('d M, Y') }}
                    @else
                        {{ $selectedDate->translatedFormat('l, d \d\e F \d\e Y') }}
                    @endif
                </span>
            </div>
        </div>

        <!-- View Mode and Doctor Filters -->
        <div class="flex flex-wrap items-center gap-3">
            @if(!auth()->user()->isDoctor())
                <form method="GET" action="{{ route('admin.agenda') }}" class="flex items-center space-x-2">
                    <input type="hidden" name="view" value="{{ $viewMode }}">
                    <input type="hidden" name="date" value="{{ $selectedDate->format('Y-m-d') }}">
                    
                    <select name="doctor_id" onchange="this.form.submit()" class="rounded-xl border-slate-200 text-xs font-semibold p-2.5 focus:border-brand-primary focus:ring-brand-primary">
                        <option value="">Todos los Especialistas</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" {{ $selectedDoctorId == $doc->id ? 'selected' : '' }}>
                                {{ $doc->user->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif

            <!-- View Switcher Tabs -->
            <div class="flex p-1 bg-slate-100 rounded-xl">
                <a href="{{ route('admin.agenda', ['view' => 'day', 'date' => $selectedDate->format('Y-m-d'), 'doctor_id' => $selectedDoctorId]) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $viewMode === 'day' ? 'bg-white text-brand-primary shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Día
                </a>
                <a href="{{ route('admin.agenda', ['view' => 'week', 'date' => $selectedDate->format('Y-m-d'), 'doctor_id' => $selectedDoctorId]) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $viewMode === 'week' ? 'bg-white text-brand-primary shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Semana
                </a>
            </div>
        </div>
    </div>

    <!-- CALENDAR MATRIX -->
    @if($viewMode === 'day')
        <!-- DAY VIEW -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Horarios del Día ({{ $appointments->count() }} citas)</h3>
                <span class="text-xs text-slate-400">Haz clic sobre cualquier turno para ver o actualizar</span>
            </div>

            @if($appointments->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($appointments as $app)
                        <div @click="selectedApp = {{ $app->toJson() }}; showModal = true" class="p-4 rounded-2xl border border-slate-200/80 bg-slate-50/50 hover:bg-brand-soft/30 hover:border-brand-primary cursor-pointer transition-all space-y-3 group shadow-xs">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-white text-brand-primary border border-slate-200 shadow-xs">
                                    {{ $app->start_at->format('H:i') }} - {{ $app->end_at->format('H:i') }}
                                </span>
                                <x-status-badge :status="$app->status" />
                            </div>

                            <div>
                                <h4 class="font-bold text-slate-900 text-sm group-hover:text-brand-primary transition-colors">
                                    {{ $app->patient->first_name }} {{ $app->patient->last_name }}
                                </h4>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Dr. {{ $app->doctor->user->name }} &bull; {{ $app->specialty->name }}
                                </p>
                            </div>

                            <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-xs text-slate-400">
                                <span class="font-mono">#{{ $app->code }}</span>
                                <span class="font-bold text-slate-700">${{ number_format($app->total_amount, 0) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 text-slate-400">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-sm font-semibold text-slate-600">No hay citas registradas para este día</p>
                    <p class="text-xs text-slate-400 mt-1">Crea una nueva cita manual desde el botón superior o selecciona otra fecha.</p>
                </div>
            @endif
        </div>
    @else
        <!-- WEEK VIEW -->
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm overflow-x-auto">
            <div class="grid grid-cols-7 gap-3 min-w-[900px]">
                @php
                    $startDay = $selectedDate->copy()->startOfWeek();
                @endphp

                @for($i = 0; $i < 7; $i++)
                    @php
                        $day = $startDay->copy()->addDays($i);
                        $dayApps = $appointments->filter(fn ($a) => $a->start_at->isSameDay($day));
                        $isToday = $day->isToday();
                    @endphp

                    <div class="rounded-2xl p-3 border {{ $isToday ? 'border-brand-primary bg-brand-soft/20' : 'border-slate-100 bg-slate-50/50' }} min-h-[400px] flex flex-col">
                        <div class="text-center pb-2 border-b {{ $isToday ? 'border-brand-primary/20' : 'border-slate-200' }} mb-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider block {{ $isToday ? 'text-brand-primary' : 'text-slate-400' }}">
                                {{ $day->translatedFormat('D') }}
                            </span>
                            <span class="text-base font-black {{ $isToday ? 'text-brand-primary' : 'text-slate-800' }}">
                                {{ $day->format('d M') }}
                            </span>
                        </div>

                        <div class="space-y-2 flex-1 overflow-y-auto">
                            @forelse($dayApps as $app)
                                <div @click="selectedApp = {{ $app->toJson() }}; showModal = true" class="p-2 rounded-xl bg-white border border-slate-200 shadow-xs hover:border-brand-primary cursor-pointer text-xs space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-black text-brand-primary">{{ $app->start_at->format('H:i') }}</span>
                                        <span class="w-2 h-2 rounded-full {{ $app->status->value === 'CONFIRMED' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    </div>
                                    <p class="font-bold text-slate-800 truncate">{{ $app->patient->first_name }} {{ $app->patient->last_name }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">Dr. {{ $app->doctor->user->name }}</p>
                                </div>
                            @empty
                                <div class="text-center py-8 text-[11px] text-slate-300">Libre</div>
                            @endforelse
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    @endif

    <!-- QUICK APPOINTMENT DETAIL DRAWER / MODAL -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4" @click.away="showModal = false">
            <template x-if="selectedApp">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <span class="text-xs font-mono text-slate-400" x-text="'#' + selectedApp.code"></span>
                            <h3 class="text-lg font-extrabold text-slate-900" x-text="selectedApp.patient ? selectedApp.patient.first_name + ' ' + selectedApp.patient.last_name : 'Paciente'"></h3>
                        </div>
                        <button @click="showModal = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="py-4 space-y-3 text-xs">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 rounded-xl bg-slate-50">
                                <span class="text-slate-400 block">Médico Especialista:</span>
                                <span class="font-bold text-slate-800" x-text="selectedApp.doctor?.user?.name || 'Dr.'"></span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50">
                                <span class="text-slate-400 block">Especialidad:</span>
                                <span class="font-bold text-slate-800" x-text="selectedApp.specialty?.name || 'General'"></span>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50">
                            <span class="text-slate-400 block">Fecha y Horario:</span>
                            <span class="font-bold text-brand-primary" x-text="selectedApp.start_at"></span>
                        </div>

                        <!-- Status Update Form -->
                        <div class="pt-3 border-t border-slate-100">
                            <label class="block font-bold text-slate-700 mb-1.5">Actualizar Estado de la Consulta:</label>
                            <form :action="'{{ url('/admin/citas') }}/' + selectedApp.id + '/estado'" method="POST" class="space-y-3">
                                @csrf
                                <select name="status" class="w-full rounded-xl border-slate-200 text-xs p-2.5 font-bold focus:border-brand-primary">
                                    <option value="CONFIRMED">CONFIRMED (Confirmada)</option>
                                    <option value="COMPLETED">COMPLETED (Atendida / Realizada)</option>
                                    <option value="NO_SHOW">NO_SHOW (No Asistió)</option>
                                    <option value="CANCELLED">CANCELLED (Cancelada)</option>
                                </select>

                                <input type="text" name="notes" placeholder="Notas de recepción o motivo..." class="w-full rounded-xl border-slate-200 text-xs p-2.5" />

                                <div class="flex justify-end space-x-2 pt-2">
                                    <button type="button" @click="showModal = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold">
                                        Cerrar
                                    </button>
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-primary text-white font-bold shadow-sm">
                                        Guardar Estado
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection
