@extends('layouts.app')

@section('title', 'Dashboard Pelatih')
@section('page-title', 'Dashboard Pelatih')

@section('content')
    {{-- Header Halaman --}}
    <x-display.page-header title="Dashboard Pelatih"
        subtitle="Pantau kehadiran latihan mingguan, volume tembakan panah, dan perkembangan atlet">
        <x-slot:actions>
            <x-form.btn-primary href="{{ route('pelatih.skor.create') }}">
                <x-display.icon name="plus" class="size-4 shrink-0" />
                <span>Input Skor Latihan</span>
            </x-form.btn-primary>
        </x-slot:actions>
    </x-display.page-header>

    {{-- Grid Kartu Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-display.card-stat title="Atlet Aktif" :value="$totalAtlet ?? 0" icon="users" />
        <x-display.card-stat title="Latihan Berlangsung" :value="$sesiLatihanAktif ?? 0" icon="calendar" />
        <x-display.card-stat title="Sesi Bulan Ini" :value="$sesiBulanIni ?? 0" icon="clock" />
        <x-display.card-stat title="Volume Panah Bulan Ini" :value="number_format($panahBulanIni ?? 0)" icon="chart-line" />
    </div>

    {{-- Grid: Jadwal Mendatang & Sesi Terbaru --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Jadwal Latihan Mendatang --}}
        <div class="card bg-base-100 border border-base-300 shadow-sm">
            <div class="p-4 sm:p-5 border-b border-base-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-base-content">Jadwal Terdekat</h2>
                    <p class="text-xs text-base-content/50 mt-0.5">Sesi latihan yang akan datang</p>
                </div>
                <a href="{{ route('pelatih.sesi-latihan.index') }}"
                    class="btn btn-ghost btn-xs text-primary font-semibold hover:bg-primary/10 gap-1">
                    <span>Semua</span>
                    <x-display.icon name="chevron-right" class="size-3" />
                </a>
            </div>

            <div class="p-4 sm:p-5 divide-y divide-base-200">
                @forelse ($sesiLatihanMendatang as $jadwal)
                    <div class="py-3 first:pt-0 last:pb-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-xs sm:text-sm text-base-content">{{ $jadwal->nama_sesi }}</span>
                            <span class="badge badge-xs badge-outline text-base-content/60">{{ str_replace('_', ' ', ucfirst($jadwal->jenis_latihan)) }}</span>
                        </div>
                        <div class="text-[11px] text-base-content/60 mt-1 flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-1">
                                <x-display.icon name="calendar" class="size-3 text-primary/70 shrink-0" />
                                <span>{{ $jadwal->tanggal ? $jadwal->tanggal->format('d M Y') : '-' }}</span>
                            </span>
                            @if ($jadwal->jam_mulai)
                                <span class="inline-flex items-center gap-1">
                                    <x-display.icon name="clock" class="size-3 text-primary/70 shrink-0" />
                                    <span>{{ substr($jadwal->jam_mulai, 0, 5) }} WIB</span>
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1">
                                <x-display.icon name="pin" class="size-3 text-primary/70 shrink-0" />
                                <span>{{ $jadwal->lokasi ?? 'Lapangan Utama' }}</span>
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-base-content/50">
                        Belum ada jadwal latihan mendatang.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Sesi Terbaru --}}
        <div class="lg:col-span-2 card bg-base-100 border border-base-300 shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-base-200 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-base-content">Aktivitas Latihan Terbaru</h2>
                    <p class="text-xs text-base-content/50 mt-0.5">5 sesi latihan yang baru saja dicatat</p>
                </div>
                <a href="{{ route('pelatih.analisis.index') }}"
                    class="btn btn-ghost btn-xs text-primary font-semibold hover:bg-primary/10 gap-1">
                    <span>Lihat Analisis</span>
                    <x-display.icon name="chevron-right" class="size-3" />
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full text-left">
                    <thead class="bg-base-200/50 text-[11px] font-bold uppercase tracking-wider text-base-content/60">
                        <tr>
                            <th class="py-3 px-4">Tanggal & Sesi</th>
                            <th class="py-3 px-4">Atlet</th>
                            <th class="py-3 px-4 text-right">Total Skor</th>
                            <th class="py-3 px-4 text-center">Evaluasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-base-200 text-xs font-medium text-base-content">
                        @forelse ($sesiTerbaru as $sesi)
                            <tr class="hover:bg-base-200/30 transition-colors">
                                <td class="py-3 px-4 whitespace-nowrap">
                                    <div class="font-bold text-base-content">
                                        {{ $sesi->sesiLatihan?->nama_sesi ?? 'Latihan Rutin' }}
                                    </div>
                                    <div class="text-[11px] text-base-content/50 font-mono mt-0.5">
                                        {{ $sesi->tanggal_sesi ? $sesi->tanggal_sesi->format('d/m/Y') : '-' }}
                                    </div>
                                </td>

                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="size-6 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-[10px] uppercase">
                                            {{ strtoupper(substr($sesi->atlet?->nama_lengkap ?? 'A', 0, 1)) }}
                                        </div>
                                        <span class="font-semibold text-base-content truncate max-w-[140px] sm:max-w-xs">
                                            {{ $sesi->atlet?->nama_lengkap ?? 'Atlet Nonaktif' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="py-3 px-4 text-right whitespace-nowrap">
                                    <span class="font-bold text-sm text-primary">
                                        {{ number_format($sesi->total_skor ?? 0) }}
                                    </span>
                                </td>

                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    @if ($sesi->catatan_pelatih)
                                        <span class="badge badge-xs bg-primary/10 text-primary border-primary/20 font-medium">
                                            ✓ Ada Catatan
                                        </span>
                                    @else
                                        <span class="badge badge-xs badge-ghost text-base-content/40">
                                            Belum Dicatat
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <x-table.table-empty colspan="4">Belum ada data sesi latihan terbaru.</x-table.table-empty>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
