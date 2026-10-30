@props([
    'variant' => 'full', // 'full', 'icon', 'white'
    'class' => '',
    'height' => null,
])

@php
    $isWhite = $variant === 'white';
    $isIcon = $variant === 'icon';

    if ($isIcon) {
        $src = $isWhite ? asset('images/surhospital/icon-white.png') : asset('images/surhospital/icon.png');
        $imgClass = 'h-10 w-auto object-contain';
    } elseif ($isWhite) {
        $src = asset('images/surhospital/logo-white.png');
        $imgClass = 'h-12 w-auto max-w-[220px] object-contain';
    } else {
        $src = asset('images/surhospital/logo.png');
        $imgClass = 'h-11 sm:h-12 w-auto max-w-[220px] sm:max-w-[240px] object-contain';
    }
@endphp

<div class="inline-flex items-center shrink-0 {{ $class }}">
    <img 
        src="{{ $src }}" 
        alt="{{ config('branding.client_name', 'SurHospital') }}" 
        class="shrink-0 select-none {{ $imgClass }}"
        @if($height) style="height: {{ is_numeric($height) ? $height . 'px' : $height }};" @endif
    >
</div>

