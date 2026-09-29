@extends('layouts.app')

@section('title', 'Detail User')
@section('page-title', 'Detail User')

@section('content')
    <x-display.page-header title="Detail User" subtitle="Informasi akun dan aksi cepat">
        <x-slot:actions>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm gap-1">
                <x-display.icon name="chevron-left" class="size-4" />
                Kembali
            </a>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm">Edit</a>
        </x-slot:actions>
    </x-display.page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Profil --}}
        <div class="card bg-base-100 border border-base-200 shadow-xs rounded-2xl lg:col-span-2">
            <div class="card-body p-5 sm:p-6">
                <div class="flex items-center gap-4 mb-4 pb-4 border-b border-base-200">
                    <div
                        class="size-14 rounded-2xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center font-bold text-xl uppercase shadow-xs shrink-0">
                        {{ strtoupper(substr($user->username, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-base-content tracking-tight">{{ $user->username }}</h2>
                        <div class="mt-1">
                            <x-display.badge-status :status="$user->role" />
                        </div>
                    </div>
                </div>

                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-base-content/60">Email</dt>
                        <dd class="font-medium text-right">{{ $user->email ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-base-content/60">Role</dt>
                        <dd class="font-medium text-right capitalize">{{ $user->role }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-base-content/60">Dibuat</dt>
                        <dd class="font-medium text-right">{{ $user->created_at->format('d F Y, H:i') }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-base-content/60">Terakhir diubah</dt>
                        <dd class="font-medium text-right">{{ $user->updated_at?->diffForHumans() ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Aksi Cepat --}}
        <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
            <div class="card-body p-5 sm:p-6">
                <h3 class="text-sm font-bold text-base-content tracking-tight mb-3">Aksi Cepat</h3>

                <div class="space-y-2">
                    <button type="button"
                        onclick="document.getElementById('modal-reset-pass-{{ $user->id }}').showModal()"
                        class="btn btn-warning btn-sm w-full justify-start gap-2 rounded-xl">
                        <x-display.icon name="key" class="size-4" />
                        Reset Password
                    </button>

                    @if (in_array($user->role, ['pelatih', 'scoring']))
                        <button type="button"
                            onclick="document.getElementById('modal-reset-token-{{ $user->id }}').showModal()"
                            class="btn btn-info btn-sm w-full justify-start gap-2 rounded-xl">
                            <x-display.icon name="lock" class="size-4" />
                            Reset Token
                        </button>
                    @endif

                    <button type="button" onclick="document.getElementById('modal-hapus-{{ $user->id }}').showModal()"
                        class="btn btn-error btn-sm w-full justify-start gap-2 rounded-xl">
                        <x-display.icon name="trash" class="size-4" />
                        Hapus User
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('modals')
        <x-feedback.modal-confirm id="modal-reset-pass-{{ $user->id }}" title="Reset Password?" :message="'Password untuk ' .
            $user->username .
            ' akan direset ke default. User harus mengganti password saat login berikutnya.'"
            :action="route('admin.users.reset-password', $user)" method="POST" variant="warning" confirmText="Ya, Reset" />

        @if (in_array($user->role, ['pelatih', 'scoring']))
            <x-feedback.modal-confirm id="modal-reset-token-{{ $user->id }}" title="Reset Token?" :message="'Token registrasi untuk ' . $user->username . ' akan di-reset. Token lama tidak berlaku lagi.'"
                :action="route('admin.users.reset-token', $user)" method="POST" variant="primary" confirmText="Ya, Reset" />
        @endif

        <x-feedback.modal-confirm id="modal-hapus-{{ $user->id }}" title="Hapus User?" :message="'User ' . $user->username . ' beserta seluruh data terkait akan dihapus permanen.'" :action="route('admin.users.destroy', $user)"
            method="DELETE" variant="error" confirmText="Ya, Hapus" />
    @endpush
@endsection
