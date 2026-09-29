@extends('layouts.public')

@section('title', 'Beranda - ArcheryPro')

@section('content')

    {{-- HERO --}}
    <section class="relative overflow-hidden bg-primary text-primary-content">
        {{-- Background image --}}
        <img src="{{ asset('images/home-bg.jpg') }}" alt="Latihan panahan" fetchpriority="high"
            class="absolute inset-0 w-full h-full object-cover" />

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
                Sistem Data Skor Panahan
            </div>

            {{-- Heading --}}
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight mb-4">
                Catat. Pantau. Berkembang.
            </h1>

            {{-- Subtitle --}}
            <p class="text-sm sm:text-base md:text-lg text-primary-content/85 max-w-2xl mx-auto mb-8 leading-relaxed">
                Platform pencatatan skor panahan yang transparan, tercatat rapi, dan
                memudahkan pemantauan performa atlet dari waktu ke waktu.
            </p>

            {{-- CTA --}}
            <div class="flex flex-wrap justify-center gap-3">
                <a href="{{ route('events.index') }}"
                    class="btn bg-primary-content text-primary hover:bg-primary-content/90 border-0 shadow-sm gap-2">
                    Lihat Hasil
                    <x-display.icon name="arrow-right" class="size-4" />
                </a>
                <a href="{{ route('login') }}"
                    class="btn btn-outline text-primary-content border-primary-content/50
                      hover:bg-primary-content hover:text-primary hover:border-primary-content">
                    Masuk
                </a>
            </div>
        </div>
    </section>

    {{-- STATISTIK --}}
    <section class="max-w-6xl mx-auto px-4 -mt-10 relative z-10">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-display.card-stat title="Event Publik" :value="$totalEvent" />
            <x-display.card-stat title="Atlet Terdaftar" :value="$totalAtlet" />
            <x-display.card-stat title="Sesi Tercatat" :value="$totalSesi" />
        </div>
    </section>

    {{-- EVENT TERBARU --}}
    <section class="max-w-6xl mx-auto px-4 py-14">
        <div class="flex items-end justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold">Event Terbaru</h2>
                <p class="text-sm text-base-content/60 mt-1">
                    Hasil pertandingan yang telah dipublikasikan
                </p>
            </div>
            <a href="{{ route('events.index') }}" class="btn btn-ghost btn-sm text-primary hidden md:inline-flex">
                Lihat semua
                <x-display.icon name="arrow-right" class="size-4" />
            </a>
        </div>

        @if ($events->isEmpty())
            <div class="card bg-base-100 border border-dashed border-base-300">
                <div class="card-body items-center text-center py-14">
                    <div class="size-14 rounded-full bg-base-200 flex items-center justify-center mb-3">
                        <x-display.icon name="target" class="size-7 text-base-content/40" />
                    </div>
                    <h3 class="font-semibold">Belum Ada Event</h3>
                    <p class="text-sm text-base-content/60 max-w-sm">
                        Saat ini belum ada event yang dipublikasikan. Silakan cek kembali nanti.
                    </p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($events as $event)
                    <article
                        class="card bg-base-100 shadow-sm border border-base-300 hover:shadow-sm hover:border-primary/30 transition-all duration-200">
                        <div class="card-body p-5">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="size-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                                    <x-display.icon name="calendar" class="size-5 text-primary" />
                                </div>
                                <x-display.badge-status :status="$event->status" />
                            </div>

                            <h3 class="card-title text-base leading-snug line-clamp-2">
                                {{ $event->nama_event }}
                            </h3>

                            <ul class="text-sm text-base-content/70 space-y-1 mt-1">
                                <li class="flex items-center gap-2">
                                    <x-display.icon name="calendar" class="size-4 text-primary/70" />
                                    {{ $event->tanggal->format('d M Y') }}
                                </li>
                                <li class="flex items-center gap-2">
                                    <x-display.icon name="pin" class="size-4 text-primary/70" />
                                    {{ $event->lokasi ?? 'Lokasi belum ditentukan' }}
                                </li>
                            </ul>

                            <div class="card-actions justify-end mt-3 pt-3 border-t border-base-200">
                                <a href="{{ route('events.show', $event) }}" class="btn btn-primary btn-sm">
                                    Lihat Hasil
                                    <x-display.icon name="arrow-right" class="size-4" />
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-8 text-center md:hidden">
                <a href="{{ route('events.index') }}" class="btn btn-outline btn-sm text-primary border-primary/40">
                    Lihat semua event
                </a>
            </div>
        @endif
    </section>

@endsection
