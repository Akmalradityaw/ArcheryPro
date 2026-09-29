@props(['id', 'nama', 'sub' => null, 'checked' => false, 'name' => 'atlet_ids[]'])

@php
    $domId = 'atlet-' . \Illuminate\Support\Str::slug((string) $id);
@endphp

<label for="{{ $domId }}"
    class="relative flex items-center gap-3 p-3.5 border border-base-300 bg-base-100 rounded-xl cursor-pointer transition-all duration-150 select-none
           hover:border-primary/50 hover:bg-primary/5
           has-[:checked]:border-primary has-[:checked]:bg-primary/5 has-[:checked]:ring-2 has-[:checked]:ring-primary/20">

    <input type="checkbox" id="{{ $domId }}" name="{{ $name }}" value="{{ $id }}"
        @checked($checked)
        {{ $attributes->merge(['class' => 'checkbox checkbox-primary checkbox-sm shrink-0 rounded-md transition-transform active:scale-95']) }} />

    <span class="flex-1 min-w-0">
        <span class="block font-semibold text-sm text-base-content leading-tight truncate">{{ $nama }}</span>
        @if ($sub)
            <span class="block text-xs text-base-content/60 truncate mt-0.5">{{ $sub }}</span>
        @endif
    </span>
</label>
