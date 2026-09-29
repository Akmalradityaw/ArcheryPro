@props([
    'messages' => [],
    'for' => null,
])

@php
    if ($for && isset($errors)) {
        $messages = $errors->get($for);
    }
    $messages = array_filter((array) $messages);
@endphp

@if (!empty($messages))
    <div {{ $attributes->merge(['class' => 'mt-1.5 space-y-1']) }}>
        @foreach ($messages as $message)
            <p class="text-error text-xs font-medium flex items-center gap-1.5 leading-snug">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 shrink-0" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <path stroke-linecap="round" d="M12 8v4M12 16h.01" />
                </svg>
                <span>{{ $message }}</span>
            </p>
        @endforeach
    </div>
@endif
