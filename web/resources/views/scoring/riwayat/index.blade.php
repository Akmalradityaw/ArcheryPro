@extends('layouts.app')

@section('title', 'Riwayat Input')
@section('page-title', 'Riwayat Input')

@section('content')
    <x-display.page-header title="Riwayat Input Saya" subtitle="Semua sesi yang pernah kamu input" />

    <x-table.data-table :paginator="$sesi" :cari="false">
        <thead>
            <tr
                class="bg-base-200/60 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                <th class="py-2.5 px-2 sm:px-4 text-left whitespace-nowrap">Tanggal</th>
                <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-left">Sesi Latihan</th>
                <th class="py-2.5 px-3 sm:px-4 text-left">Atlet</th>
                <th class="py-2.5 px-3 sm:px-4 text-left whitespace-nowrap">Total</th>
                <th class="py-2.5 px-2 sm:px-4 text-left whitespace-nowrap w-px">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
            @forelse ($sesi as $s)
                <tr class="hover:bg-base-200/30 transition-colors">
                    {{-- Tanggal --}}
                    <td class="py-2.5 px-2 sm:px-4 whitespace-nowrap align-middle">
                        <span
                            class="font-mono text-xs text-base-content/70 bg-base-200/60 px-2 py-1 rounded-md border border-base-200">
                            {{ $s->tanggal_sesi->format('d/m/Y') }}
                        </span>
                    </td>

                    {{-- Sesi Latihan (Desktop) --}}
                    <td class="py-2.5 px-3 sm:px-4 hidden lg:table-cell align-middle">
                        <span class="text-xs text-base-content/80 truncate block max-w-[200px] xl:max-w-[280px]">
                            {{ $s->sesiLatihan->nama_sesi ?? $s->event->nama_event ?? '-' }}
                        </span>
                    </td>

                    {{-- Atlet — dengan sesi di bawah nama untuk mobile/tablet --}}
                    <td class="py-2.5 px-3 sm:px-4 min-w-0 align-middle">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div
                                class="size-7 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-[10px] uppercase">
                                {{ strtoupper(substr($s->atlet->nama_lengkap ?? 'A', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="font-semibold text-xs sm:text-sm text-base-content truncate">
                                    {{ $s->atlet->nama_lengkap ?? '-' }}
                                </div>
                                <div class="text-[10px] sm:text-[11px] text-base-content/50 lg:hidden mt-0.5 truncate">
                                    {{ $s->sesiLatihan->nama_sesi ?? $s->event->nama_event ?? '-' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Total --}}
                    <td class="py-2.5 px-3 sm:px-4 whitespace-nowrap align-middle">
                        <span class="font-bold text-sm text-primary tracking-tight">
                            {{ number_format($s->total_skor ?? 0) }}
                        </span>
                    </td>

                    {{-- Aksi --}}
                    <td class="py-2 px-2 sm:py-3 sm:px-4 text-left whitespace-nowrap w-px align-middle">
                        <div class="flex items-center justify-start gap-0.5 sm:gap-1">
                            <a href="{{ route('scoring.riwayat.show', $s) }}"
                                class="btn btn-ghost btn-square btn-xs size-7 sm:size-8 text-base-content/70 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                title="Lihat Detail">
                                <x-display.icon name="eye" class="size-3.5 sm:size-4" />
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <x-table.table-empty colspan="5">Belum ada riwayat input.</x-table.table-empty>
            @endforelse
        </tbody>
    </x-table.data-table>
@endsection
