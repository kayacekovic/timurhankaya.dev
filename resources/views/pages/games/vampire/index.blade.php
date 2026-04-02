@php
    $gameUrl = \App\Support\Seo::route('games.vampire.index');
    $seo = [
        'title' => __('vampire.title').' — '.__('portfolio.nav.games'),
        'description' => __('vampire.subtitle'),
        'canonical' => $gameUrl,
        'image' => 'og-vampire.svg',
    ];
@endphp

@extends('layouts.app')

@section('title', __('vampire.title').' — '.__('portfolio.nav.games'))

@push('head')
    @php
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebPage',
                    'name' => __('vampire.title'),
                    'url' => $gameUrl,
                    'description' => __('vampire.subtitle'),
                ],
                [
                    '@type' => 'Game',
                    'name' => __('vampire.title'),
                    'url' => $gameUrl,
                    'description' => __('vampire.subtitle'),
                    'playMode' => 'MultiPlayer',
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <section class="pb-16 pt-[calc(4rem+env(safe-area-inset-top)+2rem)]">
        <div class="max-w-3xl reveal" data-reveal>
            <div class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white/70 px-4 py-2 font-mono text-[11px] font-semibold tracking-wide text-zinc-700 backdrop-blur dark:border-white/10 dark:bg-zinc-950/35 dark:text-zinc-300">
                <span class="inline-flex h-2 w-2 rounded-full bg-red-400 shadow-[0_0_0_4px_rgba(248,113,113,0.16)]"></span>
                {{ __('vampire.kicker') }}
            </div>
            <h1 class="cyber-title mt-6 text-4xl font-extrabold tracking-tight text-zinc-950 sm:text-5xl dark:text-white">{{ __('vampire.title') }}</h1>
            <p class="mt-4 text-base leading-relaxed text-zinc-600 dark:text-zinc-300">
                {{ __('vampire.subtitle') }}
            </p>
        </div>

        <livewire:games.vampire.index />
    </section>
@endsection
