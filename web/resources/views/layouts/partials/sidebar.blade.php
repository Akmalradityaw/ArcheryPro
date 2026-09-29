{{-- Overlay untuk mobile --}}
<label for="main-drawer" class="drawer-overlay" aria-label="Tutup menu"></label>

<aside
    class="w-64 min-h-full bg-base-100 text-base-content border-r border-base-200 flex flex-col transition-all duration-200 select-none">

    {{-- Brand Header --}}
    <div class="h-16 px-5 border-b border-base-200 flex items-center justify-between">
        <a href="/" class="flex items-center gap-3 group" aria-label="ArcheryPro">
            <div
                class="size-9 rounded-xl bg-primary flex items-center justify-center text-primary-content shadow-sm shadow-primary/20 transition-transform group-hover:scale-105">
                <x-display.icon name="target" class="size-5" />
            </div>
            <div class="flex flex-col">
                <span class="font-bold text-base tracking-tight text-base-content leading-none">ArcheryPro</span>
                <span
                    class="text-[10px] text-base-content/50 font-medium tracking-wider uppercase mt-0.5">Management</span>
            </div>
        </a>
    </div>

    {{-- Navigasi Menu --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6" aria-label="Menu utama">

        @php
            $user = auth()->user();
            $isAdmin = $user?->hasRole('admin') ?? false;
            $isPelatih = $user?->hasRole('pelatih') ?? false;
            $isScoring = $user?->hasRole('scoring') ?? false;
            $isAtlet = $user?->hasRole('atlet') ?? false;

            // Helper class untuk menu link agar konsisten
            $navClass = fn(
                $active,
            ) => 'flex items-center gap-3 px-3 py-2.5 rounded-lg text-xs font-semibold transition-all duration-150 ' .
                ($active
                    ? 'bg-primary/10 text-primary shadow-xs font-bold'
                    : 'text-base-content/70 hover:text-base-content hover:bg-base-200/60');
        @endphp

        {{-- ============ ADMIN ============ --}}
        @if ($isAdmin)
            <div>
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-base-content/40">Utama</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                            class="{{ $navClass(request()->routeIs('admin.dashboard')) }}">
                            <x-display.icon name="grid" class="size-4 shrink-0" />
                            <span>Dashboard</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-base-content/40">Manajemen</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.users.index') }}"
                            class="{{ $navClass(request()->routeIs('admin.users.*')) }}">
                            <x-display.icon name="users" class="size-4 shrink-0" />
                            <span>Users</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.atlet.index') }}"
                            class="{{ $navClass(request()->routeIs('admin.atlet.*')) }}">
                            <x-display.icon name="user" class="size-4 shrink-0" />
                            <span>Atlet</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.sesi-latihan.index') }}"
                            class="{{ $navClass(request()->routeIs('admin.sesi-latihan.*')) }}">
                            <x-display.icon name="calendar" class="size-4 shrink-0" />
                            <span>Jadwal Latihan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.sekolah.index') }}"
                            class="{{ $navClass(request()->routeIs('admin.sekolah.*')) }}">
                            <x-display.icon name="academic" class="size-4 shrink-0" />
                            <span>Sekolah</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.kategori.index') }}"
                            class="{{ $navClass(request()->routeIs('admin.kategori.*')) }}">
                            <x-display.icon name="tag" class="size-4 shrink-0" />
                            <span>Kategori</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-base-content/40">Sistem</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('admin.pengaturan.index') }}"
                            class="{{ $navClass(request()->routeIs('admin.pengaturan.*')) }}">
                            <x-display.icon name="cog" class="size-4 shrink-0" />
                            <span>Pengaturan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.backup.index') }}"
                            class="{{ $navClass(request()->routeIs('admin.backup.*')) }}">
                            <x-display.icon name="download" class="size-4 shrink-0" />
                            <span>Backup</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.log-aktivitas.index') }}"
                            class="{{ $navClass(request()->routeIs('admin.log-aktivitas.*')) }}">
                            <x-display.icon name="clipboard" class="size-4 shrink-0" />
                            <span>Log Aktivitas</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endif

        {{-- ============ PELATIH ============ --}}
        @if ($isPelatih)
            <div>
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-base-content/40">Pelatih</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('pelatih.dashboard') }}"
                            class="{{ $navClass(request()->routeIs('pelatih.dashboard')) }}">
                            <x-display.icon name="grid" class="size-4 shrink-0" />
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pelatih.atlet.index') }}"
                            class="{{ $navClass(request()->routeIs('pelatih.atlet.*')) }}">
                            <x-display.icon name="user" class="size-4 shrink-0" />
                            <span>Atlet</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pelatih.analisis.index') }}"
                            class="{{ $navClass(request()->routeIs('pelatih.analisis.*')) }}">
                            <x-display.icon name="chart" class="size-4 shrink-0" />
                            <span>Analisis</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pelatih.ekspor.index') }}"
                            class="{{ $navClass(request()->routeIs('pelatih.ekspor.*')) }}">
                            <x-display.icon name="download-doc" class="size-4 shrink-0" />
                            <span>Ekspor</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pelatih.sesi-latihan.index') }}"
                            class="{{ $navClass(request()->routeIs('pelatih.sesi-latihan.*')) }}">
                            <x-display.icon name="calendar" class="size-4 shrink-0" />
                            <span>Jadwal Latihan</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pelatih.skor.create') }}"
                            class="{{ $navClass(request()->routeIs('pelatih.skor.*')) }}">
                            <x-display.icon name="target" class="size-4 shrink-0" />
                            <span>Input Skor</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endif

        {{-- ============ SCORING ============ --}}
        @if ($isScoring)
            <div>
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-base-content/40">Scoring</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('scoring.dashboard') }}"
                            class="{{ $navClass(request()->routeIs('scoring.dashboard')) }}">
                            <x-display.icon name="grid" class="size-4 shrink-0" />
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('scoring.input.index') }}"
                            class="{{ $navClass(request()->routeIs('scoring.input.*')) }}">
                            <x-display.icon name="plus" class="size-4 shrink-0" />
                            <span>Input Skor</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('scoring.riwayat.index') }}"
                            class="{{ $navClass(request()->routeIs('scoring.riwayat.*')) }}">
                            <x-display.icon name="clock" class="size-4 shrink-0" />
                            <span>Riwayat</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endif

        {{-- ============ ATLET ============ --}}
        @if ($isAtlet)
            <div>
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-wider text-base-content/40">Atlet</p>
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('atlet.dashboard') }}"
                            class="{{ $navClass(request()->routeIs('atlet.dashboard')) }}">
                            <x-display.icon name="grid" class="size-4 shrink-0" />
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('atlet.riwayat.index') }}"
                            class="{{ $navClass(request()->routeIs('atlet.riwayat.*')) }}">
                            <x-display.icon name="clock" class="size-4 shrink-0" />
                            <span>Riwayat</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('atlet.grafik.index') }}"
                            class="{{ $navClass(request()->routeIs('atlet.grafik.*')) }}">
                            <x-display.icon name="chart-line" class="size-4 shrink-0" />
                            <span>Grafik Performa</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('atlet.profil.edit') }}"
                            class="{{ $navClass(request()->routeIs('atlet.profil.*')) }}">
                            <x-display.icon name="user" class="size-4 shrink-0" />
                            <span>Profil</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('atlet.ekspor.index') }}"
                            class="{{ $navClass(request()->routeIs('atlet.ekspor.*')) }}">
                            <x-display.icon name="download-doc" class="size-4 shrink-0" />
                            <span>Ekspor Data</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endif

    </nav>

    {{-- User Footer --}}
    @auth
        <div class="p-3 border-t border-base-200 bg-base-200/30">
            <div class="flex items-center gap-3 px-2 py-1.5 rounded-lg">
                <div
                    class="size-9 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-xs uppercase">
                    {{ strtoupper(substr(auth()->user()->username ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0 leading-tight">
                    <p class="text-xs font-semibold text-base-content truncate">{{ auth()->user()->username }}</p>
                    <p class="text-[10px] font-medium text-base-content/50 uppercase tracking-wider truncate mt-0.5">
                        {{ method_exists(auth()->user(), 'getRoleNames') ? auth()->user()->getRoleNames()->first() ?? 'user' : 'user' }}
                    </p>
                </div>
            </div>
        </div>
    @endauth

</aside>
