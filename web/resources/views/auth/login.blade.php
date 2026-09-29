@extends('layouts.auth')

@section('title', 'Masuk - ArcheryPro')

@section('content')

    {{-- Form Header --}}
    <div class="mb-6">
        <span class="badge badge-primary badge-soft text-xs font-semibold mb-2">Portal Akses</span>
        <h1 class="text-2xl font-bold text-base-content tracking-tight">Selamat Datang Kembali</h1>
        <p class="text-xs sm:text-sm text-base-content/60 mt-1">
            Silakan masukkan kredensial akun Anda untuk mengakses sistem.
        </p>
    </div>

    {{-- Form Input --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <x-form.input-field name="username" label="Username" placeholder="Masukkan username Anda" :required="true"
            :autofocus="true" autocomplete="username" icon="user" />

        <x-form.input-field name="password" label="Password" type="password" placeholder="••••••••" :required="true"
            autocomplete="current-password" icon="lock" />

        <div class="flex items-center justify-between pt-1">
            <label class="label cursor-pointer justify-start gap-2.5 p-0 select-none">
                <input type="checkbox" name="remember" class="checkbox checkbox-primary checkbox-sm rounded-md">
                <span class="label-text text-xs text-base-content/80 font-medium">Ingat saya di perangkat ini</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary w-full shadow-sm shadow-primary/20 normal-case font-semibold mt-2">
            Masuk ke Akun
            <x-display.icon name="arrow-right" class="size-4" />
        </button>
    </form>

    {{-- Registration Options Divider --}}
    <div class="divider text-xs text-base-content/40 my-6 font-medium">Belum memiliki akun?</div>

    {{-- Interactive Role Registration Grid Cards --}}
    <div class="space-y-2">
        <p class="text-xs font-semibold text-base-content/70 mb-2">Pilih peran untuk mendaftar:</p>

        <a href="{{ route('register.atlet') }}"
            class="group flex items-center justify-between p-3 rounded-xl border border-base-200 hover:border-primary/40 hover:bg-primary/5 transition-all">
            <div class="flex items-center gap-3">
                <div
                    class="size-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-primary-content transition-colors">
                    <x-display.icon name="target" class="size-4" />
                </div>
                <div>
                    <p class="text-xs font-bold text-base-content group-hover:text-primary transition-colors">Atlet</p>
                    <p class="text-[11px] text-base-content/50">Tanpa token pendaftaran</p>
                </div>
            </div>
            <x-display.icon name="arrow-right"
                class="size-4 text-base-content/30 group-hover:text-primary group-hover:translate-x-0.5 transition-all" />
        </a>

        <a href="{{ route('register.pelatih') }}"
            class="group flex items-center justify-between p-3 rounded-xl border border-base-200 hover:border-primary/40 hover:bg-primary/5 transition-all">
            <div class="flex items-center gap-3">
                <div
                    class="size-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-primary-content transition-colors">
                    <x-display.icon name="academic" class="size-4" />
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <p class="text-xs font-bold text-base-content group-hover:text-primary transition-colors">Pelatih
                        </p>
                        <span class="badge badge-warning badge-xs text-[10px] font-medium px-1.5">Wajib Token</span>
                    </div>
                    <p class="text-[11px] text-base-content/50">Manajemen tim & atlet</p>
                </div>
            </div>
            <x-display.icon name="arrow-right"
                class="size-4 text-base-content/30 group-hover:text-primary group-hover:translate-x-0.5 transition-all" />
        </a>

        <a href="{{ route('register.scoring') }}"
            class="group flex items-center justify-between p-3 rounded-xl border border-base-200 hover:border-primary/40 hover:bg-primary/5 transition-all">
            <div class="flex items-center gap-3">
                <div
                    class="size-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-primary-content transition-colors">
                    <x-display.icon name="clipboard" class="size-4" />
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <p class="text-xs font-bold text-base-content group-hover:text-primary transition-colors">Petugas
                            Scoring</p>
                        <span class="badge badge-warning badge-xs text-[10px] font-medium px-1.5">Wajib Token</span>
                    </div>
                    <p class="text-[11px] text-base-content/50">Input skor sesi latihan & lomba</p>
                </div>
            </div>
            <x-display.icon name="arrow-right"
                class="size-4 text-base-content/30 group-hover:text-primary group-hover:translate-x-0.5 transition-all" />
        </a>
    </div>

@endsection
