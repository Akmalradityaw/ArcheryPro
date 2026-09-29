@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
    'hint' => null,
    'required' => false,
    'autofocus' => false,
    'autocomplete' => null,
    'readonly' => false,
    'disabled' => false,
    'icon' => null,
    'togglePassword' => false,
    'options' => [], // Untuk type="select": [value => label]
    'rows' => 3, // Untuk type="textarea"
])

@php
    $hasError = isset($errors) && $errors->has($name);
    $inputId = $name . '_' . \Illuminate\Support\Str::random(6);
    $isPassword = $type === 'password';
    $showToggle = $togglePassword && $isPassword;
    $currentValue = old($name, $value);
@endphp

<div class="form-control w-full space-y-1" @if ($showToggle) x-data="{ show: false }" @endif>
    @if ($label)
        <label class="flex items-center justify-between text-xs font-semibold text-base-content/80 px-0.5"
            for="{{ $inputId }}">
            <span>
                {{ $label }}
                @if ($required)
                    <span class="text-error font-bold ml-0.5">*</span>
                @endif
            </span>
            @if ($hint)
                <span class="text-xs font-normal text-base-content/50">{{ $hint }}</span>
            @endif
        </label>
    @endif

    <div
        class="relative flex items-center w-full border rounded-xl px-3 bg-base-100 transition-all duration-150 shadow-sm
                {{ $hasError
                    ? 'border-error ring-1 ring-error/30 focus-within:border-error focus-within:ring-2 focus-within:ring-error/20'
                    : 'border-base-300 hover:border-base-400 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20' }}
                {{ $readonly || $disabled ? 'bg-base-200/60 cursor-not-allowed opacity-80' : '' }}">

        @if ($icon)
            <span class="text-base-content/40 shrink-0 mr-2 flex items-center justify-center">
                @if (str_starts_with(trim($icon), '<'))
                    {!! $icon !!}
                @else
                    <x-display.icon :name="$icon" class="size-4" />
                @endif
            </span>
        @endif

        @if ($type === 'select')
            <select id="{{ $inputId }}" name="{{ $name }}" @required($required) @disabled($disabled || $readonly)
                {{ $attributes->merge(['class' => 'w-full bg-transparent border-0 outline-none focus:outline-none focus:ring-0 py-2.5 text-sm text-base-content cursor-pointer']) }}>
                @if (isset($slot) && $slot->isNotEmpty())
                    {{ $slot }}
                @else
                    @foreach ($options as $val => $lbl)
                        <option value="{{ $val }}" @selected((string) $currentValue === (string) $val)>
                            {{ $lbl }}
                        </option>
                    @endforeach
                @endif
            </select>
        @elseif ($type === 'textarea')
            <textarea id="{{ $inputId }}" name="{{ $name }}" rows="{{ $rows }}"
                placeholder="{{ $placeholder }}" @required($required) @readonly($readonly) @disabled($disabled)
                {{ $attributes->merge(['class' => 'w-full bg-transparent border-0 outline-none focus:outline-none focus:ring-0 py-2.5 text-sm text-base-content placeholder:text-base-content/40 resize-y']) }}>{{ $currentValue }}</textarea>
        @else
            <input id="{{ $inputId }}"
                @if ($showToggle) :type="show ? 'text' : 'password'" @else type="{{ $type }}" @endif
                name="{{ $name }}" value="{{ $currentValue }}" placeholder="{{ $placeholder }}"
                @required($required) @autofocus($autofocus) @readonly($readonly) @disabled($disabled)
                @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
                {{ $attributes->merge(['class' => 'w-full bg-transparent border-0 outline-none focus:outline-none focus:ring-0 py-2.5 text-sm text-base-content placeholder:text-base-content/40']) }} />
        @endif

        @if ($showToggle)
            <button type="button" @click="show = !show"
                class="btn btn-ghost btn-xs btn-circle ml-1 shrink-0 text-base-content/50 hover:text-base-content"
                tabindex="-1" :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                </svg>
            </button>
        @endif
    </div>

    <x-form.input-error :for="$name" />
</div>
