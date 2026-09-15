@props([
    'variant' => 'full', // 'full', 'icon', 'white'
    'class' => 'h-9 w-auto',
])

@php
    $isWhite = $variant === 'white';
    $primaryColor = $isWhite ? '#FFFFFF' : '#0700FF';
    $textColor = $isWhite ? '#FFFFFF' : '#172033';
    $accentColor = $isWhite ? '#EEF0FF' : '#00C2FF';
@endphp

@if($variant === 'icon')
    <svg viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => $class]) }}>
        <!-- Shield Background -->
        <rect width="44" height="44" rx="12" fill="{{ $isWhite ? 'rgba(255,255,255,0.2)' : '#EEF0FF' }}"/>
        <!-- Healthcare Cross with Dynamic Pulse -->
        <path d="M22 10V34M10 22H34" stroke="{{ $primaryColor }}" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
        <!-- Pulse Heartbeat Accent -->
        <path d="M14 22L19 22L21 16L24 28L26 22L30 22" stroke="{{ $accentColor }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@else
    <div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5 font-sans ' . $class]) }}>
        <svg viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-9 w-9 shrink-0">
            <rect width="44" height="44" rx="12" fill="{{ $isWhite ? 'rgba(255,255,255,0.2)' : '#EEF0FF' }}"/>
            <path d="M22 10V34M10 22H34" stroke="{{ $primaryColor }}" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M14 22L19 22L21 16L24 28L26 22L30 22" stroke="{{ $accentColor }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div class="flex flex-col leading-none">
            <span class="text-xl font-black tracking-tight font-sans" style="color: {{ $textColor }};">
                SUR<span style="color: {{ $primaryColor }};">HOSPITAL</span>
            </span>
            <span class="text-[10px] uppercase font-bold tracking-widest mt-0.5" style="color: {{ $isWhite ? 'rgba(255,255,255,0.8)' : '#667085' }};">
                Excelencia Médica
            </span>
        </div>
    </div>
@endif
