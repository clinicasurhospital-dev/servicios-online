@props([
    'label' => null,
    'name' => null,
    'id' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'error' => null,
    'hint' => null,
    'disabled' => false,
    'icon' => null,
])

@php
    $id = $id ?? $name ?? 'input-' . Str::random(8);
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
        @if($icon)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                {{ $icon }}
            </div>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @required($required)
            @disabled($disabled)
            {{ $attributes->merge([
                'class' => 'block w-full min-h-[44px] rounded-xl border-slate-200 text-slate-900 text-sm placeholder-slate-400 focus:border-brand-500 focus:ring-brand-500 transition-colors disabled:bg-slate-50 disabled:text-slate-500 disabled:cursor-not-allowed ' .
                ($icon ? 'pl-10 ' : 'pl-3.5 ') .
                ($hasError ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500 text-rose-900' : 'focus:border-brand-500')
            ]) }}
        />

        @if($hasError)
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="h-5 w-5 text-rose-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
            </div>
        @endif
    </div>

    @if($hasError)
        <p class="mt-1.5 text-xs text-rose-600 flex items-center gap-1 font-medium">
            {{ $errorMessage }}
        </p>
    @elseif($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
