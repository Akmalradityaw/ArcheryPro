@props([
    'paginator' => null,
    'cari' => true,
    'placeholder' => 'Cari data...',
    'actions' => null,
    'filters' => null,
])

<div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl overflow-hidden">

    {{-- Toolbar: Search + Filters + Actions --}}
    @if ($cari || $filters || $actions)
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 border-b border-base-200 bg-base-100/50">

            {{-- Area Pencarian & Filter --}}
            @if ($cari || $filters)
                <div class="flex flex-wrap items-center gap-2 flex-1">
                    @if ($cari)
                        <form method="GET" class="flex items-center gap-2 w-full sm:w-auto">
                            {{-- Pertahankan query param lain selain q dan page --}}
                            @foreach (request()->except(['q', 'page']) as $k => $v)
                                @if (is_array($v))
                                    @foreach ($v as $item)
                                        <input type="hidden" name="{{ $k }}[]" value="{{ $item }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                @endif
                            @endforeach

                            <div class="relative w-full sm:w-64">
                                <span
                                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-base-content/40">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" name="q" value="{{ request('q') }}"
                                    placeholder="{{ $placeholder }}"
                                    class="input input-bordered input-sm pl-9 pr-8 w-full rounded-xl focus:outline-none focus:border-primary transition-colors">

                                @if (request('q'))
                                    <a href="{{ request()->fullUrlWithQuery(['q' => null, 'page' => null]) }}"
                                        class="absolute inset-y-0 right-0 flex items-center pr-2.5 text-base-content/40 hover:text-base-content/80 transition-colors"
                                        title="Hapus pencarian">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </a>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-sm btn-primary rounded-xl font-medium shrink-0">
                                Cari
                            </button>
                        </form>
                    @endif

                    {{-- Slot tambahan untuk Filter opsional --}}
                    @if ($filters)
                        <div class="flex items-center gap-2 flex-wrap">
                            {{ $filters }}
                        </div>
                    @endif
                </div>
            @endif

            {{-- Slot Action Tombol Tambah / Export / dll --}}
            @if ($actions)
                <div class="flex items-center gap-2 shrink-0 self-end sm:self-auto">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    {{-- Tabel Container --}}
    <div class="overflow-x-auto">
        <table class="table table-zebra w-full text-left">
            {{ $slot }}
        </table>
    </div>

    {{-- Footer / Pagination --}}
    @if ($paginator && method_exists($paginator, 'links') && $paginator->hasPages())
        <div class="p-4 border-t border-base-200 bg-base-100">
            {{ $paginator->links() }}
        </div>
    @endif
</div>
