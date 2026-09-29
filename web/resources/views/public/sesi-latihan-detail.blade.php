@extends('layouts.public')

@section('title', $sesiLatihan->nama_sesi . ' - ArcheryPro')

@section('content')
    {{-- HERO SECTION --}}
    <section class="relative overflow-hidden bg-primary text-primary-content">
        <img src="{{ asset('images/home-bg.jpg') }}" alt="" aria-hidden="true" fetchpriority="high"
            onerror="this.style.display='none'" class="absolute inset-0 w-full h-full object-cover" />
        <div class="absolute inset-0 bg-primary/75" aria-hidden="true"></div>

        <div class="relative max-w-6xl mx-auto px-4 py-16 md:py-24">
            <nav class="text-xs text-primary-content/70 mb-6" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="link link-hover">Beranda</a>
                <span class="mx-1.5">/</span>
                <span class="text-primary-content/90 line-clamp-1">{{ $sesiLatihan->nama_sesi }}</span>
            </nav>

            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary-content/10 backdrop-blur-sm ring-1 ring-primary-content/20 text-xs font-medium mb-4">
                <span class="size-1.5 rounded-full bg-primary-content animate-pulse"></span>
                Papan Skor Sesi Latihan
            </div>

            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5">
                <div class="max-w-3xl">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight mb-4">
                        {{ $sesiLatihan->nama_sesi }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-primary-content/85">
                        <span class="flex items-center gap-1.5">
                            <x-display.icon name="calendar" class="size-4" />
                            {{ $sesiLatihan->tanggal ? $sesiLatihan->tanggal->format('d F Y') : '-' }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <x-display.icon name="clock" class="size-4" />
                            {{ substr($sesiLatihan->jam_mulai, 0, 5) }} - {{ substr($sesiLatihan->jam_selesai, 0, 5) }} WIB
                        </span>
                        <span class="flex items-center gap-1.5">
                            <x-display.icon name="pin" class="size-4" />
                            {{ $sesiLatihan->lokasi }}
                        </span>
                        <span class="flex items-center gap-1.5 font-semibold">
                            <x-display.icon name="target" class="size-4 shrink-0" />
                            <span>{{ str_replace('_', ' ', ucfirst($sesiLatihan->jenis_latihan)) }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- TABEL PERINGKAT --}}
    <main class="max-w-6xl mx-auto px-4 py-8 md:py-12">
        <div class="card bg-base-100 border border-base-300 shadow-sm overflow-hidden rounded-2xl">
            <div class="p-4 sm:p-5 border-b border-base-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-base-content">Hasil Skor Latihan</h2>
                    <p class="text-xs text-base-content/50 mt-0.5">Daftar skor atlet yang telah dipublikasikan</p>
                </div>
                <div class="text-xs text-base-content/60">
                    Total: <strong class="text-primary">{{ $peringkat->count() }}</strong> atlet terdata
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-zebra w-full text-left">
                    <thead class="bg-base-200/80 text-[11px] font-bold uppercase tracking-wider text-base-content/70">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">Rank</th>
                            <th class="py-3 px-4">Atlet</th>
                            <th class="py-3 px-4 hidden md:table-cell">Sekolah</th>
                            <th class="py-3 px-4 text-center">Jarak</th>
                            <th class="py-3 px-4 text-right">Total Skor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-base-200 text-xs sm:text-sm">
                        @forelse ($peringkat as $idx => $s)
                            <tr class="hover:bg-base-200/40 transition-colors">
                                <td class="py-3 px-4 text-center font-bold font-mono">
                                    @if ($idx === 0)
                                        <span class="badge badge-sm bg-amber-500 text-white border-0 font-bold">1</span>
                                    @elseif ($idx === 1)
                                        <span class="badge badge-sm bg-slate-400 text-white border-0 font-bold">2</span>
                                    @elseif ($idx === 2)
                                        <span class="badge badge-sm bg-amber-700 text-white border-0 font-bold">3</span>
                                    @else
                                        {{ $idx + 1 }}
                                    @endif
                                </td>

                                <td class="py-3 px-4 align-middle">
                                    <div class="font-bold text-base-content">{{ $s->atlet->nama_lengkap }}</div>
                                    <div class="text-[11px] text-base-content/50 md:hidden">
                                        {{ $s->atlet->sekolah?->nama_sekolah ?? '-' }}
                                    </div>
                                </td>

                                <td class="py-3 px-4 hidden md:table-cell align-middle text-base-content/70 text-xs">
                                    {{ $s->atlet->sekolah?->nama_sekolah ?? '-' }}
                                </td>

                                <td class="py-3 px-4 text-center align-middle whitespace-nowrap font-mono text-xs">
                                    {{ $s->jarak_meter ?? 18 }}m
                                </td>

                                <td class="py-3 px-4 text-right align-middle whitespace-nowrap">
                                    <span class="font-bold text-sm sm:text-base text-primary">
                                        {{ number_format($s->total_skor) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <x-table.table-empty colspan="5">
                                Belum ada atlet dengan izin publikasi skor pada sesi latihan ini.
                            </x-table.table-empty>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection
