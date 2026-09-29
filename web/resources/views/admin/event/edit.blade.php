@extends('layouts.app')

@section('title', 'Edit Event')
@section('page-title', 'Edit Event')

@section('content')
    <x-display.page-header title="Edit Event: {{ $event->nama_event }}" subtitle="Perbarui detail event">
        <x-slot:actions>
            <x-display.badge-status :status="$event->status" class="badge-lg" />
        </x-slot:actions>
    </x-display.page-header>

    <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
        <div class="card-body p-5 sm:p-6 lg:p-8">
            <form method="POST" action="{{ route('admin.event.update', $event) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Informasi Event
                        </h3>

                        <x-form.input-field name="nama_event" label="Nama Event" :value="$event->nama_event" required autofocus />

                        <x-form.input-field name="tanggal" type="date" label="Tanggal Pelaksanaan" :value="$event->tanggal->format('Y-m-d')"
                            required />

                        <x-form.input-field name="lokasi" label="Lokasi" :value="$event->lokasi" />
                    </div>

                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Status & Visibilitas
                        </h3>

                        <x-form.input-field name="status" type="select" label="Status Event" :value="$event->status"
                            :options="[
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
                                <input type="checkbox" name="is_publik" value="1" @checked(old('is_publik', $event->is_publik))
                                    class="checkbox checkbox-primary checkbox-sm shrink-0 mt-0.5">
                                <span class="text-sm">
                                    <span class="block font-medium">Publikasikan hasil event</span>
                                    <span class="block text-xs text-base-content/60 mt-0.5">
                                        Event akan muncul di halaman publik setelah diselesaikan.
                                    </span>
                                </span>
                            </label>
                        </div>

                        <x-feedback.alert type="neutral" icon="clock" class="mt-3">
                            <div class="text-xs">
                                <p>Dibuat: {{ $event->created_at->format('d F Y, H:i') }}</p>
                                <p class="text-base-content/60">
                                    Terakhir diubah: {{ $event->updated_at?->diffForHumans() ?? '-' }}
                                </p>
                            </div>
                        </x-feedback.alert>
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
                        <span>Simpan Perubahan</span>
                    </x-form.btn-primary>
                </div>
            </form>
        </div>
    </div>
@endsection
