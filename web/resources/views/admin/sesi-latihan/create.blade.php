@extends('layouts.app')

@section('title', 'Tambah Jadwal Latihan')
@section('page-title', 'Tambah Jadwal Latihan')

@section('content')
    <x-display.page-header title="Tambah Jadwal Latihan" subtitle="Jadwalkan agenda latihan rutin mingguan klub atau sesi evaluasi teknik" />

    <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
        <div class="card-body p-5 sm:p-6 lg:p-8">
            <form method="POST" action="{{ route('admin.sesi-latihan.store') }}">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-4">
                    {{-- Kolom Kiri: Informasi Sesi --}}
                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Agenda Latihan
                        </h3>

                        <x-form.input-field name="nama_sesi" label="Nama Sesi Latihan"
                            placeholder="contoh: Latihan Rutin Pekan 39 - Scoring Jarak 30m" required autofocus />

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-1">
                                <x-form.input-field name="tanggal" type="date" label="Tanggal" required
                                    :value="date('Y-m-d')" />
                            </div>
                            <div>
                                <x-form.input-field name="jam_mulai" type="time" label="Jam Mulai" value="08:00" />
                            </div>
                            <div>
                                <x-form.input-field name="jam_selesai" type="time" label="Jam Selesai" value="11:00" />
                            </div>
                        </div>

                        <x-form.input-field name="lokasi" label="Lokasi Lapangan" placeholder="contoh: Lapangan Utama Panahan"
                            value="Lapangan Utama" />

                        <x-form.input-field name="fokus_latihan" label="Fokus / Materi Latihan"
                            placeholder="contoh: Koreksi anchor point & uji konsistensi 36 anak panah" />
                    </div>

                    {{-- Kolom Kanan: Pengaturan & Status --}}
                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Format & Visibilitas
                        </h3>

                        <x-form.input-field name="jenis_latihan" type="select" label="Jenis Latihan" :options="[
                            'rutin_mingguan' => 'Latihan Rutin Mingguan',
                            'mandiri' => 'Latihan Mandiri / Bebas',
                            'evaluasi_skor' => 'Evaluasi Skor & Akurasi',
                            'simulasi' => 'Simulasi Skoring Kompetisi',
                        ]" required />

                        <x-form.input-field name="status" type="select" label="Status Latihan" :options="[
                            'terjadwal' => 'Terjadwal (Belum dimulai)',
                            'berlangsung' => 'Sedang Berlangsung',
                            'selesai' => 'Selesai',
                            'batal' => 'Dibatalkan',
                        ]" required />

                        <div class="form-control">
                            <label class="label pb-1 cursor-pointer">
                                <span class="label-text font-medium">Tampilkan ke Jadwal Publik</span>
                            </label>
                            <label
                                class="flex items-start gap-3 p-3 border border-base-300 rounded-lg cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition has-[:checked]:border-primary has-[:checked]:bg-primary/5 has-[:checked]:ring-1 has-[:checked]:ring-primary/20">
                                <input type="checkbox" name="is_publik" value="1" @checked(old('is_publik', true))
                                    class="checkbox checkbox-primary checkbox-sm shrink-0 mt-0.5">
                                <span class="text-sm">
                                    <span class="block font-medium">Tampilkan agenda ini di kalender publik klub</span>
                                    <span class="block text-xs text-base-content/60 mt-0.5">
                                        Anggota dan masyarakat umum dapat melihat pengumuman jadwal latihan ini.
                                    </span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5 mt-6 pt-4 border-t border-base-200">
                    <a href="{{ route('admin.sesi-latihan.index') }}"
                        class="btn btn-ghost btn-sm rounded-xl w-full sm:w-auto text-center">
                        Batal
                    </a>
                    <x-form.btn-primary type="submit" size="sm"
                        class="w-full sm:w-auto justify-center gap-1.5 rounded-xl">
                        <span>Simpan Jadwal Latihan</span>
                    </x-form.btn-primary>
                </div>
            </form>
        </div>
    </div>
@endsection
