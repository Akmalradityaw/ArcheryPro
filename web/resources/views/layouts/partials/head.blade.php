<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- SEO --}}
<meta name="description" content="@yield('meta_description', 'Sistem Data Skor Panahan ArcheryPro — transparan, tercatat, terpantau.')">
<meta name="theme-color" content="#2E7D32">

{{-- Favicon inline (SVG) --}}
<link rel="icon" type="image/svg+xml"
    href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232E7D32' stroke-width='2'%3E%3Ccircle cx='12' cy='12' r='9'/%3E%3Ccircle cx='12' cy='12' r='4'/%3E%3Ccircle cx='12' cy='12' r='1' fill='%232E7D32'/%3E%3C/svg%3E">

<title>@yield('title', 'ArcheryPro')</title>

{{-- Font premium (skill: no Inter/Roboto/Arial) --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">

{{-- Asset --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])
