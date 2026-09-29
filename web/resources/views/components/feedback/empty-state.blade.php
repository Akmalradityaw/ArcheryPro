@props([
    'title' => 'Belum Ada Data',
    'description' => null,
    'icon' => null,
])

<div
    {{ $attributes->merge(['class' => 'card bg-base-100/60 border border-dashed border-base-300 rounded-2xl overflow-hidden']) }}>
    <div class="card-body items-center text-center py-10 sm:py-14 px-6">
        <div
            class="size-16 rounded-2xl bg-base-200/80 border border-base-300/50 flex items-center justify-center mb-4 shadow-2xs">
            @if ($icon)
                <span class="size-8 text-base-content/40 flex items-center justify-center">{!! $icon !!}</span>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-base-content/40" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.75 7.5h16.5M3.75 7.5a2.25 2.25 0 012.25-2.25h12a2.25 2.25 0 012.25 2.25" />
                </svg>
            @endif
        </div>

        <h3 class="font-bold text-base sm:text-lg text-base-content tracking-tight">{{ $title }}</h3>

        @if ($description || $slot->isNotEmpty())
            <p class="text-xs sm:text-sm text-base-content/60 max-w-sm mt-1 leading-relaxed">
                {{ $description ?? $slot }}
            </p>
        @endif

        @isset($action)
            <div class="mt-4">{{ $action }}</div>
        @endisset
    </div>
</div>
