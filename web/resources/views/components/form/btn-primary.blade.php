@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'loading' => false,
    'href' => null,
])

@php
    $variantClass = match ($variant) {
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'outline' => 'btn-outline btn-primary',
        'ghost' => 'btn-ghost',
        'error' => 'btn-error text-white',
        'success' => 'btn-success text-white',
        'warning' => 'btn-warning text-white',
        'info' => 'btn-info text-white',
        default => 'btn-primary',
    };

    $sizeClass = match ($size) {
        'xs' => 'btn-xs',
        'sm' => 'btn-sm',
        'lg' => 'btn-lg',
        default => '',
    };

    // TAMBAHKAN flex-nowrap DAN whitespace-nowrap DI SINI
    $classes = "btn {$variantClass} {$sizeClass} gap-2 inline-flex items-center justify-center flex-nowrap whitespace-nowrap font-medium transition-all duration-150";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon && $iconPosition === 'left')
            <span class="size-4 shrink-0 flex items-center justify-center">{!! $icon !!}</span>
        @endif

        <span class="inline-flex items-center gap-1.5 whitespace-nowrap">{{ $slot }}</span>

        @if ($icon && $iconPosition === 'right')
            <span class="size-4 shrink-0 flex items-center justify-center">{!! $icon !!}</span>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @disabled($loading)>
        @if ($loading)
            <span class="loading loading-spinner loading-sm shrink-0"></span>
        @elseif ($icon && $iconPosition === 'left')
            <span class="size-4 shrink-0 flex items-center justify-center">{!! $icon !!}</span>
        @endif

        <span class="inline-flex items-center gap-1.5 whitespace-nowrap">{{ $slot }}</span>

        @if ($icon && $iconPosition === 'right' && !$loading)
            <span class="size-4 shrink-0 flex items-center justify-center">{!! $icon !!}</span>
        @endif
    </button>
@endif
