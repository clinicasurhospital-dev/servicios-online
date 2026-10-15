@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navegación de páginas" class="flex items-center justify-between">
        {{-- VISTA MÓVIL (sm:hidden) --}}
        <div class="flex justify-between items-center w-full sm:hidden gap-2">
            {{-- Botón Anterior Móvil --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-3.5 py-2 text-xs font-bold text-slate-400 bg-slate-50 border border-slate-200 rounded-xl cursor-not-allowed opacity-60">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>Anterior</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center px-3.5 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-brand-primary transition-colors shadow-xs">
                    <svg class="w-4 h-4 mr-1 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>Anterior</span>
                </a>
            @endif

            {{-- Indicador de Página Móvil --}}
            <span class="text-xs font-semibold text-slate-500">
                Pág. <span class="font-bold text-slate-800">{{ $paginator->currentPage() }}</span> / {{ $paginator->lastPage() }}
            </span>

            {{-- Botón Siguiente Móvil --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center px-3.5 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-brand-primary transition-colors shadow-xs">
                    <span>Siguiente</span>
                    <svg class="w-4 h-4 ml-1 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center px-3.5 py-2 text-xs font-bold text-slate-400 bg-slate-50 border border-slate-200 rounded-xl cursor-not-allowed opacity-60">
                    <span>Siguiente</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                    </svg>
                </span>
            @endif
        </div>

        {{-- VISTA TABLET / ESCRITORIO (hidden sm:flex) --}}
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-slate-500">
                    Mostrando
                    @if ($paginator->firstItem())
                        <span class="font-bold text-slate-800">{{ $paginator->firstItem() }}</span>
                        a
                        <span class="font-bold text-slate-800">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    de
                    <span class="font-bold text-slate-800">{{ $paginator->total() }}</span>
                    resultados
                </p>
            </div>

            <div>
                <div class="inline-flex items-center space-x-1">
                    {{-- Botón Anterior Escritorio --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" class="w-8 h-8 flex items-center justify-center rounded-xl border border-slate-200 text-slate-300 bg-slate-50 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="w-8 h-8 flex items-center justify-center rounded-xl border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 hover:text-brand-primary hover:border-brand-primary/40 transition-colors shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                    @endif

                    {{-- Elementos de Paginación Numéricos --}}
                    @foreach ($elements as $element)
                        {{-- Separador "..." --}}
                        @if (is_string($element))
                            <span class="w-8 h-8 flex items-center justify-center text-xs font-bold text-slate-400">{{ $element }}</span>
                        @endif

                        {{-- Lista de Páginas --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="w-8 h-8 flex items-center justify-center rounded-xl bg-brand-primary text-white font-bold text-xs shadow-sm shadow-brand-primary/30">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="w-8 h-8 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 font-bold text-xs hover:bg-slate-50 hover:text-brand-primary hover:border-brand-primary/40 transition-colors shadow-xs">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Botón Siguiente Escritorio --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="w-8 h-8 flex items-center justify-center rounded-xl border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 hover:text-brand-primary hover:border-brand-primary/40 transition-colors shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" class="w-8 h-8 flex items-center justify-center rounded-xl border border-slate-200 text-slate-300 bg-slate-50 cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </nav>
@endif
