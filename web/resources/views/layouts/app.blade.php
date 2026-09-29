<!DOCTYPE html>
<html lang="id" data-theme="archery" class="scroll-smooth">

<head>
    @include('layouts.partials.head')
    @stack('styles')
</head>

<body class="bg-base-200 min-h-[100dvh] antialiased">

    {{-- Skip link (aksesibilitas) --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50
              focus:btn focus:btn-primary focus:btn-sm">
        Lewati ke konten utama
    </a>

    <div class="drawer lg:drawer-open">
        <input id="main-drawer" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex flex-col min-h-[100dvh]">
            @include('layouts.partials.navbar')

            {{-- Alert global --}}
            <div class="px-4 lg:px-6 pt-4 print:hidden">
                @include('layouts.partials.alert')
            </div>

            <main id="main-content" class="p-4 lg:p-6 flex-1">
                @yield('content')
            </main>

            @include('layouts.partials.footer')
        </div>

        <div class="drawer-side z-40 print:hidden">
            @include('layouts.partials.sidebar')
        </div>
    </div>

    {{-- Modal & script stack --}}
    @stack('modals')
    @stack('scripts')
</body>

</html>
