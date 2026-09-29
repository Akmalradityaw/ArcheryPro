@props(['title', 'subtitle' => null, 'actions' => null, 'badge' => null])

<div
    {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-4 border-b border-base-200/80']) }}>
    <div class="min-w-0 flex-1">
        <div class="flex items-center gap-2.5">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-base-content truncate">{{ $title }}
            </h1>
            @if ($badge)
                <span class="badge badge-primary badge-soft font-semibold text-xs shrink-0">{{ $badge }}</span>
            @endif
        </div>
        @if ($subtitle)
            <p class="text-xs sm:text-sm text-base-content/60 mt-1 leading-relaxed">{{ $subtitle }}</p>
        @endif
    </div>

    @if ($actions)
        <div class="flex items-center gap-2 shrink-0 self-start sm:self-auto">{{ $actions }}</div>
    @endif
</div>
