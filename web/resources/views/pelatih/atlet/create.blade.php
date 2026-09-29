@extends('layouts.app')

@section('title', 'Tambah Atlet')
@section('page-title', 'Tambah Atlet')

@section('content')
    <div class="mb-4">
        <a href="{{ route('pelatih.atlet.index') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-base-content/70 hover:text-primary transition-colors">
            <x-display.icon name="arrow-left" class="size-4 shrink-0" />
            <span>Kembali ke Daftar Atlet</span>
        </a>
    </div>

    <x-display.page-header title="Tambah Atlet Baru"
        subtitle="Buat akun user sekaligus profil data atlet dalam satu langkah" />

    <div class="card bg-base-100 border border-base-200 shadow-xs rounded-2xl max-w-3xl">
        <div class="card-body p-6 sm:p-8">
            <form method="POST" action="{{ route('pelatih.atlet.store') }}" class="space-y-6">
                @csrf

                {{-- Section: Info Akun --}}
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-base-content/50 mb-3">Informasi Akun</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form.input-field name="username" label="Username" hint="min. 3 karakter"
                            placeholder="contoh: atlet_baru" required autofocus autocomplete="username" />

                        <x-form.input-field name="password" type="password" label="Password" hint="min. 8 karakter"
                            placeholder="Buat password" required togglePassword autocomplete="new-password" />
                    </div>
                </div>

                <div class="border-t border-base-200"></div>

                {{-- Section: Data Diri & Instansi --}}
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-base-content/50 mb-3">Data Diri & Instansi
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form.input-field name="nia" label="NIA (Nomor Induk Atlet)" placeholder="Masukkan NIA"
                            required />

                        <x-form.input-field name="nama_lengkap" label="Nama Lengkap" placeholder="Nama lengkap atlet"
                            required />

                        <x-form.input-field name="sekolah_id" type="select" label="Sekolah" :options="['' => 'Pilih Sekolah'] + $sekolah->pluck('nama_sekolah', 'id')->toArray()" />

                        <x-form.input-field name="kategori_id" type="select" label="Kategori" :options="['' => 'Pilih Kategori'] + $kategori->pluck('nama_kategori', 'id')->toArray()" />
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div
                    class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5 pt-5 border-t border-base-200">
                    <a href="{{ route('pelatih.atlet.index') }}"
                        class="btn btn-ghost btn-sm rounded-xl w-full sm:w-auto text-center">
                        Batal
                    </a>
                    <x-form.btn-primary type="submit" size="sm"
                        class="w-full sm:w-auto inline-flex items-center justify-center flex-nowrap whitespace-nowrap gap-1.5 rounded-xl">
                        <span>Simpan Data Atlet</span>
                    </x-form.btn-primary>
                </div>
            </form>
        </div>
    </div>
@endsection
