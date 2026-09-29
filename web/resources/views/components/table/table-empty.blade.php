@props([
    'colspan' => 100,
    'title' => null,
    'message' => null,
    'icon' => null,
])

@php
    $isSearching = request()->filled('q');
@endphp

<tr>
    <td colspan="{{ $colspan }}" class="text-center py-12 px-4">
        <div class="flex flex-col items-center justify-center max-w-md mx-auto">
            {{-- Icon Container --}}
            <div
                class="size-16 rounded-2xl bg-base-200/80 border border-base-300 flex items-center justify-center mb-4 text-base-content/50 shadow-inner">
                @if ($icon)
                    <span class="size-8 flex items-center justify-center">{!! $icon !!}</span>
                @elseif($isSearching)
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 stroke-[1.5]" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-8 stroke-[1.5]" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                @endif
            </div>

            {{-- Text Content --}}
            <h4 class="font-semibold text-base text-base-content mb-1">
                {{ $title ?? ($isSearching ? 'Hasil tidak ditemukan' : 'Belum Ada Data') }}
            </h4>

            <p class="text-sm text-base-content/60 leading-relaxed mb-4">
                @if ($message)
                    {{ $message }}
                @elseif ($slot->isNotEmpty())
                    {{ $slot }}
                @elseif ($isSearching)
                    Tidak ada data yang cocok dengan kata kunci "<span
                        class="font-semibold text-base-content">{{ request('q') }}</span>".
                @else
                    Belum ada data yang tersedia untuk ditampilkan saat ini.
                @endif
            </p>

            {{-- Action jika sedang mencari --}}
            @if ($isSearching)
                <a href="{{ request()->fullUrlWithQuery(['q' => null, 'page' => null]) }}"
                    class="btn btn-sm btn-ghost gap-1.5 border border-base-300 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reset Pencarian
                </a>
            @endif
        </div>
    </td>
</tr>
