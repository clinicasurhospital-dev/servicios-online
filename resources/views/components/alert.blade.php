@props([
    'type' => 'info', // 'info', 'success', 'warning', 'error'
    'title' => null,
])

@php
    $classes = match($type) {
        'success' => 'bg-emerald-50 border-emerald-200 text-emerald-900',
        'warning' => 'bg-amber-50 border-amber-200 text-amber-900',
        'error' => 'bg-rose-50 border-rose-200 text-rose-900',
        default => 'bg-blue-50 border-blue-200 text-blue-900',
    };

    $iconColor = match($type) {
        'success' => 'text-emerald-500',
        'warning' => 'text-amber-500',
        'error' => 'text-rose-500',
        default => 'text-blue-500',
    };
@endphp

<div {{ $attributes->merge(['class' => "rounded-2xl border p-4 flex items-start gap-3 {$classes}"]) }}>
    <div class="shrink-0 mt-0.5 {{ $iconColor }}">
        @if($type === 'success')
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
            </svg>
        @elseif($type === 'warning')
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
            </svg>
        @elseif($type === 'error')
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
            </svg>
        @else
            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
            </svg>
        @endif
    </div>
    <div class="flex-1 text-sm">
        @if($title)
            <h4 class="font-bold mb-0.5">{{ $title }}</h4>
        @endif
        <div class="leading-relaxed opacity-90">{{ $slot }}</div>
    </div>
</div>
