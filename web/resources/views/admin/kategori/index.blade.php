@extends('layouts.app')

@section('title', 'Kategori')
@section('page-title', 'Kategori')

@section('content')
    <x-display.page-header title="Kategori" subtitle="Divisi busur beserta jarak dan jumlah panah">
        <x-slot:actions>
            <x-form.btn-primary href="{{ route('admin.kategori.create') }}" size="sm"
                class="gap-1.5 rounded-xl text-xs sm:text-sm">
                <x-display.icon name="plus" class="size-4 shrink-0" />
                <span class="hidden sm:inline">Tambah Kategori</span>
                <span class="sm:hidden">Tambah</span>
            </x-form.btn-primary>
        </x-slot:actions>
    </x-display.page-header>

    <x-table.data-table :paginator="$kategori" :cari="true" placeholder="Cari kategori...">
        <thead>
            <tr
                class="bg-base-200/60 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                <th class="py-2.5 px-2 sm:px-4 text-left">Nama</th>
                <th class="py-2.5 px-3 sm:px-4 text-left whitespace-nowrap">Jarak</th>
                <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-left whitespace-nowrap">Panah/End</th>
                <th class="py-2.5 px-3 sm:px-4 text-left whitespace-nowrap">Atlet</th>
                <th class="py-2.5 px-2 sm:px-4 text-left whitespace-nowrap w-px">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
            @forelse ($kategori as $k)
                <tr class="hover:bg-base-200/30 transition-colors">
                    <td class="py-2 px-2 sm:py-3 sm:px-4 align-middle">
                        <div class="font-semibold text-xs sm:text-sm text-base-content">{{ $k->nama_kategori }}</div>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 whitespace-nowrap align-middle">
                        <span
                            class="font-mono text-xs text-base-content/70 bg-base-200/60 px-2 py-1 rounded-md border border-base-200">
                            {{ $k->jarak_tempuh }} m
                        </span>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 hidden lg:table-cell align-middle">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-base-200 text-base-content/70 border border-base-300/50">
                            {{ $k->jumlah_panah_per_end }} panah
                        </span>
                    </td>
                    <td class="py-2.5 px-3 sm:px-4 align-middle">
                        @if ($k->atlet_count > 0)
                            <span class="badge badge-primary badge-sm font-semibold">{{ $k->atlet_count }}</span>
                        @else
                            <span class="text-base-content/40 text-xs">0</span>
                        @endif
                    </td>
                    <td class="py-2 px-2 sm:py-3 sm:px-4 text-left whitespace-nowrap w-px align-middle">
                        <div class="flex items-center justify-start gap-0.5 sm:gap-1">
                            <a href="{{ route('admin.kategori.edit', $k) }}"
                                class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-warning hover:bg-warning/10 rounded-lg transition-colors"
                                title="Edit Kategori">
                                <x-display.icon name="pencil" class="size-3.5 sm:size-4" />
                            </a>
                            <button type="button"
                                class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-error hover:bg-error/10 rounded-lg transition-colors"
                                title="Hapus Kategori"
                                onclick="document.getElementById('del-kategori-{{ $k->id }}').showModal()">
                                <x-display.icon name="trash" class="size-3.5 sm:size-4" />
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <x-table.table-empty colspan="5">Belum ada kategori.</x-table.table-empty>
            @endforelse
        </tbody>
    </x-table.data-table>

    @push('modals')
        @foreach ($kategori as $k)
            <x-feedback.modal-confirm id="del-kategori-{{ $k->id }}" title="Hapus Kategori?" :message="'Hapus kategori ' .
                $k->nama_kategori .
                '? Atlet yang terhubung akan kehilangan referensi kategori.'"
                :action="route('admin.kategori.destroy', $k)" method="DELETE" />
        @endforeach
    @endpush
@endsection
