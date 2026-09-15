@props([
    'title' => null,
    'subtitle' => null,
    'action' => null,
    'padding' => 'p-6',
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-slate-100 shadow-sm ' . $class]) }}>
    @if($title || $action)
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                @if($title)
                    <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if($action)
                <div>
                    {{ $action }}
                </div>
            @endif
        </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
