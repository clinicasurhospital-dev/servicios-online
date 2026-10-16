@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'rows' => 3,
    'placeholder' => null,
    'required' => false,
    'error' => null,
    'hint' => null,
    'disabled' => false,
])

@php
    $id = $id ?? $name ?? 'textarea-' . Str::random(8);
    $hasError = $error || ($name && $errors->has($name));
    $errorMessage = $error ?? ($name ? $errors->first($name) : null);
@endphp

<div class="w-full">
    @if($label)
        <label for="{{ $id }}" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-rose-500 font-bold ml-0.5">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-xl shadow-sm">
        <textarea
            name="{{ $name }}"
            id="{{ $id }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            @required($required)
            @disabled($disabled)
            {{ $attributes->merge([
                'class' => 'block w-full rounded-xl border-slate-200 text-slate-900 text-sm placeholder-slate-400 focus:border-brand-500 focus:ring-brand-500 transition-colors disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed p-3.5 ' .
                ($hasError ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500 text-rose-900' : 'focus:border-brand-500')
            ]) }}
        >{{ $slot }}</textarea>
    </div>

    @if($hasError)
        <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1 font-medium">
            {{ $errorMessage }}
        </p>
    @elseif($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
