@extends('layouts.app')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')

@section('content')
    <x-display.page-header title="Tambah Kategori" subtitle="Daftarkan divisi busur baru" />

    <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
        <div class="card-body p-5 sm:p-6 lg:p-8">
            <form method="POST" action="{{ route('admin.kategori.store') }}">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Informasi Kategori
                        </h3>

                        <x-form.input-field name="nama_kategori" label="Nama Kategori"
                            hint="contoh: Recurve, Compound, Traditional" placeholder="contoh: Recurve" required
                            autofocus />

                        <x-form.input-field name="jarak_tempuh" type="number" label="Jarak Tempuh" hint="dalam meter"
                            placeholder="20" required min="1" />
                    </div>

                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Format Pencatatan
                        </h3>

                        <x-form.input-field name="jumlah_panah_per_end" type="number" label="Jumlah Panah per End"
                            hint="default 6" :value="6" required min="1" max="12" />

                        <x-feedback.alert type="neutral" icon="info-circle" class="mt-2">
                            <span class="text-xs text-base-content/60">
                                Kategori menentukan jarak tembak dan jumlah panah per end.
                                Format standar World Archery adalah 6 panah per end.
                            </span>
                        </x-feedback.alert>
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5 mt-6 pt-4 border-t border-base-200">
                    <a href="{{ route('admin.kategori.index') }}"
                        class="btn btn-ghost btn-sm rounded-xl w-full sm:w-auto text-center">
                        Batal
                    </a>
                    <x-form.btn-primary type="submit" size="sm"
                        class="w-full sm:w-auto justify-center gap-1.5 rounded-xl">
                        <span>Simpan Kategori</span>
                    </x-form.btn-primary>
                </div>
            </form>
        </div>
    </div>
@endsection
