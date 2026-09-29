@props([
    'id',
    'title' => 'Konfirmasi Tindakan',
    'message' => 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    'action' => '#',
    'method' => 'DELETE',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'variant' => 'error', // error | warning | primary
])

@php
    $styles = match ($variant) {
        'warning' => [
            'btn' => 'btn-warning text-warning-content',
            'bg' => 'bg-warning/10 border-warning/20',
            'iconText' => 'text-warning',
        ],
        'primary' => [
            'btn' => 'btn-primary',
            'bg' => 'bg-primary/10 border-primary/20',
            'iconText' => 'text-primary',
        ],
        default => [
            'btn' => 'btn-error text-error-content',
            'bg' => 'bg-error/10 border-error/20',
            'iconText' => 'text-error',
        ],
    };
@endphp

<dialog id="{{ $id }}" class="modal modal-bottom sm:modal-middle backdrop-blur-xs transition-all">
    <div class="modal-box rounded-2xl border border-base-200/80 p-6 shadow-2xl">
        <div class="flex items-start gap-4">
            <div class="size-12 rounded-xl {{ $styles['bg'] }} border flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6 {{ $styles['iconText'] }}" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-extrabold text-lg text-base-content tracking-tight">{{ $title }}</h3>
                <p class="py-1.5 text-xs sm:text-sm text-base-content/70 leading-relaxed">{{ $message }}</p>
            </div>
        </div>

        @if ($slot->isNotEmpty())
            <div class="mt-4 pt-3 border-t border-base-200/60">{{ $slot }}</div>
        @endif

        <div class="modal-action flex items-center justify-end gap-2 mt-6">
            <form method="dialog">
                <button class="btn btn-ghost btn-sm normal-case font-medium rounded-xl">{{ $cancelText }}</button>
            </form>
            <form method="POST" action="{{ $action }}">
                @csrf
                @method($method)
                <button type="submit"
                    class="btn btn-sm {{ $styles['btn'] }} shadow-xs normal-case font-semibold rounded-xl">
                    {{ $confirmText }}
                </button>
            </form>
        </div>
    </div>

    <form method="dialog" class="modal-backdrop">
        <button class="cursor-default">close</button>
    </form>
</dialog>
