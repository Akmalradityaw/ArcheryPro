@extends('layouts.public')

@section('title', $event->nama_event . ' - ArcheryPro')

@section('content')

    {{-- ============================================================
         HERO SECTION — identik dengan home & event-result:
         background image + overlay + padding vertikal sama
    ============================================================ --}}
    <section class="relative overflow-hidden bg-primary text-primary-content">

        {{-- Background image --}}
        <img src="{{ asset('images/home-bg.jpg') }}" alt="" aria-hidden="true" fetchpriority="high"
            onerror="this.style.display='none'" class="absolute inset-0 w-full h-full object-cover" />

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-primary/75" aria-hidden="true"></div>

        {{-- Content --}}
        <div class="relative max-w-6xl mx-auto px-4 py-16 md:py-24 lg:py-28">

            {{-- Breadcrumb --}}
            <nav class="text-xs text-primary-content/70 mb-6" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="link link-hover">Beranda</a>
                <span class="mx-1.5">/</span>
                <a href="{{ route('events.index') }}" class="link link-hover">Hasil Event</a>
                <span class="mx-1.5">/</span>
                <span class="text-primary-content/90 line-clamp-1">{{ $event->nama_event }}</span>
            </nav>

            {{-- Badge --}}
            <div
                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full
                        bg-primary-content/10 backdrop-blur-sm ring-1 ring-primary-content/20
                        text-xs font-medium mb-6">
                <span class="size-1.5 rounded-full bg-primary-content animate-pulse"></span>
                Detail Event
            </div>

            {{-- Heading --}}
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5">
                <div class="max-w-3xl">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight mb-4">
                        {{ $event->nama_event }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-primary-content/85">
                        <span class="flex items-center gap-1.5">
                            <x-display.icon name="calendar" class="size-4" />
                            {{ $event->tanggal->format('d F Y') }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <x-display.icon name="pin" class="size-4" />
                            {{ $event->lokasi ?? 'Lokasi belum ditentukan' }}
                        </span>
                    </div>
                </div>

                <div class="shrink-0">
                    <x-display.badge-status :status="$event->status" class="badge-lg" />
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================
         STATISTIK — overlap -mt-10, grid 3 kolom sama seperti home
    ============================================================ --}}
    <section class="max-w-6xl mx-auto px-4 -mt-10 relative z-10">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-display.card-stat title="Peserta" :value="$peringkat->count()" />
            <x-display.card-stat title="Skor Tertinggi" :value="$peringkat->max('total_skor') ?? 0" />
            <x-display.card-stat title="Rata-rata"
                :value="$peringkat->count() ? round($peringkat->avg('total_skor'), 1) : 0" />
        </div>
    </section>

    {{-- ============================================================
         CONTENT
    ============================================================ --}}
    <section class="max-w-6xl mx-auto px-4 py-14">

        {{-- Klasemen Header --}}
        <div class="mb-6">
            <h2 class="text-2xl font-bold">Klasemen Akhir</h2>
            <p class="text-sm text-base-content/60 mt-1">
                Diurutkan berdasarkan total skor tertinggi
            </p>
        </div>

        @if ($peringkat->isEmpty())
            <div class="card bg-base-100 border border-dashed border-base-300">
                <div class="card-body items-center text-center py-14">
                    <div class="size-14 rounded-full bg-base-200 flex items-center justify-center mb-3">
                        <x-display.icon name="target" class="size-7 text-base-content/40" />
                    </div>
                    <h3 class="font-semibold">Belum Ada Skor</h3>
                    <p class="text-sm text-base-content/60 max-w-sm">
                        Event ini belum memiliki data skor yang tercatat.
                    </p>
                </div>
            </div>
        @else
            {{-- TOP 3 PODIUM --}}
            @php
                $podium = [
                    [
                        'key' => 0,
                        'order' => 'md:order-2',
                        'medal' => '🥇',
                        'ring' => 'ring-4 ring-yellow-400',
                        'bg' => 'bg-yellow-50',
                        'label' => 'Juara 1',
                    ],
                    [
                        'key' => 1,
                        'order' => 'md:order-1',
                        'medal' => '🥈',
                        'ring' => 'ring-4 ring-gray-300',
                        'bg' => 'bg-gray-50',
                        'label' => 'Juara 2',
                    ],
                    [
                        'key' => 2,
                        'order' => 'md:order-3',
                        'medal' => '🥉',
                        'ring' => 'ring-4 ring-amber-600',
                        'bg' => 'bg-amber-50',
                        'label' => 'Juara 3',
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                @foreach ($podium as $slot)
                    @isset($peringkat[$slot['key']])
                        @php $sesi = $peringkat[$slot['key']]; @endphp

                        <div
                            class="card {{ $slot['bg'] }} border border-base-300 shadow-sm {{ $slot['order'] }} relative overflow-hidden">
                            <span
                                class="absolute top-3 right-3 text-[10px] font-bold uppercase tracking-wider text-base-content/40">
                                {{ $slot['label'] }}
                            </span>

                            <div class="card-body items-center text-center p-6">
                                <div class="text-4xl mb-2">{{ $slot['medal'] }}</div>

                                <div
                                    class="size-16 rounded-full {{ $slot['ring'] }} bg-white flex items-center justify-center mb-3">
                                    <span class="text-xl font-bold text-primary">
                                        {{ strtoupper(substr($sesi->atlet->nama_lengkap, 0, 1)) }}
                                    </span>
                                </div>

                                <h3 class="font-bold leading-tight">{{ $sesi->atlet->nama_lengkap }}</h3>
                                <p class="text-xs text-base-content/60">
                                    {{ $sesi->atlet->sekolah->nama_sekolah ?? '-' }}
                                </p>

                                <div class="mt-3 pt-3 border-t border-base-content/10 w-full">
                                    <p class="text-3xl font-bold text-primary">{{ $sesi->total_skor }}</p>
                                    <p class="text-xs text-base-content/60">Total Skor</p>
                                </div>
                            </div>
                        </div>
                    @endisset
                @endforeach
            </div>

            {{-- FULL RANKING TABLE --}}
            <div class="card bg-base-100 border border-base-300 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead class="bg-base-200">
                            <tr>
                                <th class="w-16 text-center">Rank</th>
                                <th>Atlet</th>
                                <th class="hidden md:table-cell">Sekolah</th>
                                <th class="hidden lg:table-cell">Kategori</th>
                                <th class="text-right">Total Skor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($peringkat as $i => $sesi)
                                <tr class="hover">
                                    <td class="text-center">
                                        @if ($i < 3)
                                            <span class="badge badge-primary badge-sm font-bold">{{ $i + 1 }}</span>
                                        @else
                                            <span class="text-base-content/60 font-medium">{{ $i + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-semibold">{{ $sesi->atlet->nama_lengkap }}</div>
                                        <div class="text-xs text-base-content/60 md:hidden">
                                            {{ $sesi->atlet->sekolah->nama_sekolah ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="hidden md:table-cell text-sm text-base-content/70">
                                        {{ $sesi->atlet->sekolah->nama_sekolah ?? '-' }}
                                    </td>
                                    <td class="hidden lg:table-cell text-sm text-base-content/70">
                                        {{ $sesi->atlet->kategori->nama_kategori ?? '-' }}
                                    </td>
                                    <td class="text-right">
                                        <span class="font-bold text-primary text-lg">{{ $sesi->total_skor }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Tombol Kembali --}}
        <div class="mt-8">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}"
                class="btn btn-ghost btn-sm text-primary gap-1">
                <x-display.icon name="arrow-left" class="size-4" />
                Kembali
            </a>
        </div>
    </section>

@endsection
