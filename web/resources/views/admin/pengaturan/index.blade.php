@extends('layouts.app')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan')

@section('content')
    <x-display.page-header title="Pengaturan Sistem" subtitle="Token pendaftaran, format pencatatan, dan jadwal backup" />

    <form method="POST" action="{{ route('admin.pengaturan.update') }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- KOLOM KIRI --}}
            <div class="space-y-5">
                {{-- Token Registrasi --}}
                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body p-5 sm:p-6">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-4">
                            Token Registrasi
                        </h3>

                        <div class="space-y-3">
                            <x-form.input-field name="token_pelatih" label="Token Pelatih" hint="untuk registrasi pelatih"
                                placeholder="PELATIH2025" required icon="lock" class="font-mono tracking-wider" />

                            <x-form.input-field name="token_scoring" label="Token Scoring"
                                hint="untuk registrasi petugas scoring" placeholder="SKORING2025" required icon="lock"
                                class="font-mono tracking-wider" />
                        </div>

                        <x-feedback.alert type="warning" icon="warning-triangle" class="mt-4">
                            <span class="text-xs text-base-content/70">
                                Perubahan token akan memengaruhi proses registrasi berikutnya.
                                Token lama tidak akan berfungsi lagi.
                            </span>
                        </x-feedback.alert>
                    </div>
                </div>

                {{-- Backup Otomatis --}}
                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body p-5 sm:p-6">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-4">
                            Backup Otomatis
                        </h3>

                        <x-form.input-field name="backup_otomatis" type="select" label="Jadwal Backup" :value="$settings['backup_otomatis'] ?? 'harian'"
                            :options="[
                                'harian' => 'Harian — setiap hari',
                                'mingguan' => 'Mingguan — setiap 7 hari',
                                'bulanan' => 'Bulanan — setiap 30 hari',
                                'off' => 'Off — backup manual saja',
                            ]" />

                        <x-feedback.alert type="neutral" icon="clock" class="mt-3">
                            <span class="text-xs text-base-content/60">
                                Backup otomatis dijalankan oleh scheduler Laravel.
                                Pastikan cron sudah dikonfigurasi di server.
                            </span>
                        </x-feedback.alert>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN --}}
            <div class="space-y-5">
                {{-- Format Skor --}}
                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body p-5 sm:p-6">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-4">
                            Format Pencatatan Skor
                        </h3>

                        <x-form.input-field name="jumlah_panah_per_end" type="number" label="Jumlah Panah per End"
                            hint="default 6 (standar World Archery)" :value="$settings['jumlah_panah_per_end'] ?? 6" required min="1"
                            max="12" />

                        <x-feedback.alert type="neutral" icon="info-circle" class="mt-3">
                            <div class="text-xs text-base-content/60">
                                <p class="font-medium text-base-content/80">Format Standar:</p>
                                <ul class="list-disc list-inside mt-1 space-y-0.5">
                                    <li>World Archery: 6 panah per end</li>
                                    <li>Setiap anak panah: nilai 0-10</li>
                                    <li>Total maksimal per end: 60</li>
                                </ul>
                            </div>
                        </x-feedback.alert>
                    </div>
                </div>

                {{-- Privasi & Akses Publik --}}
                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body p-5 sm:p-6">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-4">
                            Privasi & Akses Publik
                        </h3>

                        <x-form.input-field name="leaderboard_publik" type="select" label="Akses Papan Skor Publik" :value="$settings['leaderboard_publik'] ?? '1'"
                            :options="[
                                '1' => 'Aktif — Papan skor latihan dapat dilihat publik tanpa login',
                                '0' => 'Nonaktif — Hanya atlet, pelatih, dan admin yang dapat melihat skor',
                            ]" />

                        <x-feedback.alert type="neutral" icon="info-circle" class="mt-3">
                            <span class="text-xs text-base-content/60">
                                Saat aktif, atlet tetap memegang kontrol atas profilnya sendiri melalui opsi 'Izinkan skor tampil publik'.
                            </span>
                        </x-feedback.alert>
                    </div>
                </div>

                {{-- Info Sistem --}}
                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body p-5 sm:p-6">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-4">
                            Info Sistem
                        </h3>

                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between gap-2">
                                <dt class="text-base-content/60">Laravel</dt>
                                <dd class="font-mono">v{{ app()->version() }}</dd>
                            </div>
                            <div class="flex justify-between gap-2">
                                <dt class="text-base-content/60">PHP</dt>
                                <dd class="font-mono">v{{ PHP_VERSION }}</dd>
                            </div>
                            <div class="flex justify-between gap-2">
                                <dt class="text-base-content/60">Timezone</dt>
                                <dd class="font-mono">{{ config('app.timezone') }}</dd>
                            </div>
                            <div class="flex justify-between gap-2">
                                <dt class="text-base-content/60">Environment</dt>
                                <dd>
                                    <span
                                        class="badge badge-sm {{ app()->environment('production') ? 'badge-success' : 'badge-warning' }}">
                                        {{ app()->environment() }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl mt-5">
            <div class="card-body p-4 sm:p-5">
                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2.5">
                    <p class="text-xs text-base-content/50">
                        Perubahan akan langsung berlaku setelah disimpan.
                    </p>
                    <x-form.btn-primary type="submit" size="sm"
                        class="w-full sm:w-auto justify-center gap-1.5 rounded-xl">
                        <span>Simpan Pengaturan</span>
                    </x-form.btn-primary>
                </div>
            </div>
        </div>
    </form>
@endsection
