@extends('layouts.app')

@section('title', 'Dashboard Scoring')
@section('page-title', 'Dashboard Scoring')

@section('content')
    <x-display.page-header title="Dashboard Scoring" subtitle="Ringkasan aktivitas input skor hari ini" />

    {{-- Grid Kartu Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <x-display.card-stat title="Sesi Hari Ini" :value="$sesiHariIni" icon="calendar" />
        <x-display.card-stat title="Total Skor Hari Ini" :value="$totalSkorHariIni" icon="chart-line" color="info" />
        <x-display.card-stat title="End Diinput Hari Ini" :value="$totalEndHariIni" icon="target" color="success" />
    </div>

    {{-- Aksi Cepat --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <x-form.btn-primary href="{{ route('scoring.input.index') }}" size="sm" class="gap-1.5 rounded-xl">
            <x-display.icon name="plus" class="size-4 shrink-0" />
            <span>Input Skor</span>
        </x-form.btn-primary>

        <x-form.btn-primary href="{{ route('scoring.riwayat.index') }}" size="sm" variant="outline"
            class="gap-1.5 rounded-xl">
            <x-display.icon name="clock" class="size-4 shrink-0" />
            <span>Riwayat</span>
        </x-form.btn-primary>
    </div>

    {{-- Kartu Tabel Input Terakhir --}}
    <div class="bg-base-100 border border-base-200 rounded-xl shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-base-200 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-sm font-bold text-base-content tracking-tight">Input Terakhir Saya</h2>
                <p class="text-xs text-base-content/50 mt-0.5">5 sesi terakhir yang kamu input hari ini</p>
            </div>
            @if (Route::has('scoring.riwayat.index'))
                <a href="{{ route('scoring.riwayat.index') }}"
                    class="btn btn-ghost btn-xs text-primary font-semibold hover:bg-primary/10 gap-1">
                    <span>Lihat Semua</span>
                    <x-display.icon name="chevron-right" class="size-3" />
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-base-200/50 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                        <th class="py-2.5 px-2 sm:px-4 text-left">Tanggal</th>
                        <th class="py-2.5 px-3 sm:px-4 text-left">Atlet</th>
                        <th class="py-2.5 px-2 sm:px-4 text-right whitespace-nowrap">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
                    @forelse ($riwayatTerbaru as $sesi)
                        <tr class="hover:bg-base-200/30 transition-colors">
                            <td class="py-2.5 px-2 sm:px-4 whitespace-nowrap align-middle">
                                <span
                                    class="font-mono text-xs text-base-content/70 bg-base-200/60 px-2 py-1 rounded-md border border-base-200">
                                    {{ $sesi->tanggal_sesi->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 sm:px-4 align-middle">
                                <div class="flex items-center gap-2.5">
                                    <div
                                        class="size-7 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-[10px] uppercase">
                                        {{ strtoupper(substr($sesi->atlet->nama_lengkap ?? 'A', 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-base-content truncate max-w-[200px] sm:max-w-xs">
                                        {{ $sesi->atlet->nama_lengkap ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-2.5 px-2 sm:px-4 text-right whitespace-nowrap align-middle">
                                <span class="font-semibold text-sm text-base-content tracking-tight">
                                    {{ number_format($sesi->total_skor ?? 0) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <x-table.table-empty colspan="3">Belum ada input hari ini.</x-table.table-empty>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
