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
    <svg width="36" height="36" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0 {{ $class }}" style="width: 36px; height: 36px; max-width: 36px; max-height: 36px; display: inline-block;">
        <!-- Shield Background -->
        <rect width="44" height="44" rx="12" fill="{{ $isWhite ? 'rgba(255,255,255,0.2)' : '#EEF0FF' }}"/>
        <!-- Healthcare Cross with Dynamic Pulse -->
        <path d="M22 10V34M10 22H34" stroke="{{ $primaryColor }}" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
        <!-- Pulse Heartbeat Accent -->
        <path d="M14 22L19 22L21 16L24 28L26 22L30 22" stroke="{{ $accentColor }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@else
    <div class="inline-flex items-center gap-2.5 font-sans {{ $class }}" style="display: inline-flex; align-items: center; text-decoration: none;">
        <svg width="36" height="36" viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0" style="width: 36px; height: 36px; min-width: 36px; max-width: 36px; max-height: 36px; display: block;">
            <rect width="44" height="44" rx="12" fill="{{ $isWhite ? 'rgba(255,255,255,0.2)' : '#EEF0FF' }}"/>
            <path d="M22 10V34M10 22H34" stroke="{{ $primaryColor }}" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M14 22L19 22L21 16L24 28L26 22L30 22" stroke="{{ $accentColor }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div class="flex flex-col leading-none" style="display: flex; flex-direction: column; line-height: 1;">
            <span class="text-xl font-black tracking-tight font-sans" style="color: {{ $textColor }}; font-size: 1.25rem; font-weight: 900; letter-spacing: -0.025em;">
                SUR<span style="color: {{ $primaryColor }};">HOSPITAL</span>
            </span>
            <span class="text-[10px] uppercase font-bold tracking-widest mt-0.5" style="color: {{ $isWhite ? 'rgba(255,255,255,0.8)' : '#667085' }}; font-size: 10px; font-weight: 700; letter-spacing: 0.1em; margin-top: 2px;">
                Excelencia Médica
            </span>
        </div>
    </div>
@endif
