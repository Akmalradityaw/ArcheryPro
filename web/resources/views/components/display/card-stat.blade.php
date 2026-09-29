@props([
    'title' => '',
    'value' => '0',
    'icon' => null,
    'description' => null,
    'trend' => null, // opsional: contoh '+12%'
    'trendType' => 'up', // up | down
    'color' => 'primary', // primary | success | warning | error | info
])

@php
    $colorStyles = match ($color) {
        'success' => ['text' => 'text-success', 'bg' => 'bg-success/10 border-success/20'],
        'warning' => ['text' => 'text-warning', 'bg' => 'bg-warning/10 border-warning/20'],
        'error' => ['text' => 'text-error', 'bg' => 'bg-error/10 border-error/20'],
        'info' => ['text' => 'text-info', 'bg' => 'bg-info/10 border-info/20'],
        default => ['text' => 'text-primary', 'bg' => 'bg-primary/10 border-primary/20'],
    };
@endphp

{{-- Double-bezel: outer tray + inner core (skill: Doppelrand) --}}
<div
    {{ $attributes->merge(['class' => 'p-1.5 bg-base-200/60 ring-1 ring-base-300/60 rounded-[1.75rem] transition-transform duration-500 group']) }}>
    <div
        class="relative overflow-hidden bg-base-100 rounded-[calc(1.75rem-0.375rem)] shadow-[inset_0_1px_1px_rgba(255,255,255,0.6)] group-hover:-translate-y-0.5 transition-transform duration-500">
        <div class="card-body p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-base-content/50">
                        {{ $title }}
                    </p>
                    <div class="flex items-baseline gap-2 mt-1.5">
                        <span
                            class="text-2xl sm:text-3xl font-extrabold tracking-tight {{ $colorStyles['text'] }} truncate">
                            {{ $value }}
                        </span>
                        @if ($trend)
                            <span
                                class="inline-flex items-center text-[11px] font-semibold px-1.5 py-0.5 rounded-md {{ $trendType === 'up' ? 'bg-success/15 text-success' : 'bg-error/15 text-error' }}">
                                {{ $trend }}
                            </span>
                        @endif
                    </div>

                    @if ($description)
                        <p class="text-xs text-base-content/60 mt-1.5 line-clamp-1">
                            {{ $description }}
                        </p>
                    @endif
                </div>

                @if ($icon)
                    <div
                        class="size-11 rounded-xl {{ $colorStyles['bg'] }} border flex items-center justify-center shrink-0 transition-transform duration-500 group-hover:scale-105">
                        @if (str_contains($icon, '<'))
                            {{-- Jika $icon dikirim berupa mentahan tag HTML/SVG --}}
                            <span class="size-5.5 {{ $colorStyles['text'] }} flex items-center justify-center">
                                {!! $icon !!}
                            </span>
                        @else
                            {{-- Jika $icon berupa string nama (seperti "users", "calendar", "clock") --}}
                            <x-display.icon :name="$icon" class="size-5.5 {{ $colorStyles['text'] }}" />
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
