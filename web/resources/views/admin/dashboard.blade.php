@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
    <x-display.page-header title="Dashboard Admin" subtitle="Ringkasan data sistem ArcheryPro" />

    {{-- Statistik --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <x-display.card-stat title="Total Atlet" :value="$totalAtlet" icon="user-plus" color="primary" />
        <x-display.card-stat title="Jadwal Latihan" :value="$totalJadwal" icon="calendar" color="success" />
        <x-display.card-stat title="Total Sesi" :value="$totalSesi" icon="clock" color="warning" />
        <x-display.card-stat title="Total End Skor" :value="$totalSkor" icon="target" color="info" />
    </div>

    {{-- Sesi Terbaru --}}
    <div class="bg-base-100 border border-base-200 rounded-xl shadow-xs overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-base-200 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-sm font-bold text-base-content tracking-tight">Sesi Latihan Terbaru</h2>
                <p class="text-xs text-base-content/50 mt-0.5">{{ $sesiTerbaru->count() }} sesi latihan terakhir</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-base-200/50 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                        <th class="py-2.5 px-2 sm:px-4 text-left">Tanggal</th>
                        <th class="py-2.5 px-3 sm:px-4 text-left">Atlet</th>
                        <th class="py-2.5 px-3 sm:px-4 hidden lg:table-cell text-left">Sesi Latihan</th>
                        <th class="py-2.5 px-2 sm:px-4 text-right whitespace-nowrap">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
                    @forelse ($sesiTerbaru as $sesi)
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
                            <td class="py-2.5 px-3 sm:px-4 hidden lg:table-cell align-middle">
                                <span class="text-xs text-base-content/80 truncate block max-w-[200px] xl:max-w-[280px]">
                                    {{ $sesi->sesiLatihan->nama_sesi ?? $sesi->event->nama_event ?? '-' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-2 sm:px-4 text-right whitespace-nowrap align-middle">
                                <span class="font-bold text-sm text-primary tracking-tight">
                                    {{ number_format($sesi->total_skor ?? 0) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <x-table.table-empty colspan="4">Belum ada sesi tercatat.</x-table.table-empty>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
