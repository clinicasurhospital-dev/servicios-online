@props([
    'variant' => 'full', // 'full', 'icon', 'white'
    'class' => '',
])

@php
    $isWhite = $variant === 'white';
    $primaryColor = $isWhite ? '#FFFFFF' : '#0700FF';
    $textColor = $isWhite ? '#FFFFFF' : '#172033';
    $accentColor = $isWhite ? '#EEF0FF' : '#00C2FF';
@endphp

@if($variant === 'icon')
    <svg width="40" height="40" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0 {{ $class }}" style="width: 40px; height: 40px; display: inline-block;">
        <!-- Shield Background -->
        <rect width="44" height="44" rx="12" fill="{{ $isWhite ? 'rgba(255,255,255,0.15)' : '#EEF0FF' }}"/>
        <!-- Healthcare Cross with Dynamic Pulse -->
        <path d="M22 10V34M10 22H34" stroke="{{ $primaryColor }}" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
        <!-- Pulse Heartbeat Accent -->
        <path d="M14 22L19 22L21 16L24 28L26 22L30 22" stroke="{{ $accentColor }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@else
    <div class="inline-flex items-center gap-3 font-sans shrink-0 {{ $class }}" style="text-decoration: none;">
        <svg width="40" height="40" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0" style="width: 40px; height: 40px; min-width: 40px;">
            <rect width="44" height="44" rx="12" fill="{{ $isWhite ? 'rgba(255,255,255,0.15)' : '#EEF0FF' }}"/>
            <path d="M22 10V34M10 22H34" stroke="{{ $primaryColor }}" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M14 22L19 22L21 16L24 28L26 22L30 22" stroke="{{ $accentColor }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div class="flex flex-col justify-center leading-none select-none">
            <span class="text-xl font-black tracking-tight font-sans" style="color: {{ $textColor }}; font-size: 1.3rem; font-weight: 900; letter-spacing: -0.03em;">
                SUR<span style="color: {{ $primaryColor }};">HOSPITAL</span>
            </span>
            <span class="text-[10px] uppercase font-bold tracking-widest mt-1" style="color: {{ $isWhite ? 'rgba(255,255,255,0.8)' : '#667085' }}; font-size: 10px; font-weight: 700; letter-spacing: 0.12em;">
                Excelencia Médica
            </span>
        </div>
    </div>
@endif
