@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
    <x-display.page-header title="Edit User: {{ $user->username }}" subtitle="Perbarui data akun dan peran" />

    <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
        <div class="card-body p-5 sm:p-6 lg:p-8">
            <x-feedback.alert type="neutral" icon="info-circle" class="mb-6">
                <div class="text-xs">
                    <p class="font-semibold">Dibuat pada {{ $user->created_at->format('d F Y, H:i') }}</p>
                    <p class="text-base-content/60">
                        Terakhir diubah: {{ $user->updated_at?->diffForHumans() ?? '-' }}
                    </p>
                </div>
            </x-feedback.alert>

            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Data Akun
                        </h3>

                        <x-form.input-field name="username" label="Username" :value="$user->username" required
                            autocomplete="username" />

                        <x-form.input-field name="email" type="email" label="Email" hint="opsional" :value="$user->email"
                            autocomplete="email" />

                        <x-form.input-field name="role" type="select" label="Role" :value="$user->role" required
                            :options="[
                                'admin' => 'Admin — akses penuh',
                                'pelatih' => 'Pelatih — kelola atlet & analisis',
                                'scoring' => 'Scoring — input skor',
                                'atlet' => 'Atlet — lihat skor pribadi',
                            ]" />
                    </div>

                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold uppercase tracking-wide text-base-content/50 pb-1 border-b border-base-200">
                            Ganti Password
                            <span class="ml-1 text-[10px] font-normal normal-case text-base-content/40">
                                (opsional)
                            </span>
                        </h3>

                        <x-form.input-field name="password" type="password" label="Password Baru"
                            hint="kosongkan bila tidak diubah" placeholder="Password baru" togglePassword
                            autocomplete="new-password" />

                        <x-form.input-field name="password_confirmation" type="password" label="Konfirmasi Password"
                            placeholder="Ulangi password baru" togglePassword autocomplete="new-password" />

                        <x-feedback.alert type="neutral" icon="info-circle" class="mt-2">
                            <span class="text-xs text-base-content/60">
                                Biarkan kosong jika tidak ingin mengganti password.
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
                        <span>Simpan Perubahan</span>
                    </x-form.btn-primary>
                </div>
            </form>
        </div>
    </div>
@endsection
