@php
    $years = now()->year - 2019;

    /** @var array<string, mixed> $content */
    $content = is_array($content ?? null) ? $content : [];
    $t = function (mixed $value) use ($years): string {
        $text = is_string($value) ? $value : '';

        return str_replace([':years'], [(string) $years], $text);
    };

    $pitchPlain = app()->getLocale() === 'tr'
        ? "Selam Timur,\n\nBen şunu geliştiriyorum: <ne>\nHedef: <etki>\nZaman: <tarih>\nKısıtlar: <stack/bütçe>\n\nİlk sürümü çıkaralım mı?"
        : "Hey Timur,\n\nI’m building: <what>\nGoal: <impact>\nTimeline: <date>\nConstraints: <stack/budget>\n\nCan we build a first version?";
    $pitchForAttribute = str_replace("\n", '\\n', $pitchPlain);
    $contactUrl = \App\Support\Seo::route('contact');
    $homeUrl = \App\Support\Seo::route('home');
    $gamesUrl = \App\Support\Seo::route('games.index');

    $seo = [
        'title' => $t(data_get($content, 'contact.title')).' — '.$t(data_get($content, 'meta.title')),
        'description' => $t(data_get($content, 'meta.description')),
        'keywords' => $t(data_get($content, 'meta.keywords')),
        'canonical' => $contactUrl,
        'image' => 'og-contact.svg',
        'author' => 'Timurhan Kaya',
    ];
@endphp

@extends('layouts.app')

@section('title', $t(data_get($content, 'contact.title')).' — '.$t(data_get($content, 'meta.title')))

@push('head')
    @php
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'ContactPage',
                    'name' => $t(data_get($content, 'contact.title')),
                    'url' => $contactUrl,
                    'description' => $t(data_get($content, 'contact.description')),
                ],
                [
                    '@type' => 'Person',
                    'name' => 'Timurhan Kaya',
                    'url' => $homeUrl,
                    'email' => 'mailto:kayacekovic@gmail.com',
                    'sameAs' => [
                        'https://www.linkedin.com/in/timurhan-kaya/',
                        'https://github.com/kayacekovic',
                    ],
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <section class="pt-20">
        <div class="grid gap-10 lg:grid-cols-12 lg:items-start">
            <div class="lg:col-span-6">
                <div class="reveal" data-reveal>
                    <div class="inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white/70 px-4 py-2 font-mono text-[11px] font-semibold tracking-wide text-zinc-700 backdrop-blur dark:border-white/10 dark:bg-zinc-950/35 dark:text-zinc-300">
                        <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400 shadow-[0_0_0_4px_rgba(34,197,94,0.14)]"></span>
                        {{ $t(data_get($content, 'contact.title')) }}
                    </div>

                    <h1 class="cyber-title mt-6 text-5xl font-extrabold tracking-tight text-zinc-950 sm:text-6xl dark:text-white">
                        <span class="cyber-gradient-text">{{ $t(data_get($content, 'contact.title')) }}</span>
                    </h1>
                    <p class="mt-5 max-w-xl text-base leading-relaxed text-zinc-600 sm:text-lg dark:text-zinc-300">
                        {{ $t(data_get($content, 'contact.description')) }}
                    </p>
                </div>

                <div class="mt-10 grid gap-4 reveal" data-reveal>
                    <div class="rounded-3xl border border-zinc-200 bg-white/75 p-6 backdrop-blur dark:border-white/10 dark:bg-zinc-950/35" data-tilt>
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">{{ $t(data_get($content, 'contact.email_label')) }}</p>
                                <a class="mt-2 inline-block text-sm font-semibold text-zinc-950 hover:underline dark:text-white" href="mailto:kayacekovic@gmail.com">kayacekovic@gmail.com</a>
                            </div>
                            <button
                                type="button"
                                class="rounded-2xl border border-zinc-200 bg-white/70 px-4 py-2 text-xs font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60 dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:hover:bg-zinc-950/55 dark:focus-visible:ring-cyan-300/30"
                                data-copy-text="kayacekovic@gmail.com"
                                data-copy-label="{{ __('portfolio.ui.copy') }}"
                                data-copy-label-success="{{ __('portfolio.ui.copied') }}"
                            >
                                {{ __('portfolio.ui.copy') }}
                            </button>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-zinc-200 bg-white/75 p-6 backdrop-blur dark:border-white/10 dark:bg-zinc-950/35" data-tilt>
                        <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">{{ __('portfolio.ui.social') }}</p>
                        <div class="mt-3 space-y-2 text-sm">
                            <a class="block font-semibold text-zinc-700 hover:text-zinc-950 hover:underline dark:text-zinc-300 dark:hover:text-white" target="_blank" rel="noopener noreferrer" href="https://www.linkedin.com/in/timurhan-kaya/">
                                {{ $t(data_get($content, 'contact.linkedin_label')) }} · /in/timurhan-kaya
                            </a>
                            <a class="block font-semibold text-zinc-700 hover:text-zinc-950 hover:underline dark:text-zinc-300 dark:hover:text-white" target="_blank" rel="noopener noreferrer" href="https://github.com/kayacekovic">
                                {{ $t(data_get($content, 'contact.github_label')) }} · @kayacekovic
                            </a>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row">
                        <a
                            href="{{ $homeUrl }}#portfolio"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-2xl border border-zinc-200 bg-white/70 px-6 py-3 text-sm font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:hover:bg-zinc-950/55"
                        >
                            {{ __('portfolio.nav.portfolio') }}
                        </a>
                        <a
                            href="{{ $gamesUrl }}"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-2xl bg-zinc-950 px-6 py-3 text-sm font-semibold text-white shadow-sm ring-1 ring-zinc-950/10 transition hover:bg-zinc-900 dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100"
                        >
                            {{ __('portfolio.nav.games') }}
                            <span aria-hidden="true" class="text-white/70 dark:text-zinc-500">→</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6">
                <div class="reveal" data-reveal>
                    <div class="relative overflow-hidden rounded-3xl border border-zinc-200 bg-white/75 p-8 shadow-sm backdrop-blur dark:border-white/10 dark:bg-zinc-950/35 sm:p-10" data-tilt>
                        <div class="absolute -right-24 -top-24 h-64 w-64 rounded-full bg-fuchsia-500/12 blur-3xl"></div>
                        <div class="absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-cyan-400/14 blur-3xl"></div>

                        <div class="relative">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.22em] text-zinc-600 dark:text-zinc-400">{{ __('portfolio.ui.pitchTemplate') }}</p>
                                    <p class="mt-1 text-lg font-semibold text-zinc-950 dark:text-white">{{ __('portfolio.ui.pitchSubtitle') }}</p>
                                </div>
                                <button
                                    type="button"
                                    class="rounded-2xl border border-zinc-200 bg-white/70 px-4 py-2 text-xs font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60 dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:hover:bg-zinc-950/55 dark:focus-visible:ring-cyan-300/30"
                                    data-copy-text="{{ $pitchForAttribute }}"
                                    data-copy-label="{{ __('portfolio.ui.copy') }}"
                                    data-copy-label-success="{{ __('portfolio.ui.copied') }}"
                                >
                                    {{ __('portfolio.ui.copy') }}
                                </button>
                            </div>

                            <div class="mt-6 rounded-2xl bg-zinc-950 p-5 text-zinc-100 shadow-sm dark:bg-white dark:text-zinc-950">
                                <pre class="overflow-x-auto text-xs leading-relaxed"><code>{{ $pitchPlain }}</code></pre>
                            </div>

                            <p class="mt-5 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ __('portfolio.ui.pitchHelp') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
