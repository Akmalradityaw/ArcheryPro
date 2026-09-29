@extends('layouts.app')

@section('title', 'Profil Atlet')
@section('page-title', 'Profil Atlet')

@section('content')
    <x-display.page-header title="Profil Atlet" subtitle="Lengkapi datamu agar bisa dinilai dan dipantau" />

    <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
        <div class="card-body p-5 sm:p-6 lg:p-8">
            <form method="POST" action="{{ route('atlet.profil.update') }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-4">
                    {{-- Kolom Kiri: Data Pribadi --}}
                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Data Pribadi
                        </h3>

                        <x-form.input-field name="nia" label="NIA" :value="$atlet->nia" required autofocus />

                        <x-form.input-field name="nama_lengkap" label="Nama Lengkap" :value="$atlet->nama_lengkap" required />

                        <x-form.input-field name="tempat_lahir" label="Tempat Lahir" :value="$atlet->tempat_lahir" />

                        <x-form.input-field name="tanggal_lahir" type="date" label="Tanggal Lahir" :value="$atlet->tanggal_lahir?->format('Y-m-d')" />

                        <x-form.input-field name="jenis_kelamin" type="select" label="Jenis Kelamin" :value="$atlet->jenis_kelamin"
                            :options="['' => '-', 'L' => 'Laki-laki', 'P' => 'Perempuan']" />

                        <x-form.input-field name="golongan_darah" label="Golongan Darah" :value="$atlet->golongan_darah" />

                        <x-form.input-field name="no_telepon" label="No. Telepon" :value="$atlet->no_telepon" />

                        <x-form.input-field name="alamat" type="textarea" label="Alamat" :value="$atlet->alamat" />
                    </div>

                    {{-- Kolom Kanan: Sekolah & Kesehatan --}}
                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Sekolah & Kesehatan
                        </h3>

                        <x-form.input-field name="sekolah_id" type="select" label="Sekolah" :value="$atlet->sekolah_id"
                            :options="['' => '-'] + $sekolah->pluck('nama_sekolah', 'id')->toArray()" />

                        <x-form.input-field name="kelas" label="Kelas" :value="$atlet->kelas" />

                        <x-form.input-field name="kategori_id" type="select" label="Kategori" :value="$atlet->kategori_id"
                            :options="['' => '-'] + $kategori->pluck('nama_kategori', 'id')->toArray()" />

                        <x-form.input-field name="nama_orangtua" label="Nama Orang Tua" :value="$atlet->nama_orangtua" />

                        <x-form.input-field name="no_telepon_orangtua" label="No. Telepon Orang Tua" :value="$atlet->no_telepon_orangtua" />

                        <x-form.input-field name="riwayat_cedera" type="textarea" label="Riwayat Cedera"
                            :value="$atlet->riwayat_cedera" />

                        <x-form.input-field name="alergi" type="textarea" label="Alergi" :value="$atlet->alergi" />
                    </div>

                    {{-- Pengaturan Privasi Papan Skor --}}
                    <div class="mt-6 pt-5 border-t border-base-200">
                        <h3 class="text-sm font-semibold uppercase tracking-wide text-base-content/70 mb-3">
                            Privasi & Publikasi Skor
                        </h3>
                        <div class="p-3.5 rounded-xl bg-base-200/50 border border-base-200">
                            <label class="label cursor-pointer justify-start gap-3 p-0">
                                <input type="checkbox" name="izinkan_tampil_publik" value="1"
                                    class="checkbox checkbox-primary border-base-300 rounded-md"
                                    @checked(old('izinkan_tampil_publik', $atlet->izinkan_tampil_publik ?? false)) />
                                <div class="text-xs">
                                    <span class="font-bold text-base-content block">Tampilkan skorku di papan peringkat publik</span>
                                    <span class="text-base-content/60">Jika tidak dicentang, hasil latihanmu hanya bisa dilihat oleh dirimu sendiri, pelatih, dan admin klub.</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5 mt-6 pt-4 border-t border-base-200">
                    <x-form.btn-primary type="submit" size="sm"
                        class="w-full sm:w-auto justify-center gap-1.5 rounded-xl">
                        <span>Simpan Profil</span>
                    </x-form.btn-primary>
                </div>
            </form>
        </div>
    </div>
@endsection
