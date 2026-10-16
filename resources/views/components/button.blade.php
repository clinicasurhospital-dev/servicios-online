@props([
    'variant' => 'primary', // 'primary', 'secondary', 'danger', 'outline', 'ghost'
    'size' => 'md', // 'sm', 'md', 'lg'
    'type' => 'button',
    'href' => null,
    'loading' => false,
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-xl transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed select-none active:scale-[0.98]';

    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-xs min-h-[36px] gap-1.5',
        'lg' => 'px-6 py-3.5 text-base min-h-[50px] gap-2.5 shadow-md',
        default => 'px-4 py-2.5 text-sm min-h-[44px] gap-2 shadow-sm',
    };

    $variantClasses = match($variant) {
        'secondary' => 'bg-slate-100 text-slate-800 hover:bg-slate-200 focus:ring-slate-400 border border-slate-200',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-500 shadow-rose-500/20',
        'outline' => 'bg-white text-slate-700 hover:bg-slate-50 hover:text-brand-600 border border-slate-200 hover:border-brand-300 focus:ring-brand-500',
        'ghost' => 'bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:ring-slate-400',
        default => 'bg-brand-600 text-white hover:bg-brand-700 focus:ring-brand-500 shadow-brand-600/25',
    };

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @disabled($disabled || $loading)>
        @if($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
