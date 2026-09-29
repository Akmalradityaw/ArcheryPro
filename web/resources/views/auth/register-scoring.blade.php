@extends('layouts.auth')

@section('title', 'Daftar Petugas Scoring - ArcheryPro')

@section('content')

    {{-- Back Link & Navigation --}}
    <div class="mb-4">
        <a href="{{ route('login') }}"
            class="inline-flex items-center gap-1.5 text-xs text-base-content/60 hover:text-primary transition-colors">
            <x-display.icon name="arrow-left" class="size-3.5" />
            Kembali ke Halaman Masuk
        </a>
    </div>

    {{-- Header --}}
    <div class="mb-5">
        <div class="flex items-center gap-2 mb-1">
            <div class="size-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                <x-display.icon name="clipboard" class="size-4" />
            </div>
            <h1 class="text-2xl font-bold text-base-content tracking-tight">Daftar Petugas Scoring</h1>
        </div>
        <p class="text-xs sm:text-sm text-base-content/60">
            Pendaftaran Petugas Scoring memerlukan token otorisasi khusus dari Admin.
        </p>
    </div>

    {{-- Role Switcher Tab Controls --}}
    <div class="grid grid-cols-3 gap-1 p-1 bg-base-200/80 rounded-xl mb-5 text-center text-xs font-semibold">
        <a href="{{ route('register.atlet') }}"
            class="py-1.5 rounded-lg text-base-content/60 hover:text-base-content transition-colors">Atlet</a>
        <a href="{{ route('register.pelatih') }}"
            class="py-1.5 rounded-lg text-base-content/60 hover:text-base-content transition-colors">Pelatih</a>
        <a href="{{ route('register.scoring') }}" class="py-1.5 rounded-lg bg-base-100 shadow-sm text-primary">Scoring</a>
    </div>

    {{-- Warning Alert Token --}}
    <div
        class="flex items-start gap-3 p-3 rounded-xl bg-warning/10 border border-warning/30 text-warning-content mb-5 text-xs">
        <x-display.icon name="warning" class="size-4 shrink-0 text-warning mt-0.5" />
        <div>
            <p class="font-bold text-warning">Token Diperlukan</p>
            <p class="text-base-content/70 mt-0.5">Hubungi Admin kompetisi/sesi untuk mendapatkan token akses petugas
                scoring.</p>
        </div>
    </div>

    {{-- Registration Form --}}
    <form method="POST" action="{{ route('register.scoring') }}" class="space-y-4">
        @csrf

        {{-- Highlighted Token Field --}}
        <div class="p-3 bg-base-200/60 rounded-xl border border-base-300 space-y-2">
            <x-form.input-field name="token" label="Token Pendaftaran Admin" hint="wajib"
                placeholder="Masukkan kode token" :required="true" :autofocus="true" icon="key"
                class="font-mono tracking-wider text-primary font-bold uppercase" />
        </div>

        <x-form.input-field name="username" label="Username" placeholder="contoh: scoring_01" :required="true"
            autocomplete="username" icon="user" />

        <x-form.input-field name="email" type="email" label="Email Petugas" hint="wajib"
            placeholder="scoring@email.com" :required="true" autocomplete="email" icon="mail" />

        <x-form.input-field name="password" type="password" label="Password" hint="min. 8 karakter"
            placeholder="Buat password kuat" :required="true" autocomplete="new-password" icon="lock" />

        <x-form.input-field name="password_confirmation" type="password" label="Konfirmasi Password"
            placeholder="Ulangi password" :required="true" autocomplete="new-password" icon="lock" />

        <button type="submit" class="btn btn-primary w-full shadow-sm shadow-primary/20 normal-case font-semibold mt-2">
            Daftar sebagai Petugas Scoring
            <x-display.icon name="arrow-right" class="size-4" />
        </button>
    </form>

    <p class="text-center text-xs text-base-content/60 mt-6">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="link link-primary font-semibold no-underline hover:underline">Masuk di
            sini</a>
    </p>

@endsection
