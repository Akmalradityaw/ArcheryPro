@props(['name', 'class' => 'w-4 h-4'])

@php
    $heroiconMap = [
        'calendar' => 'heroicon-o-calendar-days',
        'pin' => 'heroicon-o-map-pin',
        'map-pin' => 'heroicon-o-map-pin',
        'arrow-right' => 'heroicon-o-arrow-right',
        'arrow-left' => 'heroicon-o-arrow-left',
        'check' => 'heroicon-o-check',
        'check-circle' => 'heroicon-o-check-circle',
        'trophy' => 'heroicon-o-trophy',
        'user' => 'heroicon-o-user',
        'users' => 'heroicon-o-users',
        'user-circle' => 'heroicon-o-user-circle',
        'user-plus' => 'heroicon-o-user-plus',
        'lock' => 'heroicon-o-lock-closed',
        'mail' => 'heroicon-o-envelope',
        'key' => 'heroicon-o-key',
        'info' => 'heroicon-o-information-circle',
        'info-circle' => 'heroicon-o-information-circle',
        'warning' => 'heroicon-o-exclamation-triangle',
        'warning-triangle' => 'heroicon-o-exclamation-triangle',
        'academic' => 'heroicon-o-academic-cap',
        'clipboard' => 'heroicon-o-clipboard-document-list',
        'grid' => 'heroicon-o-squares-2x2',
        'tag' => 'heroicon-o-tag',
        'cog' => 'heroicon-o-cog-6-tooth',
        'download' => 'heroicon-o-arrow-down-tray',
        'download-doc' => 'heroicon-o-document-arrow-down',
        'upload' => 'heroicon-o-arrow-up-tray',
        'chart' => 'heroicon-o-chart-bar',
        'chart-line' => 'heroicon-o-presentation-chart-line',
        'clock' => 'heroicon-o-clock',
        'plus' => 'heroicon-o-plus',
        'home' => 'heroicon-o-home',
        'menu' => 'heroicon-o-bars-3',
        'log-in' => 'heroicon-o-arrow-left-on-rectangle',
        'log-out' => 'heroicon-o-arrow-right-on-rectangle',
        'search' => 'heroicon-o-magnifying-glass',
        'chevron-left' => 'heroicon-o-chevron-left',
        'chevron-right' => 'heroicon-o-chevron-right',
        'chevron-down' => 'heroicon-o-chevron-down',
        'trash' => 'heroicon-o-trash',
        'pencil' => 'heroicon-o-pencil-square',
        'edit' => 'heroicon-o-pencil-square',
        'eye' => 'heroicon-o-eye',
        'refresh' => 'heroicon-o-arrow-path',
        'x' => 'heroicon-o-x-mark',
        'document-duplicate' => 'heroicon-o-document-duplicate',
        'star' => 'heroicon-o-star',
        'trend-up' => 'heroicon-o-arrow-trending-up',
        'trend-down' => 'heroicon-o-arrow-trending-down',
        'flag' => 'heroicon-o-flag',
        'filter' => 'heroicon-o-funnel',
        'bolt' => 'heroicon-o-bolt',
    ];

    $heroiconName = str_starts_with($name, 'heroicon-') 
        ? $name 
        : ($heroiconMap[$name] ?? null);

    // Custom archery-specific and non-standard icons
    $customPaths = [
        'target' =>
            '<circle cx="12" cy="12" r="9" /><circle cx="12" cy="12" r="4" /><circle cx="12" cy="12" r="1" fill="currentColor" />',
        'award' =>
            '<circle cx="12" cy="8" r="6" /><path stroke-linecap="round" stroke-linejoin="round" d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11" />',
        'activity' =>
            '<path stroke-linecap="round" stroke-linejoin="round" d="M22 12h-4l-3 9L9 3l-3 9H2" />',
    ];

    $renderedSvg = null;
    if ($heroiconName && function_exists('svg')) {
        try {
            $renderedSvg = svg($heroiconName, $attributes->merge(['class' => $class])->getAttributes());
        } catch (\Throwable $e) {
            $renderedSvg = null;
        }
    }
@endphp

@if ($renderedSvg)
    {{ $renderedSvg }}
@else
    <svg xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => $class]) }} fill="none" viewBox="0 0 24 24"
        stroke="currentColor" stroke-width="1.5" aria-hidden="true">{!! $customPaths[$name] ?? $customPaths['target'] !!}</svg>
@endif
