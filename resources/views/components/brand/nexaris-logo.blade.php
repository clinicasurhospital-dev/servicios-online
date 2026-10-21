@props([
    'variant' => 'header', // 'header', 'badge', 'footer'
    'class' => '',
])

@if($variant === 'header')
    <div class="flex items-center gap-2.5 {{ $class }}">
        <div class="h-8 w-8 rounded-lg bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center text-white font-black text-sm shadow-sm shadow-brand-500/20">
            N
        </div>
        <div class="flex items-baseline gap-1.5">
            <span class="text-base font-bold text-slate-900 tracking-tight">Nexaris <span class="text-brand-600 font-extrabold">Link</span></span>
            <span class="text-xs text-slate-400 font-medium">|</span>
            <span class="text-xs font-semibold text-slate-600">SurHospital</span>
        </div>
    </div>
@elseif($variant === 'badge')
    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-600 {{ $class }}">
        <span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>
        Nexaris Link
    </span>
@else
    <div class="inline-flex items-center gap-1.5 text-xs text-slate-400 {{ $class }}">
        <span>Desarrollado por</span>
        <span class="font-semibold text-slate-600 hover:text-brand-600 transition-colors">Nexaris Link</span>
    </div>
@endif
