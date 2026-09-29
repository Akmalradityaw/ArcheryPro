{{-- Flash messages --}}
@foreach (['success' => 'success', 'error' => 'error', 'warning' => 'warning', 'info' => 'info', 'status' => 'info'] as $key => $type)
    @if (session($key))
        <x-feedback.alert :type="$type" :message="session($key)" :dismissible="true" class="mb-3" />
    @endif
@endforeach

{{-- Global validation errors (opsional) --}}
@if ($errors->any() && !request()->routeIs('*.create', '*.edit'))
    <x-feedback.alert type="error" :dismissible="true" class="mb-3">
        <div>
            <p class="font-semibold">Terdapat {{ $errors->count() }} kesalahan pada input:</p>
            <ul class="list-disc list-inside mt-1 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </x-alert>
@endif
