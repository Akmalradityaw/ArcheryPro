@extends('layouts.public')

@section('title', 'Hasil Event - ArcheryPro')

@section('content')

    {{-- ============================================================
         HERO SECTION — konsisten dengan home
    ============================================================ --}}
    <section class="relative overflow-hidden bg-primary text-primary-content">

        {{-- Background image --}}
        <img src="{{ asset('images/home-bg.jpg') }}" alt="" aria-hidden="true" fetchpriority="high"
            onerror="this.style.display='none'" class="absolute inset-0 w-full h-full object-cover" />

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-primary/75" aria-hidden="true"></div>

        {{-- Content --}}
        <div class="relative max-w-6xl mx-auto px-4 py-16 md:py-24 lg:py-28 text-center">

            {{-- Badge --}}
            <div
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full
                    bg-primary-content/10 backdrop-blur-sm
                    ring-1 ring-primary-content/20
                    text-xs font-medium mb-6">
                <span class="size-1.5 rounded-full bg-primary-content animate-pulse"></span>
                Publikasi Resmi
            </div>

            {{-- Heading --}}
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight mb-4">
                Hasil Event
            </h1>

            {{-- Subtitle --}}
            <p class="text-sm sm:text-base md:text-lg text-primary-content/85 max-w-2xl mx-auto leading-relaxed">
                Daftar event yang telah dipublikasikan beserta hasil pertandingannya.
                Klik salah satu event untuk melihat klasemen lengkap.
            </p>
        </div>
    </section>

    {{-- ============================================================
         STATISTIK — overlap dengan hero (konsisten dengan home)
    ============================================================ --}}
    @php
        $totalEvent = method_exists($events, 'total') ? $events->total() : $events->count();
    @endphp

    <section class="max-w-6xl mx-auto px-4 -mt-10 relative z-10">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-display.card-stat title="Total Event" :value="$totalEvent" icon="calendar" color="primary" />
            <x-display.card-stat title="Status Publikasi" value="Aktif" icon="check-circle" color="success" description="Terbuka untuk publik" />
            <x-display.card-stat title="Pembaruan Terakhir" :value="now()->format('d M Y')" icon="clock" color="info" description="Data skor terverifikasi" />
        </div>
    </section>

    {{-- ============================================================
         CONTENT — Daftar Event
    ============================================================ --}}
    <section class="max-w-6xl mx-auto px-4 py-14">

        @if ($events->isEmpty())
            {{-- Empty State --}}
            <div class="card bg-base-100 border border-dashed border-base-300">
                <div class="card-body items-center text-center py-14">
                    <div class="size-14 rounded-full bg-base-200 flex items-center justify-center mb-3">
                        <x-display.icon name="calendar" class="size-7 text-base-content/40" />
                    </div>
                    <h3 class="font-semibold">Belum Ada Event</h3>
                    <p class="text-sm text-base-content/60 max-w-sm">
                        Belum ada event yang dipublikasikan. Silakan cek kembali di lain waktu.
                    </p>
                    <a href="{{ route('home') }}" class="btn btn-primary btn-sm mt-4">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        @else
            @php
                $firstItem = method_exists($events, 'firstItem') ? $events->firstItem() ?? 1 : 1;
                $lastItem = method_exists($events, 'lastItem')
                    ? $events->lastItem() ?? $events->count()
                    : $events->count();
            @endphp

            {{-- Info counter --}}
            <div class="flex items-center justify-between mb-5">
                <p class="text-sm text-base-content/60">
                    Menampilkan
                    <span class="font-semibold text-base-content">{{ $firstItem }}</span>
                    –
                    <span class="font-semibold text-base-content">{{ $lastItem }}</span>
                    dari
                    <span class="font-semibold text-base-content">{{ $totalEvent }}</span>
                    event
                </p>
            </div>

            {{-- Tabel Event --}}
            <div class="card bg-base-100 shadow-sm border border-base-300 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead class="bg-base-200">
                            <tr>
                                <th class="w-12 text-center">#</th>
                                <th>Event</th>
                                <th class="hidden md:table-cell">Tanggal</th>
                                <th class="hidden lg:table-cell">Lokasi</th>
                                <th class="w-28 text-center">Status</th>
                                <th class="w-28 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($events as $i => $event)
                                <tr class="hover">
                                    <td class="text-center text-base-content/60">
                                        {{ $firstItem + $i }}
                                    </td>
                                    <td>
                                        <div class="font-semibold">{{ $event->nama_event }}</div>
                                        <div class="text-xs text-base-content/60 md:hidden mt-0.5">
                                            {{ $event->tanggal->format('d M Y') }} · {{ $event->lokasi ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="hidden md:table-cell text-sm whitespace-nowrap">
                                        {{ $event->tanggal->format('d M Y') }}
                                    </td>
                                    <td class="hidden lg:table-cell text-sm text-base-content/70">
                                        {{ $event->lokasi ?? '-' }}
                                    </td>
                                    <td class="text-center">
                                        <x-display.badge-status :status="$event->status" />
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('events.show', $event) }}" class="btn btn-primary btn-xs gap-1">
                                            Hasil
                                            <x-display.icon name="arrow-right" class="size-3" />
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if (method_exists($events, 'links'))
                <div class="mt-6 flex justify-center">
                    {{ $events->links() }}
                </div>
            @endif
        @endif
    </section>

@endsection
