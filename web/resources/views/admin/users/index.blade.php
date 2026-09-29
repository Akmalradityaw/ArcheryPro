@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'Users')

@section('content')
    <x-display.page-header title="Users" subtitle="Kelola akun seluruh peran">
        <x-slot:actions>
            <x-form.btn-primary href="{{ route('admin.users.create') }}" size="sm"
                class="gap-1.5 rounded-xl text-xs sm:text-sm">
                <x-display.icon name="plus" class="size-4 shrink-0" />
                <span class="hidden sm:inline">Tambah User</span>
                <span class="sm:hidden">Tambah</span>
            </x-form.btn-primary>
        </x-slot:actions>
    </x-display.page-header>

    <x-table.data-table :paginator="$users" :cari="true" placeholder="Cari username / email...">
        <thead>
            <tr
                class="bg-base-200/60 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                <th class="py-2.5 px-2 sm:px-4 text-left">Username</th>
                <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-left">Email</th>
                <th class="py-2.5 px-3 sm:px-4 text-left whitespace-nowrap">Role</th>
                <th class="py-2.5 px-2 sm:px-4 text-left whitespace-nowrap w-px">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
            @forelse ($users as $user)
                <tr class="hover:bg-base-200/30 transition-colors">
                    <td class="py-2 px-2 sm:py-3 sm:px-4 min-w-0 align-middle">
                        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                            <div
                                class="size-7 sm:size-9 rounded-lg sm:rounded-xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-[11px] sm:text-xs uppercase shadow-xs">
                                {{ strtoupper(substr($user->username, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-xs sm:text-sm text-base-content truncate">
                                    {{ $user->username }}
                                </div>
                                <div class="text-[10px] sm:text-[11px] text-base-content/50 lg:hidden mt-0.5 truncate">
                                    {{ $user->email ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 hidden lg:table-cell align-middle">
                        <span class="text-xs text-base-content/80 truncate block max-w-[200px] xl:max-w-[280px]">
                            {{ $user->email ?? '-' }}
                        </span>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 whitespace-nowrap align-middle">
                        <x-display.badge-status :status="$user->role" />
                    </td>
                    <td class="py-2 px-2 sm:py-3 sm:px-4 text-left whitespace-nowrap w-px align-middle">
                        <div class="flex items-center justify-start gap-0.5 sm:gap-1">
                            <a href="{{ route('admin.users.show', $user) }}"
                                class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                title="Lihat Detail">
                                <x-display.icon name="eye" class="size-3.5 sm:size-4" />
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}"
                                class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-warning hover:bg-warning/10 rounded-lg transition-colors"
                                title="Edit User">
                                <x-display.icon name="pencil" class="size-3.5 sm:size-4" />
                            </a>
                            <button type="button"
                                class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-error hover:bg-error/10 rounded-lg transition-colors"
                                title="Hapus User"
                                onclick="document.getElementById('del-user-{{ $user->id }}').showModal()">
                                <x-display.icon name="trash" class="size-3.5 sm:size-4" />
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <x-table.table-empty colspan="4">Belum ada user.</x-table.table-empty>
            @endforelse
        </tbody>
    </x-table.data-table>

    @push('modals')
        @foreach ($users as $user)
            <x-feedback.modal-confirm id="del-user-{{ $user->id }}" title="Hapus User" :message="'Hapus user ' . $user->username . '? Tindakan ini tidak dapat dibatalkan.'" :action="route('admin.users.destroy', $user)"
                method="DELETE" />
        @endforeach
    @endpush
@endsection
