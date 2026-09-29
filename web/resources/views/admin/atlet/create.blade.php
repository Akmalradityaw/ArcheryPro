@extends('layouts.app')

@section('title', 'Tambah Atlet')
@section('page-title', 'Tambah Atlet')

@section('content')
    <x-display.page-header title="Tambah Atlet" subtitle="Buat akun sekaligus profil atlet" />

    <form method="POST" action="{{ route('admin.atlet.store') }}">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- KOLOM KIRI --}}
            <div class="space-y-5">
                {{-- Data Akun --}}
                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body p-5 sm:p-6">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-4">
                            Data Akun
                        </h3>

                        <div class="space-y-3">
                            <x-form.input-field name="username" label="Username" hint="min. 3 karakter"
                                placeholder="contoh: budi_santoso" required autofocus autocomplete="username" />

                            <x-form.input-field name="password" type="password" label="Password" hint="min. 8 karakter"
                                placeholder="Buat password" required togglePassword autocomplete="new-password" />

                            <x-form.input-field name="email" type="email" label="Email" hint="opsional"
                                placeholder="nama@email.com" autocomplete="email" />
                        </div>
                    </div>
                </div>

                {{-- Data Pribadi --}}
                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body p-5 sm:p-6">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-4">
                            Data Pribadi
                        </h3>

                        <div class="space-y-3">
                            <x-form.input-field name="nia" label="Nomor Induk Atlet (NIA)" hint="unik"
                                placeholder="contoh: ATL-2025-001" required />

                            <x-form.input-field name="nama_lengkap" label="Nama Lengkap" placeholder="Nama sesuai akta"
                                required />

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <x-form.input-field name="jenis_kelamin" type="select" label="Jenis Kelamin"
                                    :options="['' => '- Pilih -', 'L' => 'Laki-laki', 'P' => 'Perempuan']" />

                                <x-form.input-field name="tanggal_lahir" type="date" label="Tanggal Lahir" />
                            </div>

                            <x-form.input-field name="tempat_lahir" label="Tempat Lahir" placeholder="contoh: Jakarta" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN --}}
            <div class="space-y-5">
                {{-- Data Olahraga --}}
                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body p-5 sm:p-6">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-4">
                            Data Olahraga
                        </h3>

                        <div class="space-y-3">
                            <x-form.input-field name="sekolah_id" type="select" label="Sekolah Asal" :options="$sekolah->pluck('nama_sekolah', 'id')->prepend('- Pilih Sekolah -', '')" />

                            <x-form.input-field name="kategori_id" type="select" label="Kategori Panahan"
                                :options="$kategori->pluck('nama_kategori', 'id')->prepend('- Pilih Kategori -', '')" />

                            <x-form.input-field name="kelas" label="Kelas / Semester" placeholder="contoh: XI IPA 2" />
                        </div>
                    </div>
                </div>

                {{-- Data Kontak --}}
                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body p-5 sm:p-6">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-4">
                            Kontak & Wali
                        </h3>

                        <div class="space-y-3">
                            <x-form.input-field name="no_telepon" label="No. Telepon / WA" placeholder="08xxxxxxxxxx" />

                            <x-form.input-field name="nama_orangtua" label="Nama Orang Tua / Wali" />

                            <x-form.input-field name="no_telepon_orangtua" label="No. Telepon Orang Tua"
                                placeholder="08xxxxxxxxxx" />

                            <x-form.input-field name="alamat" type="textarea" label="Alamat" placeholder="Alamat lengkap"
                                :rows="3" />
                        </div>
                    </div>
                </div>

                {{-- Data Medis --}}
                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body p-5 sm:p-6">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-4 flex items-center justify-between">
                            <span>Data Medis</span>
                            <span class="text-[10px] font-normal normal-case text-base-content/40">opsional</span>
                        </h3>

                        <div class="space-y-3">
                            <x-form.input-field name="golongan_darah" type="select" label="Golongan Darah"
                                :options="['' => '-', 'A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O']" />

                            <x-form.input-field name="riwayat_cedera" type="textarea" label="Riwayat Cedera"
                                placeholder="Kosongkan jika tidak ada" :rows="2" />

                            <x-form.input-field name="alergi" type="textarea" label="Alergi"
                                placeholder="Kosongkan jika tidak ada" :rows="2" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl mt-5">
            <div class="card-body p-4 sm:p-5">
                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5">
                    <a href="{{ route('admin.atlet.index') }}"
                        class="btn btn-ghost btn-sm rounded-xl w-full sm:w-auto inline-flex items-center justify-center">
                        Batal
                    </a>
                    <x-form.btn-primary type="submit" size="sm"
                        class="w-full sm:w-auto justify-center gap-1.5 rounded-xl">
                        <span>Simpan Atlet</span>
                    </x-form.btn-primary>
                </div>
            </div>
        </div>
    </form>
@endsection
