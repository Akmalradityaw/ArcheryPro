{{-- ============================================================================
     NAVBAR PUBLIC — ArcheryPro
     - Sticky dengan backdrop blur saat scroll
     - Auto switch tombol "Masuk" ↔ "Dashboard" kalau user sudah login
     - Mobile menu dengan icon
     - Konsisten dengan design system (hijau solid / translucency)
 ============================================================================ --}}

@php
    $userRole = auth()->user()?->role ?? auth()->user()?->roles?->first()?->name;
    $dashboardUrl = match ($userRole) {
        'admin' => route('admin.dashboard'),
        'pelatih' => route('pelatih.dashboard'),
        'scoring' => route('scoring.dashboard'),
        default => route('atlet.dashboard'),
    };
@endphp

<header x-data="{ scrolled: false }" @scroll.window="scrolled = window.scrollY > 10"
    :class="scrolled ? 'bg-primary/95 backdrop-blur-md shadow-md' : 'bg-primary shadow-xs'"
    class="sticky top-0 z-40 text-primary-content transition-all duration-200 print:hidden">
    <div class="max-w-6xl mx-auto navbar px-4 min-h-16 gap-2">

        {{-- ============ BRAND ============ --}}
        <div class="flex-1">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group" aria-label="ArcheryPro - Beranda">
                <div
                    class="size-10 rounded-xl bg-primary-content/15 flex items-center justify-center group-hover:bg-primary-content/25 transition-colors duration-300 shrink-0">
                    <x-display.icon name="target" class="size-5" />
                </div>

                <div class="flex flex-col leading-none">
                    <span class="font-bold text-lg tracking-tight group-hover:opacity-90 transition-opacity">
                        ArcheryPro
                    </span>
                    <span
                        class="text-[10px] font-medium uppercase tracking-widest text-primary-content/70 hidden sm:block">
                        Scoring System
                    </span>
                </div>
            </a>
        </div>

        {{-- ============ DESKTOP NAV ============ --}}
        <nav class="flex-none hidden md:flex items-center gap-1.5" aria-label="Navigasi utama">
            <a href="{{ route('home') }}"
                class="btn btn-ghost btn-sm text-primary-content hover:bg-primary-content/10 border-0 normal-case font-medium
                      {{ request()->routeIs('home') ? 'bg-primary-content/20 font-semibold' : '' }}">
                Beranda
            </a>

            <a href="{{ route('events.index') }}"
                class="btn btn-ghost btn-sm text-primary-content hover:bg-primary-content/10 border-0 normal-case font-medium
                      {{ request()->routeIs('events.*') ? 'bg-primary-content/20 font-semibold' : '' }}">
                Hasil Event
            </a>

            {{-- Divider --}}
            <span class="w-px h-5 bg-primary-content/25 mx-1.5" aria-hidden="true"></span>

            {{-- CTA: Login / Dashboard --}}
            @auth
                <a href="{{ $dashboardUrl }}"
                    class="btn btn-sm bg-primary-content text-primary hover:bg-primary-content/90 border-0 gap-2 normal-case font-semibold shadow-xs active:scale-[0.98] transition-transform">
                    <x-display.icon name="grid" class="size-4" />
                    <span>Dashboard</span>
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="btn btn-sm bg-primary-content text-primary hover:bg-primary-content/90 border-0 gap-2 normal-case font-semibold shadow-xs active:scale-[0.98] transition-transform">
                    <x-display.icon name="log-in" class="size-4" />
                    <span>Masuk</span>
                </a>
            @endauth
        </nav>

        {{-- ============ MOBILE MENU ============ --}}
        <div class="flex-none md:hidden" x-data="{ open: false }" @click.outside="open = false">
            <div class="dropdown dropdown-end">
                {{-- Tombol Trigger Menu --}}
                <button type="button" @click="open = !open"
                    class="btn btn-ghost btn-square btn-sm text-primary-content hover:bg-primary-content/15 focus:bg-primary-content/20 border-0 transition-transform active:scale-95"
                    :aria-expanded="open" :aria-label="open ? 'Tutup menu navigasi' : 'Buka menu navigasi'">

                    {{-- Container relatif agar posisi kedua ikon tepat di tengah tanpa melompat --}}
                    <div class="relative size-6 flex items-center justify-center">
                        <x-display.icon name="menu" class="size-6 absolute transition-opacity duration-150"
                            x-show="!open" />
                        <x-display.icon name="x" class="size-6 absolute transition-opacity duration-150"
                            x-show="open" x-cloak />
                    </div>
                </button>

                {{-- Dropdown Menu Items --}}
                <ul x-show="open" x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                    class="menu menu-sm dropdown-content mt-3 z-50 p-2 shadow-xl bg-base-100 text-base-content rounded-xl w-60 border border-base-200"
                    x-cloak>
                    {{-- Section Menu Utama --}}
                    <li class="menu-title px-3 py-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/50">
                            Menu Navigasi
                        </span>
                    </li>

                    <li>
                        <a href="{{ route('home') }}" @click="open = false"
                            class="gap-2.5 py-2.5 rounded-lg transition-colors {{ request()->routeIs('home') ? 'bg-primary text-primary-content font-semibold hover:bg-primary/90' : 'hover:bg-base-200' }}">
                            <x-display.icon name="home" class="size-4" />
                            <span>Beranda</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('events.index') }}" @click="open = false"
                            class="gap-2.5 py-2.5 rounded-lg transition-colors {{ request()->routeIs('events.*') ? 'bg-primary text-primary-content font-semibold hover:bg-primary/90' : 'hover:bg-base-200' }}">
                            <x-display.icon name="calendar" class="size-4" />
                            <span>Hasil Event</span>
                        </a>
                    </li>

                    {{-- Section Akses Akun --}}
                    <li class="menu-title mt-2 px-3 py-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/50">
                            Akses Akun
                        </span>
                    </li>

                    @auth
                        <li>
                            <a href="{{ $dashboardUrl }}" @click="open = false"
                                class="bg-primary text-primary-content hover:bg-primary/90 gap-2.5 py-2.5 font-semibold rounded-lg shadow-xs">
                                <x-display.icon name="grid" class="size-4" />
                                <span>Dashboard</span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('login') }}" @click="open = false"
                                class="bg-primary text-primary-content hover:bg-primary/90 gap-2.5 py-2.5 font-semibold rounded-lg shadow-xs">
                                <x-display.icon name="log-in" class="size-4" />
                                <span>Masuk</span>
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>

    </div>
</header>
