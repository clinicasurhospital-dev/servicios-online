@props([
    'title',
    'value',
    'icon' => null,
    'change' => null,
    'trend' => 'up', // 'up', 'down', 'neutral'
    'color' => 'brand', // 'brand', 'emerald', 'amber', 'rose', 'indigo'
])

@php
    $iconBg = match($color) {
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'rose' => 'bg-rose-50 text-rose-600',
        'indigo' => 'bg-indigo-50 text-indigo-600',
        default => 'bg-brand-50 text-brand-600',
    };
@endphp

<div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-start justify-between">
    <div>
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">{{ $title }}</p>
        <h3 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $value }}</h3>
        
        @if($change)
            <div class="flex items-center gap-1 mt-2 text-xs font-medium">
                @if($trend === 'up')
                    <span class="text-emerald-600 flex items-center">
                        <svg class="h-3.5 w-3.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                        </svg>
                        {{ $change }}
                    </span>
                @elseif($trend === 'down')
                    <span class="text-rose-600 flex items-center">
                        <svg class="h-3.5 w-3.5 mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                        {{ $change }}
                    </span>
                @else
                    <span class="text-slate-500">{{ $change }}</span>
                @endif
                <span class="text-slate-400 font-normal">vs periodo ant.</span>
            </div>
        @endif
    </div>

    @if($icon)
        <div class="p-3 rounded-xl {{ $iconBg }}">
            {{ $icon }}
        </div>
    @endif
</div>
