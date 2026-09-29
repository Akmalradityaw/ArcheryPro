@extends('layouts.app')

@section('title', 'Edit Atlet')
@section('page-title', 'Edit Atlet')

@section('content')
    <div class="mb-4">
        <a href="{{ route('pelatih.atlet.index') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-base-content/70 hover:text-primary transition-colors">
            <x-display.icon name="arrow-left" class="size-4 shrink-0" />
            <span>Kembali ke Daftar Atlet</span>
        </a>
    </div>

    <x-display.page-header title="Edit Atlet" subtitle="Perbarui data profil untuk {{ $atlet->nama_lengkap }}" />

    <div class="card bg-base-100 border border-base-200 shadow-xs rounded-2xl max-w-3xl">
        <div class="card-body p-6 sm:p-8">
            <form method="POST" action="{{ route('pelatih.atlet.update', $atlet) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-base-content/50 mb-3">Informasi Atlet</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form.input-field name="nia" label="NIA" :value="$atlet->nia" required autofocus />

                        <x-form.input-field name="nama_lengkap" label="Nama Lengkap" :value="$atlet->nama_lengkap" required />

                        <x-form.input-field name="sekolah_id" type="select" label="Sekolah" :value="$atlet->sekolah_id"
                            :options="['' => 'Pilih Sekolah'] + $sekolah->pluck('nama_sekolah', 'id')->toArray()" />

                        <x-form.input-field name="kategori_id" type="select" label="Kategori" :value="$atlet->kategori_id"
                            :options="['' => 'Pilih Kategori'] + $kategori->pluck('nama_kategori', 'id')->toArray()" />

                        <x-form.input-field name="status" type="select" label="Status Keaktifan" :value="$atlet->status"
                            required :options="['aktif' => 'Aktif', 'tidak_aktif' => 'Tidak Aktif']" />
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-2 pt-4 border-t border-base-200">
                    <a href="{{ route('pelatih.atlet.index') }}" class="btn btn-ghost btn-sm rounded-xl">Batal</a>
                    <x-form.btn-primary type="submit" size="sm" class="gap-1.5 rounded-xl">
                        <span>Perbarui Data</span>
                    </x-form.btn-primary>
                </div>
            </form>
        </div>
    </div>
@endsection
