@props([
    'time', // e.g. "08:30"
    'selected' => false,
    'disabled' => false,
])

<button
    type="button"
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => 'min-h-[44px] min-w-[76px] px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 flex items-center justify-center select-none active:scale-95 ' .
        ($disabled 
            ? 'bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed line-through opacity-60' 
            : ($selected 
                ? 'bg-brand-600 text-white shadow-md shadow-brand-600/30 border-2 border-brand-600 ring-2 ring-brand-400/30' 
                : 'bg-white text-slate-800 border border-slate-200 hover:border-brand-500 hover:text-brand-600 hover:bg-brand-50/50 shadow-sm'))
    ]) }}
>
    {{ $time }}
</button>
