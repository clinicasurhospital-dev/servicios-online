@props([
    'name',
    'title' => null,
    'width' => 'max-w-md', // 'max-w-md', 'max-w-lg', 'max-w-xl'
])

<div
    x-data="{ open: false }"
    x-on:open-drawer.window="$event.detail == '{{ $name }}' ? open = true : null"
    x-on:close-drawer.window="$event.detail == '{{ $name }}' ? open = false : null"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    class="fixed inset-0 overflow-hidden z-50"
    style="display: none;"
>
    <!-- Backdrop -->
    <div
        x-show="open"
        x-on:click="open = false"
        x-transition:enter="ease-out duration-250"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"
    ></div>

    <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <div
            x-show="open"
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-250"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="w-screen {{ $width }}"
        >
            <div class="h-full flex flex-col bg-white shadow-2xl overflow-y-auto">
                <!-- Header -->
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h3 class="text-base font-bold text-slate-900">{{ $title }}</h3>
                    <button
                        type="button"
                        x-on:click="open = false"
                        class="rounded-xl p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-500"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body Content -->
                <div class="p-6 flex-1">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
