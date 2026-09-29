@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi halaman"
        class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-base-200/80">
        <p class="text-xs sm:text-sm text-base-content/60 text-center sm:text-left">
            Menampilkan
            <span class="font-bold text-base-content">{{ $paginator->firstItem() ?? 0 }}</span>
            –
            <span class="font-bold text-base-content">{{ $paginator->lastItem() ?? 0 }}</span>
            dari
            <span class="font-bold text-base-content">{{ $paginator->total() }}</span>
            data
        </p>
        <div class="join shadow-2xs rounded-xl overflow-hidden border border-base-300/80">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <button class="join-item btn btn-xs sm:btn-sm bg-base-100 border-none text-base-content/30" disabled
                    aria-label="Sebelumnya">
                    <x-display.icon name="chevron-left" class="size-3.5" />
                </button>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="join-item btn btn-xs sm:btn-sm bg-base-100 hover:bg-base-200 border-none text-base-content"
                    aria-label="Sebelumnya">
                    <x-display.icon name="chevron-left" class="size-3.5" />
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <button class="join-item btn btn-xs sm:btn-sm bg-base-100 border-none text-base-content/40"
                        disabled>{{ $element }}</button>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <button class="join-item btn btn-xs sm:btn-sm btn-primary shadow-xs font-bold"
                                aria-current="page">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}"
                                class="join-item btn btn-xs sm:btn-sm bg-base-100 hover:bg-base-200 border-none text-base-content/80 font-medium">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="join-item btn btn-xs sm:btn-sm bg-base-100 hover:bg-base-200 border-none text-base-content"
                    aria-label="Berikutnya">
                    <x-display.icon name="chevron-right" class="size-3.5" />
                </a>
            @else
                <button class="join-item btn btn-xs sm:btn-sm bg-base-100 border-none text-base-content/30" disabled
                    aria-label="Berikutnya">
                    <x-display.icon name="chevron-right" class="size-3.5" />
                </button>
            @endif
        </div>
    </nav>
@endif
