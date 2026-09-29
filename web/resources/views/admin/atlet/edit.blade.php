@extends('layouts.app')

@section('title', 'Edit Atlet')
@section('page-title', 'Edit Atlet')

@section('content')
    <x-display.page-header title="Edit Atlet: {{ $atlet->nama_lengkap }}" subtitle="Perbarui akun dan profil atlet">
        <x-slot:actions>
            <x-display.badge-status :status="$atlet->status" class="badge-lg" />
        </x-slot:actions>
    </x-display.page-header>

    <form method="POST" action="{{ route('admin.atlet.update', $atlet) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- KOLOM KIRI --}}
            <div class="space-y-5">
                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body p-5 sm:p-6">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-4">
                            Data Akun
                        </h3>

                        <div class="space-y-3">
                            <x-form.input-field name="username" label="Username" :value="$atlet->user->username" required
                                autocomplete="username" />

                            <x-form.input-field name="email" type="email" label="Email" hint="opsional"
                                :value="$atlet->user->email" autocomplete="email" />

                            <div class="divider text-xs text-base-content/40 my-1">Ganti Password (opsional)</div>

                            <x-form.input-field name="password" type="password" label="Password Baru"
                                hint="kosongkan bila tidak diubah" placeholder="Password baru" togglePassword
                                autocomplete="new-password" />
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body p-5 sm:p-6">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-4">
                            Data Pribadi
                        </h3>

                        <div class="space-y-3">
                            <x-form.input-field name="nia" label="Nomor Induk Atlet (NIA)" :value="$atlet->nia" required />

                            <x-form.input-field name="nama_lengkap" label="Nama Lengkap" :value="$atlet->nama_lengkap" required />

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <x-form.input-field name="jenis_kelamin" type="select" label="Jenis Kelamin"
                                    :value="$atlet->jenis_kelamin" :options="['' => '- Pilih -', 'L' => 'Laki-laki', 'P' => 'Perempuan']" />

                                <x-form.input-field name="tanggal_lahir" type="date" label="Tanggal Lahir"
                                    :value="$atlet->tanggal_lahir?->format('Y-m-d')" />
                            </div>

                            <x-form.input-field name="tempat_lahir" label="Tempat Lahir" :value="$atlet->tempat_lahir" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN --}}
            <div class="space-y-5">
                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body p-5 sm:p-6">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-4">
                            Data Olahraga
                        </h3>

                        <div class="space-y-3">
                            <x-form.input-field name="sekolah_id" type="select" label="Sekolah Asal" :value="$atlet->sekolah_id"
                                :options="$sekolah->pluck('nama_sekolah', 'id')->prepend('- Pilih Sekolah -', '')" />

                            <x-form.input-field name="kategori_id" type="select" label="Kategori Panahan"
                                :value="$atlet->kategori_id" :options="$kategori->pluck('nama_kategori', 'id')->prepend('- Pilih Kategori -', '')" />

                            <x-form.input-field name="kelas" label="Kelas / Semester" :value="$atlet->kelas" />

                            <x-form.input-field name="status" type="select" label="Status Atlet" :value="$atlet->status"
                                :options="['aktif' => 'Aktif', 'tidak_aktif' => 'Tidak Aktif']" />
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body p-5 sm:p-6">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-4">
                            Kontak & Wali
                        </h3>

                        <div class="space-y-3">
                            <x-form.input-field name="no_telepon" label="No. Telepon / WA" :value="$atlet->no_telepon" />

                            <x-form.input-field name="nama_orangtua" label="Nama Orang Tua / Wali" :value="$atlet->nama_orangtua" />

                            <x-form.input-field name="no_telepon_orangtua" label="No. Telepon Orang Tua"
                                :value="$atlet->no_telepon_orangtua" />

                            <x-form.input-field name="alamat" type="textarea" label="Alamat" :value="$atlet->alamat"
                                :rows="3" />
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body p-5 sm:p-6">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-2 border-b border-base-200 mb-4 flex items-center justify-between">
                            <span>Data Medis</span>
                            <span class="text-[10px] font-normal normal-case text-base-content/40">opsional</span>
                        </h3>

                        <div class="space-y-3">
                            <x-form.input-field name="golongan_darah" type="select" label="Golongan Darah"
                                :value="$atlet->golongan_darah" :options="['' => '-', 'A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O']" />

                            <x-form.input-field name="riwayat_cedera" type="textarea" label="Riwayat Cedera"
                                :value="$atlet->riwayat_cedera" :rows="2" />

                            <x-form.input-field name="alergi" type="textarea" label="Alergi" :value="$atlet->alergi"
                                :rows="2" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl mt-5">
            <div class="card-body p-4 sm:p-5">
                <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2.5">
                    <a href="{{ route('admin.atlet.show', $atlet) }}"
                        class="btn btn-ghost btn-sm rounded-xl w-full sm:w-auto inline-flex items-center justify-center gap-1.5">
                        <x-display.icon name="chevron-left" class="size-4 shrink-0" />
                        <span>Kembali</span>
                    </a>

                    <div class="flex flex-col-reverse sm:flex-row gap-2.5 w-full sm:w-auto">
                        <a href="{{ route('admin.atlet.index') }}"
                            class="btn btn-ghost btn-sm rounded-xl w-full sm:w-auto text-center">
                            Batal
                        </a>
                        <x-form.btn-primary type="submit" size="sm"
                            class="w-full sm:w-auto justify-center gap-1.5 rounded-xl">
                            <span>Simpan Perubahan</span>
                        </x-form.btn-primary>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
