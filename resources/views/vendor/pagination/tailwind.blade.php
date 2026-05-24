@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between mt-6 w-full">
        {{-- Info Text (Left) --}}
        <div class="hidden sm:block text-xs text-base-content/65">
            @if ($paginator->firstItem())
                Menampilkan <span class="font-semibold text-base-content">{{ $paginator->firstItem() }}</span>
                - <span class="font-semibold text-base-content">{{ $paginator->lastItem() }}</span>
                dari <span class="font-semibold text-base-content">{{ $paginator->total() }}</span> data
            @else
                Menampilkan <span class="font-semibold text-base-content">{{ $paginator->count() }}</span> data
            @endif
        </div>

        {{-- Pagination Buttons (Right) --}}
        <div class="join border border-base-content/15 rounded-xl overflow-hidden bg-white shadow-xs ml-auto sm:ml-0">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="join-item btn btn-square btn-sm bg-base-100 text-base-content/40 cursor-not-allowed border-none hover:bg-base-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="join-item btn btn-square btn-sm bg-white text-base-content hover:bg-base-100 border-none transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="join-item btn btn-square btn-sm bg-white text-base-content/50 border-none cursor-default hover:bg-white">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="join-item btn btn-square btn-sm bg-fern-700 text-white border-none font-bold hover:bg-fern-700">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="join-item btn btn-square btn-sm bg-white text-base-content hover:bg-base-100 border-none font-medium transition-colors" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="join-item btn btn-square btn-sm bg-white text-base-content hover:bg-base-100 border-none transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            @else
                <span class="join-item btn btn-square btn-sm bg-base-100 text-base-content/40 cursor-not-allowed border-none hover:bg-base-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
