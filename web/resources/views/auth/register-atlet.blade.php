@extends('layouts.auth')

@section('title', 'Daftar Atlet - ArcheryPro')

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
        <h1 class="text-2xl font-bold text-base-content tracking-tight">Daftar sebagai Atlet</h1>
        <p class="text-xs sm:text-sm text-base-content/60 mt-1">
            Buat akun untuk mulai memantau dan mencatat riwayat skor Anda.
        </p>
    </div>

    {{-- Role Switcher Tab Controls --}}
    <div class="grid grid-cols-3 gap-1 p-1 bg-base-200/80 rounded-xl mb-5 text-center text-xs font-semibold">
        <a href="{{ route('register.atlet') }}" class="py-1.5 rounded-lg bg-base-100 shadow-sm text-primary">Atlet</a>
        <a href="{{ route('register.pelatih') }}"
            class="py-1.5 rounded-lg text-base-content/60 hover:text-base-content transition-colors">Pelatih</a>
        <a href="{{ route('register.scoring') }}"
            class="py-1.5 rounded-lg text-base-content/60 hover:text-base-content transition-colors">Scoring</a>
    </div>

    {{-- Info Alert --}}
    <div class="flex items-start gap-3 p-3 rounded-xl bg-primary/10 border border-primary/20 text-primary mb-5 text-xs">
        <x-display.icon name="info" class="size-4 shrink-0 mt-0.5" />
        <div>
            <p class="font-bold">Pendaftaran Langsung (Tanpa Token)</p>
            <p class="text-base-content/70 mt-0.5">Kelengkapan profil atlet (NIA, kategori, klub) dapat diisi setelah
                pendaftaran selesai.</p>
        </div>
    </div>

    {{-- Registration Form --}}
    <form method="POST" action="{{ route('register.atlet') }}" class="space-y-4">
        @csrf

        <x-form.input-field name="username" label="Username" hint="min. 3 karakter" placeholder="contoh: budi_archery"
            :required="true" :autofocus="true" autocomplete="username" icon="user" />

        <x-form.input-field name="email" type="email" label="Email" hint="opsional" placeholder="nama@email.com"
            autocomplete="email" icon="mail" />

        <x-form.input-field name="password" type="password" label="Password" hint="min. 8 karakter"
            placeholder="Buat password kuat" :required="true" autocomplete="new-password" icon="lock" />

        <x-form.input-field name="password_confirmation" type="password" label="Konfirmasi Password"
            placeholder="Ulangi password" :required="true" autocomplete="new-password" icon="lock" />

        <button type="submit" class="btn btn-primary w-full shadow-sm shadow-primary/20 normal-case font-semibold mt-2">
            Daftar Sekarang
            <x-display.icon name="arrow-right" class="size-4" />
        </button>
    </form>

    <p class="text-center text-xs text-base-content/60 mt-6">
        Sudah memiliki akun?
        <a href="{{ route('login') }}" class="link link-primary font-semibold no-underline hover:underline">Masuk di
            sini</a>
    </p>

@endsection
