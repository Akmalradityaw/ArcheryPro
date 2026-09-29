@extends('layouts.app')

@section('title', 'Detail Atlet')
@section('page-title', 'Detail Atlet')

@section('content')
    <x-display.page-header title="Detail Atlet" subtitle="Profil lengkap dan riwayat sesi">
        <x-slot:actions>
            <a href="{{ route('admin.atlet.index') }}" class="btn btn-ghost btn-sm gap-1">
                <x-display.icon name="chevron-left" class="size-4" />
                Kembali
            </a>
            <a href="{{ route('admin.atlet.edit', $atlet) }}" class="btn btn-primary btn-sm">Edit</a>
        </x-slot:actions>
    </x-display.page-header>

    {{-- Card Profil --}}
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
                        <x-display.badge-status :status="$atlet->status" />
                        @if ($atlet->kategori)
                            <span class="text-xs text-base-content/50">•</span>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-primary/10 text-primary border border-primary/20">
                                {{ $atlet->kategori->nama_kategori }}
                            </span>
                        @endif
                        <span class="text-xs text-base-content/50">•</span>
                        <span class="text-xs text-base-content/60 font-mono">NIA: {{ $atlet->nia }}</span>
                    </div>
                </div>
            </div>

            <div
                class="grid grid-cols-2 sm:flex sm:flex-row sm:items-center gap-2.5 w-full sm:w-auto sm:justify-end border-t sm:border-t-0 pt-4 sm:pt-0 border-base-200">
                <a href="{{ route('admin.atlet.edit', $atlet) }}"
                    class="btn btn-outline btn-sm rounded-xl w-full sm:w-auto inline-flex items-center justify-center flex-nowrap whitespace-nowrap">
                    <span>Edit</span>
                </a>
                <button type="button" onclick="document.getElementById('modal-toggle-status').showModal()"
                    class="btn {{ $atlet->status === 'aktif' ? 'btn-warning' : 'btn-success' }} btn-sm rounded-xl w-full sm:w-auto">
                    {{ $atlet->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
            </div>
        </div>
    </div>

    {{-- Grid Info --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Kolom 1: Data Pribadi --}}
        <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
            <div class="card-body p-5">
                <h3
                    class="font-semibold text-sm uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-3">
                    Data Pribadi
                </h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-2">
                        <dt class="text-base-content/60">Jenis Kelamin</dt>
                        <dd class="font-medium text-right">
                            {{ $atlet->jenis_kelamin === 'L' ? 'Laki-laki' : ($atlet->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-base-content/60">TTL</dt>
                        <dd class="font-medium text-right">
                            {{ $atlet->tempat_lahir ?? '-' }}
                            @if ($atlet->tanggal_lahir)
                                , {{ $atlet->tanggal_lahir->format('d/m/Y') }}
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-base-content/60">Gol. Darah</dt>
                        <dd class="font-medium text-right">{{ $atlet->golongan_darah ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Kolom 2: Olahraga & Kontak --}}
        <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
            <div class="card-body p-5">
                <h3
                    class="font-semibold text-sm uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-3">
                    Olahraga & Kontak
                </h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between gap-2">
                        <dt class="text-base-content/60">Sekolah</dt>
                        <dd class="font-medium text-right">{{ $atlet->sekolah->nama_sekolah ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-base-content/60">Kelas</dt>
                        <dd class="font-medium text-right">{{ $atlet->kelas ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-base-content/60">Telp.</dt>
                        <dd class="font-medium text-right">{{ $atlet->no_telepon ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between gap-2">
                        <dt class="text-base-content/60">Wali</dt>
                        <dd class="font-medium text-right">{{ $atlet->nama_orangtua ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Kolom 3: Sesi Terbaru --}}
        <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
            <div class="card-body p-5">
                <h3
                    class="font-semibold text-sm uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-3">
                    Sesi Terbaru
                </h3>

                @forelse ($sesiTerbaru as $sesi)
                    <div class="flex items-center justify-between gap-2 py-1.5 text-sm">
                        <span class="text-base-content/60 font-mono text-xs">
                            {{ $sesi->tanggal_sesi->format('d/m/Y') }}
                        </span>
                        <span class="font-bold text-primary">{{ $sesi->total_skor }}</span>
                    </div>
                @empty
                    <p class="text-sm text-base-content/50 text-center py-4">Belum ada sesi.</p>
                @endforelse
            </div>
        </div>
    </div>

    @push('modals')
        <x-feedback.modal-confirm id="modal-toggle-status"
            title="{{ $atlet->status === 'aktif' ? 'Nonaktifkan Atlet?' : 'Aktifkan Atlet?' }}" :message="'Status atlet ' . $atlet->nama_lengkap . ' akan diubah menjadi ' . ($atlet->status === 'aktif' ? 'Tidak Aktif' : 'Aktif') . '.'"
            :action="route('admin.atlet.verifikasi', $atlet)" method="POST" variant="{{ $atlet->status === 'aktif' ? 'warning' : 'success' }}"
            confirmText="Ya, Lanjutkan" />
    @endpush
@endsection
