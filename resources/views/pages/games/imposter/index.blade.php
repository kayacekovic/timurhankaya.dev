@php
    $gameUrl = \App\Support\Seo::route('games.imposter.index');
    $seo = [
        'title' => __('imposter.title').' — '.__('portfolio.nav.games'),
        'description' => __('imposter.subtitle'),
        'canonical' => $gameUrl,
        'image' => 'og-imposter.svg',
    ];
@endphp

@extends('layouts.app')

@section('title', __('imposter.title').' — '.__('portfolio.nav.games'))

@push('head')
    @php
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebPage',
                    'name' => __('imposter.title'),
                    'url' => $gameUrl,
                    'description' => __('imposter.subtitle'),
                ],
                [
                    '@type' => 'Game',
                    'name' => __('imposter.title'),
                    'url' => $gameUrl,
                    'description' => __('imposter.subtitle'),
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
                <span class="inline-flex h-2 w-2 rounded-full bg-cyan-400 shadow-[0_0_0_4px_rgba(34,211,238,0.16)]"></span>
                {{ __('imposter.kicker') }}
            </div>
            <h1 class="cyber-title mt-6 text-4xl font-extrabold tracking-tight text-zinc-950 sm:text-5xl dark:text-white">{{ __('imposter.title') }}</h1>
            <p class="mt-4 text-base leading-relaxed text-zinc-600 dark:text-zinc-300">
                {{ __('imposter.subtitle') }}
            </p>
        </div>

        <livewire:games.imposter.index />
    </section>
@endsection
