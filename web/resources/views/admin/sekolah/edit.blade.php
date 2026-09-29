@extends('layouts.app')

@section('title', 'Edit Sekolah')
@section('page-title', 'Edit Sekolah')

@section('content')
    <x-display.page-header title="Edit Sekolah: {{ $sekolah->nama_sekolah }}" subtitle="Perbarui data sekolah" />

    <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
        <div class="card-body p-5 sm:p-6 lg:p-8">
            <x-feedback.alert type="neutral" icon="clock" class="mb-6">
                <div class="text-xs">
                    <p class="font-semibold">Dibuat pada {{ $sekolah->created_at->format('d F Y, H:i') }}</p>
                    <p class="text-base-content/60">
                        Terakhir diubah: {{ $sekolah->updated_at?->diffForHumans() ?? '-' }}
                    </p>
                </div>
            </x-feedback.alert>

            <form method="POST" action="{{ route('admin.sekolah.update', $sekolah) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Informasi Sekolah
                        </h3>

                        <x-form.input-field name="nama_sekolah" label="Nama Sekolah" :value="$sekolah->nama_sekolah" required autofocus />

                        <x-form.input-field name="kota" label="Kota" :value="$sekolah->kota" />
                    </div>

                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Alamat
                        </h3>

                        <x-form.input-field name="alamat" type="textarea" label="Alamat Lengkap" hint="opsional"
                            :value="$sekolah->alamat" :rows="5" />
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
                        <span>Simpan Perubahan</span>
                    </x-form.btn-primary>
                </div>
            </form>
        </div>
    </div>
@endsection
