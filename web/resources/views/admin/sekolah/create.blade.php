@extends('layouts.app')

@section('title', 'Tambah Sekolah')
@section('page-title', 'Tambah Sekolah')

@section('content')
    <x-display.page-header title="Tambah Sekolah" subtitle="Daftarkan asal sekolah baru" />

    <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
        <div class="card-body p-5 sm:p-6 lg:p-8">
            <form method="POST" action="{{ route('admin.sekolah.store') }}">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Informasi Sekolah
                        </h3>

                        <x-form.input-field name="nama_sekolah" label="Nama Sekolah" hint="unik"
                            placeholder="contoh: SMAN 1 Jakarta" required autofocus />

                        <x-form.input-field name="kota" label="Kota" placeholder="contoh: Jakarta Pusat" />
                    </div>

                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Alamat
                        </h3>

                        <x-form.input-field name="alamat" type="textarea" label="Alamat Lengkap" hint="opsional"
                            placeholder="Jl. Contoh No. 123, Kelurahan, Kecamatan" :rows="5" />
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5 mt-6 pt-4 border-t border-base-200">
                    <a href="{{ route('admin.sekolah.index') }}"
                        class="btn btn-ghost btn-sm rounded-xl w-full sm:w-auto text-center">
                        Batal
                    </a>
                    <x-form.btn-primary type="submit" size="sm"
                        class="w-full sm:w-auto justify-center gap-1.5 rounded-xl">
                        <span>Simpan Sekolah</span>
                    </x-form.btn-primary>
                </div>
            </form>
        </div>
    </div>
@endsection
