@extends('layouts.app')

@section('title', 'Tambah Event')
@section('page-title', 'Tambah Event')

@section('content')
    <x-display.page-header title="Tambah Event" subtitle="Buat event pertandingan baru" />

    <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
        <div class="card-body p-5 sm:p-6 lg:p-8">
            <form method="POST" action="{{ route('admin.event.store') }}">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Informasi Event
                        </h3>

                        <x-form.input-field name="nama_event" label="Nama Event"
                            placeholder="contoh: Kejuaraan Panahan Antar Sekolah 2025" required autofocus />

                        <x-form.input-field name="tanggal" type="date" label="Tanggal Pelaksanaan" required />

                        <x-form.input-field name="lokasi" label="Lokasi" placeholder="contoh: Lapangan Utama" />
                    </div>

                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Status & Visibilitas
                        </h3>

                        <x-form.input-field name="status" type="select" label="Status Event" :options="[
                            'draft' => 'Draft — belum dimulai',
                            'berlangsung' => 'Berlangsung — sedang jalan',
                            'selesai' => 'Selesai — sudah final',
                        ]" />

                        <div class="form-control">
                            <label class="label pb-1 cursor-pointer">
                                <span class="label-text font-medium">Tampilkan ke Publik</span>
                            </label>
                            <label
                                class="flex items-start gap-3 p-3 border border-base-300 rounded-lg cursor-pointer hover:border-primary/50 hover:bg-primary/5 transition has-[:checked]:border-primary has-[:checked]:bg-primary/5 has-[:checked]:ring-1 has-[:checked]:ring-primary/20">
                                <input type="checkbox" name="is_publik" value="1" @checked(old('is_publik'))
                                    class="checkbox checkbox-primary checkbox-sm shrink-0 mt-0.5">
                                <span class="text-sm">
                                    <span class="block font-medium">Publikasikan hasil event</span>
                                    <span class="block text-xs text-base-content/60 mt-0.5">
                                        Event akan muncul di halaman publik setelah diselesaikan.
                                    </span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5 mt-6 pt-4 border-t border-base-200">
                    <a href="{{ route('admin.event.index') }}"
                        class="btn btn-ghost btn-sm rounded-xl w-full sm:w-auto text-center">
                        Batal
                    </a>
                    <x-form.btn-primary type="submit" size="sm"
                        class="w-full sm:w-auto justify-center gap-1.5 rounded-xl">
                        <span>Simpan Event</span>
                    </x-form.btn-primary>
                </div>
            </form>
        </div>
    </div>
@endsection
