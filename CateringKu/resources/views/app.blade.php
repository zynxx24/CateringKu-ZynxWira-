<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- SEO & Open Graph Meta Tags for WhatsApp, Telegram, Social Sharing --}}
        <meta name="description" content="{{ $page['props']['meta']['description'] ?? 'CateringKu: bandingkan menu vendor katering dan pesan untuk jumlah tamu acara Anda.' }}">
        <meta name="keywords" content="katering, catering, pesan katering, katering online, nasi kotak, prasmanan, snack box, katering jakarta, katering wedding, tumpeng, dessert, pernikahan, #CateringKu, #KateringOnline, #PesanKatering">
        <meta name="author" content="CateringKu">
        <meta name="robots" content="index, follow">
        <meta name="theme-color" content="#B8461F">

        {{-- Open Graph (Facebook, WhatsApp, Telegram, LINE) --}}
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="{{ $page['props']['meta']['title'] ?? config('app.name') . ' | Pesan katering untuk acara Anda' }}">
        <meta property="og:description" content="{{ $page['props']['meta']['description'] ?? 'Bandingkan menu vendor katering, isi jumlah tamu, dan pesan untuk tanggal acara Anda.' }}">
        <meta property="og:image" content="{{ asset('images/og-banner.jpg') }}">
        <meta property="og:image:type" content="image/jpeg">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="CateringKu, platform pemesanan katering online">
        <meta property="og:site_name" content="CateringKu">
        <meta property="og:locale" content="id_ID">

        {{-- Twitter Card --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $page['props']['meta']['title'] ?? config('app.name') . ' | Pesan katering online' }}">
        <meta name="twitter:description" content="{{ $page['props']['meta']['description'] ?? 'Bandingkan menu vendor katering dan pesan untuk acara Anda.' }}">
        <meta name="twitter:image" content="{{ asset('images/og-banner.jpg') }}">

        {{-- Inline script to detect system dark mode preference --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';
                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        <style>
            html { background-color: oklch(1 0 0); }
            html.dark { background-color: oklch(0.145 0 0); }
        </style>

        {{-- Favicon --}}
        <link rel="icon" href="/images/logo.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/images/logo.svg">

        {{-- Fonts: Poppins for headings + Inter for body --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800|inter:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.ts'])
        <x-inertia::head>
            <title>{{ config('app.name', 'CateringKu') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
