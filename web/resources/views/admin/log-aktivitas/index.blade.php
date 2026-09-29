@extends('layouts.app')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')

@section('content')
    <x-display.page-header title="Log Aktivitas" subtitle="Jejak semua request mutasi (create, update, delete) pengguna">
        <x-slot:actions>
            <x-form.btn-primary href="{{ route('admin.log-aktivitas.export', ['q' => $q]) }}" size="sm"
                class="gap-1.5 rounded-xl">
                <span>Ekspor CSV</span>
            </x-form.btn-primary>
        </x-slot:actions>
    </x-display.page-header>

    {{-- Filter --}}
    <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl mb-5">
        <div class="card-body p-4 sm:p-5">
            <form method="GET" action="{{ route('admin.log-aktivitas.index') }}" class="flex flex-wrap items-end gap-3">
                <div class="form-control flex-1 min-w-48">
                    <label class="label pb-1">
                        <span class="label-text text-xs font-medium">Cari</span>
                    </label>
                    <label class="input input-bordered input-sm flex items-center gap-2 rounded-xl">
                        <x-display.icon name="search" class="size-4 text-base-content/40" />
                        <input type="text" name="q" value="{{ $q }}"
                            placeholder="Cari aktivitas / user..."
                            class="grow bg-transparent border-0 outline-none focus:ring-0 text-sm">
                    </label>
                </div>

                <div class="form-control">
                    <label class="label pb-1">
                        <span class="label-text text-xs font-medium">Dari</span>
                    </label>
                    <input type="date" name="dari" value="{{ request('dari') }}"
                        class="input input-bordered input-sm rounded-xl">
                </div>

                <div class="form-control">
                    <label class="label pb-1">
                        <span class="label-text text-xs font-medium">Sampai</span>
                    </label>
                    <input type="date" name="sampai" value="{{ request('sampai') }}"
                        class="input input-bordered input-sm rounded-xl">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm gap-1 rounded-xl">
                        <x-display.icon name="search" class="size-4" />
                        Filter
                    </button>
                    @if (request()->anyFilled(['q', 'dari', 'sampai']))
                        <a href="{{ route('admin.log-aktivitas.index') }}" class="btn btn-ghost btn-sm rounded-xl">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Log --}}
    <x-table.data-table :paginator="$logs" :cari="false">
        <thead>
            <tr
                class="bg-base-200/60 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                <th class="py-2.5 px-2 sm:px-4 text-left whitespace-nowrap">Waktu</th>
                <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-left">User</th>
                <th class="py-2.5 px-3 sm:px-4 text-left">Aktivitas</th>
                <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-left whitespace-nowrap">IP</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
            @forelse ($logs as $log)
                <tr class="hover:bg-base-200/30 transition-colors">
                    <td class="py-2.5 px-2 sm:px-4 whitespace-nowrap align-middle">
                        <span
                            class="font-mono text-xs text-base-content/70 bg-base-200/60 px-2 py-1 rounded-md border border-base-200">
                            {{ $log->waktu?->format('d/m/Y H:i') ?? '-' }}
                        </span>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 hidden lg:table-cell align-middle">
                        @if ($log->user)
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="size-7 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-[10px] uppercase">
                                    {{ strtoupper(substr($log->user->username, 0, 1)) }}
                                </div>
                                <span class="text-xs font-semibold">{{ $log->user->username }}</span>
                            </div>
                        @else
                            <span class="text-xs text-base-content/50">-</span>
                        @endif
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 align-middle">
                        <div class="font-mono text-xs text-base-content/80 break-all">{{ $log->aktivitas }}</div>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 hidden lg:table-cell align-middle">
                        @if ($log->ip_address)
                            <span class="badge badge-ghost badge-sm font-mono">{{ $log->ip_address }}</span>
                        @else
                            <span class="text-xs text-base-content/40">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <x-table.table-empty colspan="4">Belum ada log aktivitas.</x-table.table-empty>
            @endforelse
        </tbody>
    </x-table.data-table>
@endsection
