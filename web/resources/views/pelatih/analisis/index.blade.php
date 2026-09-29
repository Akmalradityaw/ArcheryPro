@extends('layouts.app')

@section('title', 'Analisis Performa')
@section('page-title', 'Analisis Performa')

@section('content')
    <x-display.page-header title="Analisis Performa" subtitle="Pilih atlet untuk melihat tren skornya" />

    <x-table.data-table :paginator="$atlets" :cari="true" placeholder="Cari atlet...">
        <thead>
            <tr
                class="bg-base-200/60 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                {{-- Atlet: lebar 60% di mobile, otomatis di desktop --}}
                <th class="py-2.5 px-2 sm:px-4 text-left w-[60%] lg:w-auto">Atlet</th>
                {{-- Sekolah hanya muncul di desktop (lg) --}}
                <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-left">Sekolah</th>
                <th class="py-2.5 px-3 sm:px-4 text-left whitespace-nowrap">Sesi</th>
                {{-- Aksi: icon button --}}
                <th class="py-2.5 px-2 sm:px-4 text-left whitespace-nowrap w-px">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
            @forelse ($atlets as $atlet)
                <tr class="hover:bg-base-200/30 transition-colors">
                    {{-- Nama & Avatar Atlet — Sekolah & Sesi tampil di bawah nama untuk mobile/tablet --}}
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
                                    @if ($atlet->sekolah)
                                        <span>{{ $atlet->sekolah->nama_sekolah }}</span>
                                        <span class="mx-0.5">•</span>
                                    @endif
                                    <span>{{ $atlet->sesi_count }} sesi</span>
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Sekolah (Desktop saja) --}}
                    <td class="py-2.5 px-3 sm:px-4 hidden lg:table-cell align-middle">
                        <span class="text-xs text-base-content/80 truncate block max-w-[180px] xl:max-w-[250px]">
                            {{ $atlet->sekolah->nama_sekolah ?? '-' }}
                        </span>
                    </td>

                    {{-- Sesi --}}
                    <td class="py-2.5 px-3 sm:px-4 whitespace-nowrap align-middle">
                        <span
                            class="font-mono text-xs text-base-content/70 bg-base-200/60 px-2 py-1 rounded-md border border-base-200">
                            {{ $atlet->sesi_count }}
                        </span>
                    </td>

                    {{-- AKSI: icon button --}}
                    <td class="py-2 px-2 sm:py-3 sm:px-4 text-left whitespace-nowrap w-px align-middle">
                        <div class="flex items-center justify-start gap-0.5 sm:gap-1">
                            <a href="{{ route('pelatih.analisis.show', $atlet) }}"
                                class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                title="Lihat Analisis">
                                <x-display.icon name="chart-line" class="size-3.5 sm:size-4" />
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <x-table.table-empty colspan="4">Belum ada atlet.</x-table.table-empty>
            @endforelse
        </tbody>
    </x-table.data-table>
@endsection
