<!DOCTYPE html>
<html lang="id" data-theme="archery" class="scroll-smooth">

<head>
    @include('layouts.partials.head')
    @stack('styles')
</head>

<body class="bg-base-200 min-h-[100dvh] flex flex-col antialiased">

    {{-- NAVBAR --}}
    @include('layouts.partials.public-navbar')  

    {{-- MAIN --}}
    <main class="flex-1">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-neutral text-neutral-content mt-16">
        <div class="max-w-6xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="size-8 rounded-lg bg-neutral-content/15 flex items-center justify-center">
                        <x-display.icon name="target" class="size-4" />
                    </div>
                    <h3 class="font-bold text-lg">ArcheryPro</h3>
                </div>
                <p class="text-sm text-neutral-content/70 leading-relaxed">
                    Sistem data skor panahan yang transparan, tercatat, dan terpantau
                    untuk mendukung pembinaan atlet jangka panjang.
                </p>
            </div>

            <div>
                <h4 class="font-semibold mb-2 text-sm uppercase tracking-wide text-neutral-content/80">Navigasi</h4>
                <ul class="space-y-1 text-sm">
                    <li><a href="{{ route('home') }}" class="link link-hover">Beranda</a></li>
                    <li><a href="{{ route('events.index') }}" class="link link-hover">Hasil Event</a></li>
                    <li><a href="{{ route('login') }}" class="link link-hover">Masuk</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-2 text-sm uppercase tracking-wide text-neutral-content/80">Kontak</h4>
                <ul class="space-y-1 text-sm text-neutral-content/70">
                    <li>
                        <a href="mailto:info@archerypro.local" class="link link-hover">
                            info@archerypro.local
                        </a>
                    </li>
                    <li>
                        <a href="tel:+6221000000" class="link link-hover">
                            (021) 000-0000
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-neutral-content/10">
            <div class="max-w-6xl mx-auto px-4 py-4 text-center text-xs text-neutral-content/60">
                &copy; {{ date('Y') }} ArcheryPro. All rights reserved.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>
