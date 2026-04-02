@php
    $gamesUrl = \App\Support\Seo::route('games.index');
    $imposterUrl = \App\Support\Seo::route('games.imposter.index');
    $vampireUrl = \App\Support\Seo::route('games.vampire.index');
    $seo = [
        'title' => __('games.metaTitle'),
        'description' => __('games.metaDescription'),
        'canonical' => $gamesUrl,
        'image' => 'og-games.svg',
    ];
@endphp

@extends('layouts.app')

@section('title', __('games.metaTitle'))

@push('head')
    @php
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'CollectionPage',
                    'name' => __('games.metaTitle'),
                    'url' => $gamesUrl,
                    'description' => __('games.metaDescription'),
                ],
                [
                    '@type' => 'ItemList',
                    'name' => __('portfolio.nav.games'),
                    'itemListElement' => [
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => __('imposter.title'),
                            'url' => $imposterUrl,
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => __('vampire.title'),
                            'url' => $vampireUrl,
                        ],
                    ],
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <section class="relative pb-16 pt-[calc(4rem+env(safe-area-inset-top)+2rem)]">
        <div class="relative z-10">
            <div class="max-w-3xl reveal" data-reveal>
                <div class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white/70 px-4 py-2 font-mono text-[11px] font-semibold tracking-wide text-zinc-700 backdrop-blur dark:border-white/10 dark:bg-zinc-950/35 dark:text-zinc-300">
                    <span class="inline-flex h-2 w-2 rounded-full bg-sky-500 shadow-[0_0_0_4px_rgba(14,165,233,0.18)]"></span>
                    {{ __('games.kicker') }}
                </div>
                <h1 class="cyber-title mt-6 text-4xl font-extrabold tracking-tight text-zinc-950 sm:text-5xl dark:text-white">{{ __('portfolio.nav.games') }}</h1>
                <p class="mt-4 text-base leading-relaxed text-zinc-600 sm:text-lg dark:text-zinc-300">
                    {{ __('games.lead') }}
                </p>
            </div>

            <div class="mt-10 grid gap-5 lg:grid-cols-2">
                <a
                    href="{{ $imposterUrl }}"
                    class="group relative overflow-hidden rounded-3xl border border-zinc-200 bg-white/75 p-7 shadow-sm backdrop-blur focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60 dark:border-white/10 dark:bg-zinc-950/35 dark:focus-visible:ring-cyan-300/30 active:bg-white/75 active:dark:bg-zinc-950/35 reveal"
                    data-reveal
                    data-tilt-disabled
                >
                    <div class="relative">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ __('imposter.title') }}</h2>
                                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ __('games.imposter.cardDescription') }}</p>
                            </div>
                            <span class="rounded-full bg-sky-500/10 px-3 py-1 text-xs font-semibold text-sky-700 dark:text-sky-300">{{ __('games.imposter.badge') }}</span>
                        </div>
                        <div class="mt-6 flex flex-wrap gap-2 text-xs">
                            <span class="rounded-full border border-zinc-200 bg-white/70 px-3 py-1 font-semibold text-zinc-700 dark:border-white/10 dark:bg-zinc-950/35 dark:text-zinc-300">{{ __('games.imposter.chip.roomCodes') }}</span>
                            <span class="rounded-full border border-zinc-200 bg-white/70 px-3 py-1 font-semibold text-zinc-700 dark:border-white/10 dark:bg-zinc-950/35 dark:text-zinc-300">{{ __('games.imposter.chip.hiddenRoles') }}</span>
                            <span class="rounded-full border border-zinc-200 bg-white/70 px-3 py-1 font-semibold text-zinc-700 dark:border-white/10 dark:bg-zinc-950/35 dark:text-zinc-300">{{ __('games.imposter.chip.voting') }}</span>
                        </div>
                        <div class="mt-7 inline-flex items-center gap-2 text-sm font-semibold text-zinc-950 underline-offset-4 dark:text-white">
                            {{ __('games.imposter.cta') }}
                            <span aria-hidden="true">→</span>
                        </div>
                    </div>
                </a>

                <a
                    href="{{ $vampireUrl }}"
                    class="group relative overflow-hidden rounded-3xl border border-zinc-200 bg-white/75 p-7 shadow-sm backdrop-blur focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:border-white/10 dark:bg-zinc-950/35 dark:focus-visible:ring-red-300/30 active:bg-white/75 active:dark:bg-zinc-950/35 reveal"
                    data-reveal
                    data-tilt-disabled
                >
                    <div class="relative">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-semibold text-zinc-950 dark:text-white">{{ __('vampire.title') }}</h2>
                                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ __('games.vampire.cardDescription') }}</p>
                            </div>
                            <span class="rounded-full bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-700 dark:text-red-300">{{ __('games.vampire.badge') }}</span>
                        </div>
                        <div class="mt-6 flex flex-wrap gap-2 text-xs">
                            <span class="rounded-full border border-zinc-200 bg-white/70 px-3 py-1 font-semibold text-zinc-700 dark:border-white/10 dark:bg-zinc-950/35 dark:text-zinc-300">{{ __('games.vampire.chip.roomCodes') }}</span>
                            <span class="rounded-full border border-zinc-200 bg-white/70 px-3 py-1 font-semibold text-zinc-700 dark:border-white/10 dark:bg-zinc-950/35 dark:text-zinc-300">{{ __('games.vampire.chip.hiddenRoles') }}</span>
                            <span class="rounded-full border border-zinc-200 bg-white/70 px-3 py-1 font-semibold text-zinc-700 dark:border-white/10 dark:bg-zinc-950/35 dark:text-zinc-300">{{ __('games.vampire.chip.nightDay') }}</span>
                            <span class="rounded-full border border-zinc-200 bg-white/70 px-3 py-1 font-semibold text-zinc-700 dark:border-white/10 dark:bg-zinc-950/35 dark:text-zinc-300">{{ __('games.vampire.chip.voting') }}</span>
                        </div>
                        <div class="mt-7 inline-flex items-center gap-2 text-sm font-semibold text-zinc-950 underline-offset-4 dark:text-white">
                            {{ __('games.vampire.cta') }}
                            <span aria-hidden="true">→</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>
@endsection
