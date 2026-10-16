@props([
    'title',
    'description' => null,
    'action' => null,
    'icon' => null,
])

<div class="text-center py-12 px-4 rounded-2xl bg-white border border-dashed border-slate-200">
    <div class="mx-auto h-14 w-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 mb-4 shadow-sm">
        @if($icon)
            {{ $icon }}
        @else
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        @endif
    </div>

    <h3 class="text-base font-bold text-slate-900">{{ $title }}</h3>

    @if($description)
        <p class="mt-1.5 text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">{{ $description }}</p>
    @endif

    @if($action)
        <div class="mt-5">
            {{ $action }}
        </div>
    @endif
</div>
