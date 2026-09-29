@extends('layouts.app')

@section('title', 'Detail Sesi')
@section('page-title', 'Detail Sesi')

@section('content')
    {{-- Back link --}}
    <div class="mb-4">
        <a href="{{ route('scoring.riwayat.index') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-base-content/70 hover:text-primary transition-colors">
            <x-display.icon name="arrow-left" class="size-4 shrink-0" />
            <span>Kembali ke Riwayat</span>
        </a>
    </div>

    <x-display.page-header title="{{ $sesi->atlet->nama_lengkap }}"
        subtitle="{{ $sesi->sesiLatihan->nama_sesi ?? $sesi->event->nama_event ?? 'Latihan Rutin' }} · {{ $sesi->tanggal_sesi->format('d/m/Y') }}{{ $sesi->jarak_meter ? ' · ' . $sesi->jarak_meter . 'm' : '' }}" />

    {{-- Card profil sesi --}}
    <div class="card bg-base-100 border border-base-200 shadow-xs rounded-2xl p-5 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="size-14 rounded-2xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center font-bold text-xl uppercase shadow-xs shrink-0">
                    {{ strtoupper(substr($sesi->atlet->nama_lengkap ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-base-content tracking-tight">
                        {{ $sesi->atlet->nama_lengkap ?? '-' }}
                    </h2>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        <span class="text-xs text-base-content/60 font-medium">
                            {{ $sesi->sesiLatihan->nama_sesi ?? $sesi->event->nama_event ?? 'Latihan Rutin' }}
                        </span>
                        <span class="text-xs text-base-content/50">•</span>
                        <span class="text-xs text-base-content/60 font-mono">
                            {{ $sesi->tanggal_sesi->format('d/m/Y') }}
                        </span>
                        @if ($sesi->jarak_meter)
                            <span class="text-xs text-base-content/50">•</span>
                            <span class="badge badge-xs badge-primary badge-outline">{{ $sesi->jarak_meter }}m</span>
                        @endif
                    </div>
                    @if ($sesi->catatan_pelatih)
                        <div class="text-xs text-base-content/70 bg-base-200/50 rounded-lg p-2 mt-2 border border-base-200">
                            <strong>Catatan:</strong> {{ $sesi->catatan_pelatih }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-center sm:text-right">
                <p class="text-xs uppercase tracking-wide text-base-content/50">Total Skor</p>
                <p class="text-3xl font-bold text-primary tracking-tight">
                    {{ number_format($sesi->total_skor ?? 0) }}
                </p>
            </div>
        </div>
    </div>

    {{-- Tabel per end --}}
    <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-base-200">
            <h2 class="text-sm font-bold text-base-content tracking-tight">Rincian per End</h2>
            <p class="text-xs text-base-content/50 mt-0.5">Skor setiap anak panah per end</p>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-base-200/50 border-b border-base-200 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-base-content/50 select-none">
                        <th class="py-2.5 px-2 sm:px-4 text-left whitespace-nowrap">End</th>
                        <th class="py-2.5 px-2 sm:px-4 text-center">1</th>
                        <th class="py-2.5 px-2 sm:px-4 text-center">2</th>
                        <th class="py-2.5 px-2 sm:px-4 text-center">3</th>
                        <th class="py-2.5 px-2 sm:px-4 text-center">4</th>
                        <th class="py-2.5 px-2 sm:px-4 text-center">5</th>
                        <th class="py-2.5 px-2 sm:px-4 text-center">6</th>
                        <th class="py-2.5 px-2 sm:px-4 text-right whitespace-nowrap">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200/60 text-xs font-medium text-base-content">
                    @forelse ($ends as $end)
                        <tr class="hover:bg-base-200/30 transition-colors">
                            <td class="py-2.5 px-2 sm:px-4 whitespace-nowrap align-middle">
                                <span
                                    class="font-mono text-xs text-base-content/70 bg-base-200/60 px-2 py-1 rounded-md border border-base-200">
                                    End {{ $end->end_ke }}
                                </span>
                            </td>
                            @foreach ([$end->skor1, $end->skor2, $end->skor3, $end->skor4, $end->skor5, $end->skor6] as $nilai)
                                <td class="py-2.5 px-2 sm:px-4 text-center align-middle">
                                    <span
                                        class="inline-flex items-center justify-center size-7 rounded-lg bg-base-200/60 font-mono text-xs font-semibold text-base-content">
                                        {{ $nilai }}
                                    </span>
                                </td>
                            @endforeach
                            <td class="py-2.5 px-2 sm:px-4 text-right whitespace-nowrap align-middle">
                                <span class="font-bold text-sm text-primary tracking-tight">
                                    {{ $end->total_end }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <x-table.table-empty colspan="8">Tidak ada data end.</x-table.table-empty>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
