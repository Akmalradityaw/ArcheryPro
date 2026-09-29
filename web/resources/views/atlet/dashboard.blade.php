@extends('layouts.app')

@section('title', 'Dashboard Atlet')
@section('page-title', 'Dashboard Atlet')

@section('content')
    <x-display.page-header title="Dashboard Atlet" subtitle="Pantau perkembangan akurasi tembakan dan konsistensi latihanmu" />

    {{-- Alert Catatan Pelatih yang Belum Dibaca --}}
    @if ($catatanBelumDibaca)
        <div class="card bg-primary/10 border-2 border-primary text-base-content shadow-sm mb-6 p-4 sm:p-5">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="size-9 rounded-lg bg-primary text-white flex items-center justify-center shrink-0">
                        <x-display.icon name="clipboard" class="size-5" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="badge badge-sm bg-primary text-white font-bold border-0">Catatan Pelatih Baru</span>
                            <span class="text-xs text-base-content/60 font-mono">
                                {{ $catatanBelumDibaca->tanggal_sesi ? $catatanBelumDibaca->tanggal_sesi->format('d M Y') : '' }}
                            </span>
                        </div>
                        <p class="text-sm font-semibold text-base-content mt-1">
                            Sesi: {{ $catatanBelumDibaca->sesiLatihan?->nama_sesi ?? $catatanBelumDibaca->event?->nama_event ?? 'Latihan Rutin' }} ({{ $catatanBelumDibaca->jarak_meter }}m)
                        </p>
                        <p class="text-xs sm:text-sm text-base-content/90 mt-1 italic bg-base-100/80 p-3 rounded-lg border border-primary/20">
                            "{{ $catatanBelumDibaca->catatan_pelatih }}"
                        </p>
                    </div>
                </div>

                <form action="{{ route('atlet.catatan.baca', $catatanBelumDibaca) }}" method="POST" class="sm:self-center shrink-0">
                    @csrf
                    <button type="submit" class="btn btn-sm bg-primary text-white hover:bg-primary-focus border-0 gap-1.5 shadow-xs">
                        <x-display.icon name="check" class="size-4" />
                        <span>Tandai Sudah Dibaca</span>
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- Grid Kartu Statistik --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-display.card-stat title="Total Latihan" :value="$totalLatihan" icon="calendar" />
        <x-display.card-stat title="Rata-rata Skor" :value="$rataRata" icon="chart-line" />
        <x-display.card-stat title="Skor Tertinggi" :value="number_format($skorTerbaik)" icon="star" />
        <x-display.card-stat title="Total Anak Panah" :value="number_format($totalPanah)" icon="award" />
    </div>

    {{-- Seksi Badge Pencapaian Motivasi --}}
    <div class="card bg-base-100 border border-base-300 shadow-sm mb-6">
        <div class="p-4 sm:p-5 border-b border-base-200">
            <h2 class="text-base font-bold text-base-content">Lencana Pencapaian Latihan</h2>
            <p class="text-xs text-base-content/50 mt-0.5">Penghargaan atas komitmen, skor, dan volume latihanmu</p>
        </div>
        <div class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach ($badges as $badge)
                <div class="border rounded-xl p-3.5 flex items-start gap-3 transition-colors {{ $badge['unlocked'] ? 'bg-primary/5 border-primary/30' : 'bg-base-200/40 border-base-300 opacity-60' }}">
                    <div class="size-10 rounded-lg flex items-center justify-center shrink-0 font-bold {{ $badge['unlocked'] ? 'bg-primary text-white' : 'bg-base-300 text-base-content/40' }}">
                        @if ($badge['unlocked'])
                            <x-display.icon name="star" class="size-5" />
                        @else
                            <x-display.icon name="lock" class="size-5" />
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="font-bold text-xs sm:text-sm text-base-content truncate">{{ $badge['nama'] }}</span>
                            @if ($badge['unlocked'])
                                <span class="badge badge-xs bg-primary text-white border-0">Terbuka</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-base-content/60 mt-0.5 leading-snug">
                            {{ $badge['deskripsi'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Aksi Cepat --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('atlet.grafik.index') }}" class="btn btn-sm bg-primary text-white hover:bg-primary-focus border-0 gap-1.5 rounded-lg">
            <x-display.icon name="chart-line" class="size-4" />
            <span>Grafik Performa Lengkap</span>
        </a>
        <a href="{{ route('atlet.riwayat.index') }}" class="btn btn-sm btn-outline border-base-300 text-base-content hover:bg-base-200 gap-1.5 rounded-lg">
            <x-display.icon name="clipboard" class="size-4" />
            <span>Riwayat Latihan</span>
        </a>
        <a href="{{ route('atlet.ekspor.index') }}" class="btn btn-sm btn-outline border-base-300 text-base-content hover:bg-base-200 gap-1.5 rounded-lg">
            <x-display.icon name="download" class="size-4" />
            <span>Unduh Laporan</span>
        </a>
    </div>

    {{-- Kartu Tabel Skor Terbaru --}}
    <div class="card bg-base-100 border border-base-300 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-base-200 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-sm font-bold text-base-content tracking-tight">Sesi Latihan Terakhir</h2>
                <p class="text-xs text-base-content/50 mt-0.5">5 sesi latihan yang baru saja dicatat</p>
            </div>
            <a href="{{ route('atlet.riwayat.index') }}" class="btn btn-ghost btn-xs text-primary font-semibold hover:bg-primary/10 gap-1">
                <span>Lihat Semua</span>
                <x-display.icon name="chevron-right" class="size-3" />
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead class="bg-base-200/60 text-[11px] font-bold uppercase tracking-wider text-base-content/60">
                    <tr>
                        <th class="py-2.5 px-3 sm:px-4 text-left">Tanggal</th>
                        <th class="py-2.5 px-3 sm:px-4 text-left">Sesi Latihan</th>
                        <th class="py-2.5 px-3 sm:px-4 text-center hidden sm:table-cell">Jarak</th>
                        <th class="py-2.5 px-3 sm:px-4 text-right whitespace-nowrap">Total Skor</th>
                        <th class="py-2.5 px-3 sm:px-4 text-left hidden md:table-cell">Catatan Pelatih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200 text-xs font-medium text-base-content">
                    @forelse ($skorTerbaru as $sesi)
                        <tr class="hover:bg-base-200/30 transition-colors">
                            <td class="py-3 px-3 sm:px-4 whitespace-nowrap align-middle">
                                <span class="font-mono text-xs text-base-content/70 bg-base-200/60 px-2 py-1 rounded-md border border-base-200">
                                    {{ $sesi->tanggal_sesi ? $sesi->tanggal_sesi->format('d/m/Y') : '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-3 sm:px-4 align-middle">
                                <div class="font-semibold text-base-content">
                                    {{ $sesi->sesiLatihan?->nama_sesi ?? $sesi->event?->nama_event ?? 'Latihan Rutin' }}
                                </div>
                                <div class="text-[10px] text-base-content/50 sm:hidden mt-0.5">
                                    {{ $sesi->jarak_meter ?? 18 }}m
                                    @if ($sesi->catatan_pelatih && !$sesi->isCatatanDibaca())
                                        • <span class="text-amber-600 font-bold">Ada Catatan Baru</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-3 sm:px-4 text-center align-middle whitespace-nowrap font-mono text-xs hidden sm:table-cell">
                                {{ $sesi->jarak_meter ?? 18 }}m
                            </td>
                            <td class="py-3 px-3 sm:px-4 text-right whitespace-nowrap align-middle">
                                <span class="font-bold text-sm text-primary">
                                    {{ number_format($sesi->total_skor ?? 0) }}
                                </span>
                            </td>
                            <td class="py-3 px-3 sm:px-4 align-middle max-w-xs hidden md:table-cell">
                                @if ($sesi->catatan_pelatih)
                                    <div class="text-xs text-base-content/80 line-clamp-1 italic">
                                        "{{ $sesi->catatan_pelatih }}"
                                    </div>
                                    @if ($sesi->isCatatanDibaca())
                                        <span class="text-[10px] text-primary font-medium inline-flex items-center gap-0.5">
                                            <x-display.icon name="check" class="size-3 shrink-0" />
                                            <span>Dibaca</span>
                                        </span>
                                    @else
                                        <span class="badge badge-xs bg-amber-500 text-white border-0 font-medium">Baru</span>
                                    @endif
                                @else
                                    <span class="text-xs text-base-content/40 italic">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <x-table.table-empty colspan="5">Belum ada skor sesi latihan.</x-table.table-empty>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
