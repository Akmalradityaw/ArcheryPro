@props(['label', 'by', 'align' => 'left'])

@php
    $aktif = request('sort') === $by;
    $arah = $aktif && request('dir', 'asc') === 'asc' ? 'desc' : 'asc';
    $ariaSort = $aktif ? (request('dir', 'asc') === 'asc' ? 'ascending' : 'descending') : 'none';

    $alignmentClass = match ($align) {
        'center' => 'justify-center text-center',
        'right' => 'justify-end text-right',
        default => 'justify-start text-left',
    };
@endphp

<th aria-sort="{{ $ariaSort }}" class="select-none py-3 px-4">
    <a href="{{ request()->fullUrlWithQuery(['sort' => $by, 'dir' => $arah]) }}"
        class="group inline-flex items-center gap-1.5 font-semibold text-xs uppercase tracking-wider transition-colors duration-150 {{ $alignmentClass }} {{ $aktif ? 'text-primary' : 'text-base-content/70 hover:text-base-content' }}">

        <span>{{ $label }}</span>

        <span class="inline-flex flex-col justify-center shrink-0">
            @if ($aktif)
                @if (request('dir', 'asc') === 'asc')
                    {{-- Ascending Icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 text-primary stroke-[2.5]" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                    </svg>
                @else
                    {{-- Descending Icon --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 text-primary stroke-[2.5]" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                @endif
            @else
                {{-- Default Unsorted Icon --}}
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="size-3.5 text-base-content/30 group-hover:text-base-content/60 transition-colors stroke-[2]"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                </svg>
            @endif
        </span>
    </a>
</th>
