<!DOCTYPE html>
<html lang="id" data-theme="archery">

<head>
    @include('layouts.partials.head')
    @stack('styles')
</head>

<body
    class="bg-base-200/50 text-base-content min-h-[100dvh] font-sans antialiased selection:bg-primary selection:text-primary-content">

    <div class="min-h-[100dvh] grid lg:grid-cols-12 overflow-hidden">

        {{-- LEFT: Visual Branding Side (Desktop & Tablet Wide) --}}
        <aside
            class="hidden lg:flex lg:col-span-5 xl:col-span-6 bg-primary text-primary-content flex-col justify-between p-10 xl:p-14 relative overflow-hidden">

            {{-- Background Target Pattern Accent --}}
            <div
                class="absolute -right-24 -top-24 w-96 h-96 rounded-full border-[30px] border-primary-content/10 pointer-events-none">
            </div>
            <div
                class="absolute -right-12 -top-12 w-72 h-72 rounded-full border-[20px] border-primary-content/10 pointer-events-none">
            </div>
            <div
                class="absolute right-24 bottom-12 w-64 h-64 rounded-full border-[15px] border-primary-content/5 pointer-events-none">
            </div>

            {{-- Top Branding Header --}}
            <div class="relative z-10">
                <a href="/" class="inline-flex items-center gap-3.5 group" aria-label="ArcheryPro - Beranda">
                    <div
                        class="size-11 rounded-xl bg-primary-content/15 border border-primary-content/20 flex items-center justify-center transition-transform duration-500 group-hover:scale-105">
                        <x-display.icon name="target" class="size-6 text-primary-content" />
                    </div>
                    <div>
                        <span class="font-extrabold text-2xl tracking-tight block leading-none">ArcheryPro</span>
                        <span class="text-[11px] font-medium tracking-wider text-primary-content/70 uppercase">Score
                            Management</span>
                    </div>
                </a>
            </div>

            {{-- Center Hero Copy --}}
            <div class="max-w-lg relative z-10 my-auto py-8">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-content/10 border border-primary-content/15 text-xs font-medium mb-6">
                    <span class="size-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Platform Panahan Digital #1
                </div>

                <h2 class="text-3xl xl:text-4xl font-extrabold leading-tight mb-4 tracking-tight">
                    Presisi dalam Setiap Bidikan Skor.
                </h2>
                <p class="text-primary-content/85 leading-relaxed text-sm xl:text-base">
                    Pencatatan skor panahan modern yang transparan, aman, dan mempermudah analisis perkembangan performa
                    atlet secara real-time.
                </p>

                {{-- Feature List --}}
                <div class="mt-8 space-y-3.5">
                    @foreach (['Pencatatan skor multi-atlet & mode scoring langsung', 'Grafik analitik perkembangan akurasi & statistik panahan', 'Sistem log aktivitas & histori kompetisi yang aman'] as $feature)
                        <div
                            class="flex items-center gap-3 bg-primary-content/5 p-3 rounded-xl border border-primary-content/10">
                            <div
                                class="size-6 rounded-lg bg-primary-content/20 flex items-center justify-center shrink-0">
                                <x-display.icon name="check" class="size-3.5 text-primary-content" />
                            </div>
                            <span
                                class="text-xs xl:text-sm font-medium text-primary-content/95">{{ $feature }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Footer Info --}}
            <div
                class="text-xs text-primary-content/60 relative z-10 border-t border-primary-content/10 pt-4 flex items-center justify-between">
                <span>&copy; {{ date('Y') }} ArcheryPro. All rights reserved.</span>
                <span class="opacity-75">v2.0 Systems</span>
            </div>
        </aside>

        {{-- RIGHT: Form Area --}}
        <main class="lg:col-span-7 xl:col-span-6 flex flex-col justify-between p-4 sm:p-8 lg:p-12 min-h-[100dvh]">

            {{-- Top Bar Mobile Logo --}}
            <div class="lg:hidden flex items-center justify-between mb-6 pt-2">
                <a href="/" class="inline-flex items-center gap-2.5" aria-label="ArcheryPro - Beranda">
                    <div
                        class="size-9 rounded-lg bg-primary text-primary-content flex items-center justify-center shadow-sm">
                        <x-display.icon name="target" class="size-5" />
                    </div>
                    <span class="font-extrabold text-xl text-primary tracking-tight">ArcheryPro</span>
                </a>
            </div>

            {{-- Main Form Card Container --}}
            <div class="w-full max-w-md mx-auto my-auto py-4">
                <div
                    class="card bg-base-100 shadow-sm border border-base-200/80 rounded-2xl overflow-hidden transition-all duration-500">
                    <div class="card-body p-6 sm:p-8">
                        @yield('content')
                    </div>
                </div>
            </div>

            {{-- Mobile Footer Copyright --}}
            <div class="text-center text-xs text-base-content/50 py-4">
                &copy; {{ date('Y') }} ArcheryPro. Presisi & Akurasi Terjamin.
            </div>
        </main>

    </div>

    @stack('scripts')
</body>

</html>
