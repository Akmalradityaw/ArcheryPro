@extends('layouts.app')

@section('title', 'Detail Atlet')
@section('page-title', 'Detail Atlet')

@section('content')
    <div class="mb-4">
        <a href="{{ route('pelatih.atlet.index') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-base-content/70 hover:text-primary transition-colors">
            <x-display.icon name="arrow-left" class="size-4 shrink-0" />
            <span>Kembali ke Daftar Atlet</span>
        </a>
    </div>

    <x-display.page-header title="{{ $atlet->nama_lengkap }}"
        subtitle="NIA: {{ $atlet->nia }} · {{ $atlet->sekolah->nama_sekolah ?? '-' }}" />

    {{-- Info Card Profil --}}
    <div class="card bg-base-100 border border-base-200 shadow-xs rounded-2xl p-5 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="size-14 rounded-2xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center font-bold text-xl uppercase shadow-xs shrink-0">
                    {{ strtoupper(substr($atlet->nama_lengkap, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-base-content tracking-tight">{{ $atlet->nama_lengkap }}</h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-primary/10 text-primary border border-primary/20">
                            {{ $atlet->kategori->nama_kategori ?? 'Tanpa Kategori' }}
                        </span>
                        <span class="text-xs text-base-content/50">•</span>
                        <span class="text-xs text-base-content/60 font-mono">NIA: {{ $atlet->nia }}</span>
                    </div>
                </div>
            </div>

            <div
                class="grid grid-cols-2 sm:flex sm:flex-row sm:items-center gap-2.5 w-full sm:w-auto sm:justify-end border-t sm:border-t-0 pt-4 sm:pt-0 border-base-200">
                <a href="{{ route('pelatih.atlet.edit', $atlet) }}"
                    class="btn btn-outline btn-sm rounded-xl w-full sm:w-auto inline-flex items-center justify-center flex-nowrap whitespace-nowrap gap-1.5">
                    <span>Edit Profile</span>
                </a>

                <x-form.btn-primary href="{{ route('pelatih.analisis.show', $atlet) }}" size="sm"
                    class="w-full sm:w-auto justify-center gap-1.5 rounded-xl">
                    <span>Analisis Skor</span>
                </x-form.btn-primary>
            </div>
        </div>
    </div>

    {{-- Grid Kartu Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <x-display.card-stat title="Total Sesi" :value="$ringkasan['sesi'] ?? 0" icon="calendar" />
        <x-display.card-stat title="Rata-rata Skor" :value="$ringkasan['rata'] ?? 0" icon="clock" color="info" />
        <x-display.card-stat title="Skor Terbaik" :value="$ringkasan['terbaik'] ?? 0" icon="users" color="success" />
    </div>
@endsection
