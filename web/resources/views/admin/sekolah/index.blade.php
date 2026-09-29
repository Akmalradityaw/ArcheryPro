@extends('layouts.app')

@section('title', 'Sekolah')
@section('page-title', 'Sekolah')

@section('content')
    <x-display.page-header title="Sekolah" subtitle="Asal sekolah para atlet">
        <x-slot:actions>
            <x-form.btn-primary href="{{ route('admin.sekolah.create') }}" size="sm"
                class="gap-1.5 rounded-xl text-xs sm:text-sm">
                <x-display.icon name="plus" class="size-4 shrink-0" />
                <span class="hidden sm:inline">Tambah Sekolah</span>
                <span class="sm:hidden">Tambah</span>
            </x-form.btn-primary>
        </x-slot:actions>
    </x-display.page-header>

    <x-table.data-table :paginator="$sekolah" :cari="true" placeholder="Cari sekolah / kota...">
        <thead>
            <tr
                class="bg-base-200/60 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                <th class="py-2.5 px-2 sm:px-4 text-left">Nama Sekolah</th>
                <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-left">Kota</th>
                <th class="py-2.5 px-3 sm:px-4 text-left whitespace-nowrap">Atlet</th>
                <th class="py-2.5 px-2 sm:px-4 text-left whitespace-nowrap w-px">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
            @forelse ($sekolah as $s)
                <tr class="hover:bg-base-200/30 transition-colors">
                    <td class="py-2 px-2 sm:py-3 sm:px-4 min-w-0 align-middle">
                        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                            <div
                                class="size-7 sm:size-9 rounded-lg sm:rounded-xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-[11px] sm:text-xs uppercase shadow-xs">
                                {{ strtoupper(substr($s->nama_sekolah, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-xs sm:text-sm text-base-content truncate">
                                    {{ $s->nama_sekolah }}
                                </div>
                                <div class="text-[10px] sm:text-[11px] text-base-content/50 lg:hidden mt-0.5 truncate">
                                    {{ $s->kota ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 hidden lg:table-cell align-middle">
                        <span class="text-xs text-base-content/80">{{ $s->kota ?? '-' }}</span>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 align-middle">
                        @if ($s->atlet_count > 0)
                            <span class="badge badge-primary badge-sm font-semibold">{{ $s->atlet_count }}</span>
                        @else
                            <span class="text-base-content/40 text-xs">0</span>
                        @endif
                    </td>
                    <td class="py-2 px-2 sm:py-3 sm:px-4 text-left whitespace-nowrap w-px align-middle">
                        <div class="flex items-center justify-start gap-0.5 sm:gap-1">
                            <a href="{{ route('admin.sekolah.edit', $s) }}"
                                class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-warning hover:bg-warning/10 rounded-lg transition-colors"
                                title="Edit Sekolah">
                                <x-display.icon name="pencil" class="size-3.5 sm:size-4" />
                            </a>
                            <button type="button"
                                class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-error hover:bg-error/10 rounded-lg transition-colors"
                                title="Hapus Sekolah"
                                onclick="document.getElementById('del-sekolah-{{ $s->id }}').showModal()">
                                <x-display.icon name="trash" class="size-3.5 sm:size-4" />
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <x-table.table-empty colspan="4">Belum ada sekolah.</x-table.table-empty>
            @endforelse
        </tbody>
    </x-table.data-table>

    @push('modals')
        @foreach ($sekolah as $s)
            <x-feedback.modal-confirm id="del-sekolah-{{ $s->id }}" title="Hapus Sekolah?" :message="'Hapus sekolah ' .
                $s->nama_sekolah .
                '? Atlet yang terhubung akan kehilangan referensi sekolah.'"
                :action="route('admin.sekolah.destroy', $s)" method="DELETE" />
        @endforeach
    @endpush
@endsection
