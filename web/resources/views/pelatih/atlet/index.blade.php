@extends('layouts.app')

@section('title', 'Atlet')
@section('page-title', 'Atlet')

@section('content')
    <x-display.page-header title="Daftar Atlet" subtitle="Kelola dan pantau seluruh data atlet yang kamu bina">
        <x-slot:actions>
            <x-form.btn-primary href="{{ route('pelatih.atlet.create') }}" size="sm"
                class="gap-1.5 rounded-xl text-xs sm:text-sm">
                <x-display.icon name="plus" class="size-4 shrink-0" />
                <span class="hidden sm:inline">Tambah Atlet</span>
                <span class="sm:hidden">Tambah</span>
            </x-form.btn-primary>
        </x-slot:actions>
    </x-display.page-header>

    <x-table.data-table :paginator="$atlets" :cari="true" placeholder="Cari nama atau NIA atlet...">
        <thead>
            <tr
                class="bg-base-200/60 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                <th class="py-2.5 px-2 sm:px-4 text-left">Atlet</th>
                {{-- NIA & Sekolah & Kategori hanya muncul di desktop (lg) --}}
                <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-left whitespace-nowrap">NIA</th>
                <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-left">Sekolah</th>
                <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-left">Kategori</th>
                <th class="py-2.5 px-2 sm:px-4 text-left whitespace-nowrap">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
            @forelse ($atlets as $atlet)
                <tr class="hover:bg-base-200/30 transition-colors">
                    {{-- Nama & Avatar Atlet — NIA + Sekolah + Kategori tampil di bawah nama untuk mobile & tablet --}}
                    <td class="py-2 px-2 sm:py-3 sm:px-4 min-w-0 align-middle">
                        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                            <div
                                class="size-7 sm:size-9 rounded-lg sm:rounded-xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-[11px] sm:text-xs uppercase shadow-xs">
                                {{ strtoupper(substr($atlet->nama_lengkap, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-xs sm:text-sm text-base-content truncate">
                                    {{ $atlet->nama_lengkap }}
                                </div>
                                {{-- Sub-teks: tampil sampai lg, hilang di desktop --}}
                                <div class="text-[10px] sm:text-[11px] text-base-content/50 lg:hidden mt-0.5 truncate">
                                    <span class="font-mono">NIA: {{ $atlet->nia }}</span>
                                    @if ($atlet->sekolah)
                                        <span class="mx-0.5">•</span>
                                        <span>{{ $atlet->sekolah->nama_sekolah }}</span>
                                    @endif
                                    @if ($atlet->kategori)
                                        <span class="mx-0.5">•</span>
                                        <span>{{ $atlet->kategori->nama_kategori }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- NIA (Desktop saja) --}}
                    <td class="py-2.5 px-3 sm:px-4 whitespace-nowrap hidden lg:table-cell align-middle">
                        <span
                            class="font-mono text-xs text-base-content/70 bg-base-200/60 px-2 py-1 rounded-md border border-base-200">
                            {{ $atlet->nia }}
                        </span>
                    </td>

                    {{-- Sekolah (Desktop saja) --}}
                    <td class="py-2.5 px-3 sm:px-4 hidden lg:table-cell align-middle">
                        <span class="text-xs text-base-content/80 truncate block max-w-[180px] xl:max-w-[250px]">
                            {{ $atlet->sekolah->nama_sekolah ?? '-' }}
                        </span>
                    </td>

                    {{-- Kategori (Desktop saja) --}}
                    <td class="py-2.5 px-3 sm:px-4 hidden lg:table-cell align-middle">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-base-200 text-base-content/70 border border-base-300/50">
                            {{ $atlet->kategori->nama_kategori ?? '-' }}
                        </span>
                    </td>

                    {{-- AKSI: text-left, wrapper flex justify-start, sejajar dengan kolom lain --}}
                    <td class="py-2 px-2 sm:py-3 sm:px-4 text-left whitespace-nowrap align-middle">
                        <div class="flex items-center justify-start gap-0.5 sm:gap-1">
                            {{-- Lihat Detail --}}
                            <a href="{{ route('pelatih.atlet.show', $atlet) }}"
                                class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                title="Lihat Detail">
                                <x-display.icon name="eye" class="size-3.5 sm:size-4" />
                            </a>

                            {{-- Edit --}}
                            <a href="{{ route('pelatih.atlet.edit', $atlet) }}"
                                class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-warning hover:bg-warning/10 rounded-lg transition-colors"
                                title="Edit Data">
                                <x-display.icon name="pencil" class="size-3.5 sm:size-4" />
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <x-table.table-empty colspan="5">Belum ada data atlet yang terdaftar.</x-table.table-empty>
            @endforelse
        </tbody>
    </x-table.data-table>
@endsection
