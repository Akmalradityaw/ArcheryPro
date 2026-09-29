@props([
    'type' => 'info', // info | success | warning | error | neutral
    'message' => null,
    'dismissible' => false,
    'icon' => null, // override: clock | info-circle | warning-triangle | info | check | x
])

@php
    $config = [
        'info' => [
            'bg' => 'bg-info/10 border-info/30 text-info-content',
            'iconColor' => 'text-info',
            'iconPath' =>
                'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z',
        ],
        'success' => [
            'bg' => 'bg-success/10 border-success/30 text-success-content',
            'iconColor' => 'text-success',
            'iconPath' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'warning' => [
            'bg' => 'bg-warning/10 border-warning/30 text-warning-content',
            'iconColor' => 'text-warning',
            'iconPath' =>
                'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z',
        ],
        'error' => [
            'bg' => 'bg-error/10 border-error/30 text-error-content',
            'iconColor' => 'text-error',
            'iconPath' => 'M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        'neutral' => [
            'bg' => 'bg-base-200 border-base-300 text-base-content',
            'iconColor' => 'text-base-content/50',
            'iconPath' => 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z',
        ],
    ];

    $overrides = [
        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'info-circle' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'warning-triangle' =>
            'M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-2.99l-6.93-12a2 2 0 00-3.48 0l-6.93 12A2 2 0 005.07 19z',
        'x' => 'M6 18L18 6M6 6l12 12',
    ];

    $cfg = $config[$type] ?? $config['info'];
    if ($icon && isset($overrides[$icon])) {
        $cfg['iconPath'] = $overrides[$icon];
    }
    $hasContent = $message || (isset($slot) && $slot->isNotEmpty());
@endphp

@if ($hasContent)
    <div role="alert"
        {{ $attributes->merge(['class' => "flex items-start gap-3 p-3.5 sm:p-4 rounded-xl border text-xs sm:text-sm shadow-2xs transition-all {$cfg['bg']}"]) }}>

        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0 {{ $cfg['iconColor'] }} mt-0.5" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $cfg['iconPath'] }}" />
        </svg>

        <div class="flex-1 leading-relaxed">
            {{ $message ?? $slot }}
        </div>

        @if ($dismissible)
            <button type="button" onclick="this.closest('[role=alert]').remove()"
                class="btn btn-ghost btn-xs btn-circle text-base-content/50 hover:text-base-content -mr-1 -mt-1 shrink-0"
                aria-label="Tutup">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
    </div>
@endif
