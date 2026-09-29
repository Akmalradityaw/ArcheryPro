@extends('layouts.app')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User')

@section('content')
    <x-display.page-header title="Tambah User" subtitle="Buat akun baru beserta perannya" />

    <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
        <div class="card-body p-5 sm:p-6 lg:p-8">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Data Akun
                        </h3>

                        <x-form.input-field name="username" label="Username" hint="min. 3 karakter"
                            placeholder="contoh: admin_baru" required autofocus autocomplete="username" />

                        <x-form.input-field name="email" type="email" label="Email" hint="opsional"
                            placeholder="nama@email.com" autocomplete="email" />

                        <x-form.input-field name="role" type="select" label="Role" required :options="[
                            'admin' => 'Admin — akses penuh',
                            'pelatih' => 'Pelatih — kelola atlet & analisis',
                            'scoring' => 'Scoring — input skor',
                            'atlet' => 'Atlet — lihat skor pribadi',
                        ]" />
                    </div>

                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Keamanan
                        </h3>

                        <x-form.input-field name="password" type="password" label="Password" hint="min. 8 karakter"
                            placeholder="Buat password" required togglePassword autocomplete="new-password" />

                        <x-form.input-field name="password_confirmation" type="password" label="Konfirmasi Password"
                            placeholder="Ulangi password" required togglePassword autocomplete="new-password" />

                        <x-feedback.alert type="neutral" icon="info-circle" class="mt-2">
                            <span class="text-xs text-base-content/60">
                                Password minimal 8 karakter. User dapat menggantinya setelah login.
                            </span>
                        </x-feedback.alert>
                    </div>
                </div>

                <div
                    class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5 mt-6 pt-4 border-t border-base-200">
                    <a href="{{ route('admin.users.index') }}"
                        class="btn btn-ghost btn-sm rounded-xl w-full sm:w-auto text-center">
                        Batal
                    </a>
                    <x-form.btn-primary type="submit" size="sm"
                        class="w-full sm:w-auto justify-center gap-1.5 rounded-xl">
                        <span>Simpan User</span>
                    </x-form.btn-primary>
                </div>
            </form>
        </div>
    </div>
@endsection
