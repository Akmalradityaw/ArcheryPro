<header class="sticky top-0 z-30 bg-base-100/90 backdrop-blur-md border-b border-base-200 print:hidden">
    <div class="navbar px-4 h-16 min-h-16 gap-2">

        {{-- Mobile: Hamburger Button --}}
        <div class="flex-none lg:hidden">
            <label for="main-drawer" class="btn btn-square btn-ghost btn-sm text-base-content/70 hover:text-base-content"
                aria-label="Buka menu navigasi">
                <x-display.icon name="menu" class="size-5" />
            </label>
        </div>

        {{-- Title / Mobile Brand --}}
        <div class="flex-1 min-w-0">
            {{-- Mobile Logo --}}
            <a href="/" class="flex items-center gap-2 lg:hidden" aria-label="ArcheryPro">
                <div class="size-8 rounded-lg bg-primary flex items-center justify-center text-primary-content">
                    <x-display.icon name="target" class="size-4" />
                </div>
                <span class="font-bold text-base text-base-content tracking-tight">ArcheryPro</span>
            </a>

            {{-- Desktop Breadcrumb / Page Title --}}
            <div class="hidden lg:flex items-center gap-2">
                <h1 class="text-sm font-semibold text-base-content/80 tracking-tight">
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>
        </div>

        {{-- Right: User Menu Dropdown --}}
        @auth
            <div class="flex-none">
                <div class="dropdown dropdown-end">
                    <label tabindex="0" role="button"
                        class="btn btn-ghost btn-sm h-10 px-2 gap-2.5 rounded-xl hover:bg-base-200/60"
                        aria-label="Menu akun pengguna">
                        <div
                            class="size-8 rounded-full bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0 font-bold text-xs uppercase">
                            {{ strtoupper(substr(auth()->user()->username ?? 'U', 0, 1)) }}
                        </div>
                        <div class="hidden sm:flex flex-col items-start text-left leading-tight">
                            <span
                                class="text-xs font-semibold text-base-content max-w-[120px] truncate">{{ auth()->user()->username }}</span>
                            <span class="text-[9px] font-bold uppercase tracking-wider text-base-content/40">
                                {{ method_exists(auth()->user(), 'getRoleNames') ? auth()->user()->getRoleNames()->first() ?? 'user' : 'user' }}
                            </span>
                        </div>
                        <x-display.icon name="chevron-down" class="size-3.5 text-base-content/40" />
                    </label>

                    <ul tabindex="0"
                        class="dropdown-content z-50 mt-2 p-1.5 shadow-xl bg-base-100 border border-base-200 rounded-xl w-56 text-xs space-y-0.5">
                        <li class="px-3 py-2 border-b border-base-200 mb-1">
                            <p class="font-semibold text-base-content truncate">{{ auth()->user()->username }}</p>
                            <p class="text-[10px] text-base-content/50 truncate">
                                {{ auth()->user()->email ?? 'Akun Terverifikasi' }}</p>
                        </li>

                        @if (auth()->user()->hasRole('atlet') && Route::has('atlet.profil.edit'))
                            <li>
                                <a href="{{ route('atlet.profil.edit') }}"
                                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-base-content/80 hover:text-base-content hover:bg-base-200/60 font-medium">
                                    <x-display.icon name="user-circle" class="size-4 shrink-0 text-base-content/60" />
                                    <span>Profil Saya</span>
                                </a>
                            </li>
                        @endif

                        <li>
                            <a href="/"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-base-content/80 hover:text-base-content hover:bg-base-200/60 font-medium">
                                <x-display.icon name="home" class="size-4 shrink-0 text-base-content/60" />
                                <span>Halaman Publik</span>
                            </a>
                        </li>

                        <div class="my-1 border-t border-base-200"></div>

                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="w-full">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-error hover:bg-error/10 font-medium transition-colors text-left">
                                    <x-display.icon name="log-out" class="size-4 shrink-0" />
                                    <span>Keluar</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        @endauth

    </div>
</header>
