<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="light dark">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        @php
            $pageTitle = trim($__env->yieldContent('title', \App\Support\Seo::siteName()));
            $seoData = is_array($seo ?? null) ? $seo : [];
        @endphp
        <title>{{ $seoData['title'] ?? $pageTitle }}</title>
        <x-seo
            :title="$seoData['title'] ?? $pageTitle"
            :description="$seoData['description'] ?? null"
            :keywords="$seoData['keywords'] ?? null"
            :image="$seoData['image'] ?? null"
            :canonical="$seoData['canonical'] ?? null"
            :type="$seoData['type'] ?? 'website'"
            :author="$seoData['author'] ?? 'Timurhan Kaya'"
            :section="$seoData['section'] ?? null"
            :published-time="$seoData['published_time'] ?? null"
            :modified-time="$seoData['modified_time'] ?? null"
            :alternates="$seoData['alternates'] ?? null"
            :noindex="(bool) ($seoData['noindex'] ?? false)"
        />

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=chakra-petch:400,500,600,700|instrument-sans:400,500,600,700|jetbrains-mono:400,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        @stack('head')
    </head>
    <body class="min-h-full bg-white text-zinc-950 antialiased selection:bg-fuchsia-500/25 selection:text-zinc-950 dark:bg-zinc-950 dark:text-zinc-100 dark:selection:text-zinc-100">
        <a
            href="#content"
            class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-xl focus:bg-white/95 focus:px-4 focus:py-3 focus:text-sm focus:font-semibold focus:text-zinc-950 focus:shadow-lg focus:ring-2 focus:ring-cyan-400/70 dark:focus:bg-zinc-950/95 dark:focus:text-white dark:focus:ring-cyan-300/40"
        >
            {{ __('portfolio.ui.skipToContent') }}
        </a>
        <div class="relative isolate min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute inset-0 -z-10">
                <div class="absolute inset-0 bg-[radial-gradient(800px_circle_at_20%_20%,rgba(217,70,239,0.18),transparent_55%),radial-gradient(900px_circle_at_80%_25%,rgba(14,165,233,0.18),transparent_55%),radial-gradient(900px_circle_at_55%_85%,rgba(34,197,94,0.14),transparent_55%)] dark:bg-[radial-gradient(800px_circle_at_20%_20%,rgba(217,70,239,0.22),transparent_55%),radial-gradient(900px_circle_at_80%_25%,rgba(14,165,233,0.20),transparent_55%),radial-gradient(900px_circle_at_55%_85%,rgba(34,197,94,0.16),transparent_55%)]"></div>
                <div class="absolute inset-0 opacity-[0.35] dark:opacity-[0.22] cyber-grid"></div>
                <div class="absolute inset-0 cyber-scanlines"></div>
                <div class="absolute inset-0 cyber-noise"></div>
                <div class="absolute inset-0 cyber-grain"></div>
            </div>

            @include('partials.nav')

            <main id="content" class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
                @yield('content')
            </main>

            @include('partials.footer')
        </div>

        @include('partials.command-palette')

        @livewireScripts
    </body>
</html>
