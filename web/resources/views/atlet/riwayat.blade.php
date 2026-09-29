@extends('layouts.app')

@section('title', 'Riwayat Latihan')
@section('page-title', 'Riwayat Latihan')

@section('content')
    <div class="mb-4">
        <a href="{{ route('atlet.dashboard') }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-base-content/70 hover:text-primary transition-colors">
            <x-display.icon name="arrow-left" class="size-4 shrink-0" />
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    <x-display.page-header title="Riwayat Sesi Latihan" subtitle="Semua sesi latihan mingguan dan catatan instruksi yang pernah kamu terima" />

    {{-- Filter Bar --}}
    <div class="card bg-base-100 border border-base-300 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('atlet.riwayat.index') }}" class="flex flex-col md:flex-row flex-wrap items-stretch md:items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-base-content/60 shrink-0">Rentang:</span>
                <input type="date" name="dari" value="{{ $filter['dari'] ?? '' }}"
                    class="input input-bordered input-sm border-base-300 focus:border-primary text-xs w-full sm:w-auto" aria-label="Dari tanggal">
                <span class="text-xs text-base-content/40 shrink-0">s/d</span>
                <input type="date" name="sampai" value="{{ $filter['sampai'] ?? '' }}"
                    class="input input-bordered input-sm border-base-300 focus:border-primary text-xs w-full sm:w-auto" aria-label="Sampai tanggal">
            </div>

            <div class="flex-1 min-w-[200px]">
                <select name="sesi_latihan_id"
                    class="select select-bordered select-sm w-full border-base-300 focus:border-primary text-xs"
                    aria-label="Filter Sesi Latihan">
                    <option value="">Semua Jadwal Sesi Latihan</option>
                    @foreach ($sesiLatihans as $sl)
                        <option value="{{ $sl->id }}" @selected(request('sesi_latihan_id', $filter['sesi_latihan_id'] ?? '') == $sl->id)>
                            {{ $sl->nama_sesi }} ({{ $sl->tanggal ? $sl->tanggal->format('d/m/Y') : '' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="btn btn-sm bg-primary text-white hover:bg-primary-focus border-0 gap-1.5 shadow-xs">
                    <x-display.icon name="search" class="size-3.5" />
                    <span>Filter</span>
                </button>
                @if (!empty($filter['dari']) || !empty($filter['sampai']) || !empty($filter['sesi_latihan_id']) || !empty($filter['event_id']))
                    <a href="{{ route('atlet.riwayat.index') }}" class="btn btn-sm btn-ghost text-xs">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="card bg-base-100 border border-base-300 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full text-left">
                <thead class="bg-base-200/80 text-[11px] font-bold uppercase tracking-wider text-base-content/70">
                    <tr>
                        <th class="py-3 px-3 sm:px-4">Tanggal</th>
                        <th class="py-3 px-3 sm:px-4">Sesi Latihan</th>
                        <th class="py-3 px-3 sm:px-4 text-center hidden sm:table-cell">Jarak</th>
                        <th class="py-3 px-3 sm:px-4 text-center hidden md:table-cell">End</th>
                        <th class="py-3 px-3 sm:px-4 text-right">Total Skor</th>
                        <th class="py-3 px-3 sm:px-4">Catatan & Masukan Pelatih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200 text-xs sm:text-sm">
                    @forelse ($sesi as $s)
                        <tr class="hover:bg-base-200/40 transition-colors">
                            {{-- Tanggal --}}
                            <td class="py-3 px-3 sm:px-4 whitespace-nowrap align-middle">
                                <span class="font-mono text-xs text-base-content/70 bg-base-200/60 px-2 py-1 rounded-md border border-base-200">
                                    {{ $s->tanggal_sesi ? $s->tanggal_sesi->format('d/m/Y') : '-' }}
                                </span>
                            </td>

                            {{-- Sesi Latihan --}}
                            <td class="py-3 px-3 sm:px-4 align-middle">
                                <div class="font-semibold text-base-content">
                                    {{ $s->sesiLatihan?->nama_sesi ?? $s->event?->nama_event ?? 'Latihan Rutin' }}
                                </div>
                                <div class="text-[11px] text-base-content/50">
                                    {{ $s->sesiLatihan?->lokasi ?? 'Range Klub' }}
                                    <span class="sm:hidden font-mono font-medium text-primary ml-1">• {{ $s->jarak_meter ?? 18 }}m</span>
                                </div>
                            </td>

                            {{-- Jarak --}}
                            <td class="py-3 px-3 sm:px-4 text-center align-middle whitespace-nowrap font-mono text-xs hidden sm:table-cell">
                                {{ $s->jarak_meter ?? 18 }}m
                            </td>

                            {{-- End --}}
                            <td class="py-3 px-3 sm:px-4 text-center align-middle whitespace-nowrap font-mono text-xs hidden md:table-cell">
                                {{ $s->skor->count() }} end
                            </td>

                            {{-- Total Skor --}}
                            <td class="py-3 px-3 sm:px-4 text-right align-middle whitespace-nowrap">
                                <span class="font-bold text-sm sm:text-base text-primary">
                                    {{ number_format($s->total_skor ?? 0) }}
                                </span>
                            </td>

                            {{-- Catatan Pelatih & Tombol Baca --}}
                            <td class="py-3 px-4 align-middle max-w-sm">
                                @if ($s->catatan_pelatih)
                                    <div class="p-2.5 rounded-lg bg-base-200/50 border border-base-300">
                                        <p class="text-xs text-base-content/90 italic">
                                            "{{ $s->catatan_pelatih }}"
                                        </p>
                                        <div class="mt-1.5 flex items-center justify-between gap-2 pt-1 border-t border-base-300/50">
                                            @if ($s->isCatatanDibaca())
                                                <span class="text-[10px] text-primary font-medium flex items-center gap-1">
                                                    <span class="inline-flex items-center gap-1">
                                                        <x-display.icon name="check" class="size-3 shrink-0" />
                                                        <span>Sudah dibaca</span>
                                                    </span>
                                                    <span class="text-base-content/40 font-mono">{{ $s->catatan_dibaca_at->format('d/m H:i') }}</span>
                                                </span>
                                            @else
                                                <span class="badge badge-xs bg-amber-500 text-white border-0 font-medium">
                                                    Belum Dibaca
                                                </span>
                                                <form action="{{ route('atlet.catatan.baca', $s) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs bg-primary text-white hover:bg-primary-focus border-0">
                                                        Tandai Dibaca
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-base-content/40 italic">Tidak ada catatan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <x-table.table-empty colspan="6">
                            Belum ada riwayat sesi latihan pada filter yang dipilih.
                        </x-table.table-empty>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($sesi->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $sesi->links() }}
            </div>
        @endif
    </div>
@endsection
