@php
    $workStartYear = 2019;
    $birthDate = \Carbon\CarbonImmutable::create(2002, 4, 12);
    $years = now()->year - $workStartYear;
    $age = $birthDate->diffInYears(\Carbon\CarbonImmutable::now());

    /** @var array<string, mixed> $content */
    $content = is_array($content ?? null) ? $content : [];
    $t = function (mixed $value) use ($years, $age): string {
        $text = is_string($value) ? $value : '';

        return str_replace([':years', ':age'], [(string) $years, (string) $age], $text);
    };
    $homeUrl = \App\Support\Seo::route('home');

    $seo = [
        'title' => $t(data_get($content, 'meta.title')),
        'description' => $t(data_get($content, 'meta.description')),
        'keywords' => $t(data_get($content, 'meta.keywords')),
        'canonical' => $homeUrl,
        'image' => 'og-home.svg',
        'author' => 'Timurhan Kaya',
    ];
@endphp

@extends('layouts.app')

@section('title', $t(data_get($content, 'meta.title')))

@push('head')
    @php
        $homePayload = [
            'heroTitles' => array_values((array) data_get($content, 'hero.titles', [])),
            'codeLines' => array_map(
                $t,
                array_values((array) data_get($content, 'about.code.lines', [])),
            ),
        ];

        $jsonLd = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Person',
                    'name' => $t(data_get($content, 'hero.name')),
                    'url' => $homeUrl,
                    'jobTitle' => $t(data_get($content, 'stats.role_value')),
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressLocality' => 'Istanbul',
                        'addressCountry' => 'TR',
                    ],
                    'sameAs' => [
                        'https://www.linkedin.com/in/timurhan-kaya/',
                        'https://github.com/kayacekovic',
                    ],
                ],
                [
                    '@type' => 'WebSite',
                    'name' => $t(data_get($content, 'meta.title')),
                    'url' => $homeUrl,
                    'inLanguage' => app()->getLocale(),
                    'description' => $t(data_get($content, 'meta.description')),
                ],
                [
                    '@type' => 'WebPage',
                    'name' => $t(data_get($content, 'meta.title')),
                    'url' => $homeUrl,
                    'description' => $t(data_get($content, 'meta.description')),
                    'isPartOf' => [
                        '@type' => 'WebSite',
                        'name' => \App\Support\Seo::siteName(),
                        'url' => $homeUrl,
                    ],
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/json" id="portfolio-home-data">{!! json_encode($homePayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    {{-- Hero (arena card style) --}}
    <header id="home" class="pt-[calc(4rem+env(safe-area-inset-top)+1.25rem)]">
        <div
            class="relative overflow-hidden rounded-[2.5rem] border border-zinc-200/50 bg-white shadow-2xl shadow-zinc-500/10 backdrop-blur-xl hero-arena dark:border-white/10 dark:bg-zinc-950/90 dark:shadow-black/20"
            data-imposter-arena
        >
            {{-- Atmospheric layers --}}
            <div class="pointer-events-none absolute inset-0">
                <canvas id="hero-canvas" class="absolute inset-0 h-full w-full"></canvas>
                <div class="absolute inset-0 hero-stage opacity-[0.40]"></div>
                <div class="absolute inset-0 window-scanline"></div>
                <div class="absolute inset-0 cyber-noise"></div>
                <div class="absolute inset-0 imposter-vignette"></div>
            </div>

            {{-- Toolbar --}}
            <div class="relative border-b border-zinc-200/50 bg-white/40 px-4 py-3 backdrop-blur sm:px-6 dark:border-white/5 dark:bg-zinc-950/60">
                <div class="flex items-center justify-between">
                    @php
                        $kickerRaw = $t(data_get($content, 'hero.kicker'));
                        $kickerParts = array_map('trim', explode(' · ', $kickerRaw));
                    @endphp
                    <div class="inline-flex flex-wrap items-center gap-x-2 gap-y-1 rounded-full border border-zinc-200 bg-white/75 px-4 py-2 font-mono text-[11px] font-semibold tracking-wide text-zinc-700 backdrop-blur dark:border-white/10 dark:bg-zinc-950/40 dark:text-zinc-300">
                        <span class="inline-flex h-2 w-2 shrink-0 rounded-full bg-sky-400 shadow-[0_0_0_4px_rgba(14,165,233,0.16)]"></span>
                        <span class="whitespace-nowrap">{{ $t(data_get($content, 'hero.greeting')) }}</span>
                        <span class="shrink-0 text-zinc-400 dark:text-zinc-500">/</span>
                        <span class="text-zinc-500 dark:text-zinc-400">
                            @if (count($kickerParts) === 2)
                                {{-- Both lines animated on all screen sizes --}}
                                <span class="hero-kicker-rotator" aria-live="polite">
                                    <span class="hero-kicker-rotator__sizer" aria-hidden="true">{{ $kickerParts[1] }}</span>
                                    <span class="hero-kicker-rotator__phrase hero-kicker-rotator__phrase--a">{{ $kickerParts[1] }}</span>
                                    <span class="hero-kicker-rotator__phrase hero-kicker-rotator__phrase--b">{{ $kickerParts[0] }}</span>
                                </span>
                            @else
                                <span>{{ $kickerRaw }}</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="relative p-4 sm:p-6 lg:p-8">
                <div class="grid gap-10 lg:grid-cols-12 lg:items-center">
                    <div class="lg:col-span-12">
                        <div class="reveal" data-reveal>
                            <h1 class="cyber-title text-5xl font-extrabold tracking-tight text-zinc-950 sm:text-6xl lg:text-7xl dark:text-white">
                                <span class="cyber-gradient-text">{{ $t(data_get($content, 'hero.name')) }}</span>
                            </h1>

                            <div class="mt-4 flex min-h-9 items-center text-xl font-semibold text-zinc-800 sm:text-2xl dark:text-zinc-200">
                                <span id="hero-typing-title" class="font-mono">{{ $t(data_get($content, 'hero.title')) }}</span>
                                <span class="ml-2 inline-block h-5 w-px bg-zinc-900/40 dark:bg-white/40" aria-hidden="true"></span>
                            </div>

                            <p class="mt-5 max-w-2xl text-base leading-relaxed text-zinc-600 sm:text-lg dark:text-zinc-300">
                                {{ $t(data_get($content, 'hero.description')) }}
                            </p>

                            <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                                <a
                                    href="{{ \App\Support\Seo::route('games.imposter.index') }}"
                                    class="inline-flex min-h-12 items-center justify-center gap-2 rounded-2xl bg-zinc-950 px-7 py-3.5 text-sm font-bold text-white shadow-sm ring-1 ring-zinc-950/10 transition hover:bg-zinc-900 sm:min-w-[12rem] dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100"
                                >
                                    <span class="cyber-gradient-text">{{ __('games.imposter.cta') }}</span>
                                    <span aria-hidden="true" class="text-white/70 dark:text-zinc-500">→</span>
                                </a>

                                <button
                                    type="button"
                                    class="js-open-portfolio-drawer inline-flex min-h-12 cursor-pointer items-center justify-center gap-2 rounded-2xl bg-zinc-950 px-7 py-3.5 text-sm font-bold text-white shadow-sm ring-1 ring-zinc-950/10 transition hover:bg-zinc-900 sm:min-w-[12rem] dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100"
                                >
                                    {{ $t(data_get($content, 'hero.cta_portfolio')) }}
                                    <span aria-hidden="true" class="text-white/70 dark:text-zinc-500">→</span>
                                </button>

                                <a
                                    href="{{ \App\Support\Seo::route('cv') }}"
                                    target="_blank"
                                    class="inline-flex min-h-12 items-center justify-center gap-2 rounded-2xl border border-zinc-200 bg-white/70 px-7 py-3.5 text-sm font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white sm:min-w-[12rem] dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:hover:bg-zinc-950/55"
                                >
                                    CV (PDF)
                                    <span aria-hidden="true" class="text-zinc-400 dark:text-zinc-500">→</span>
                                </a>

                                <button
                                    type="button"
                                    class="hidden lg:inline-flex items-center justify-center gap-2 rounded-2xl border border-zinc-200 bg-white/70 px-7 py-3.5 text-sm font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-400/60 dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:hover:bg-zinc-950/55 dark:focus-visible:ring-sky-300/30"
                                    data-command-palette-trigger
                                >
                                    <span class="font-mono text-xs">⌘K</span>
                                    <span class="text-zinc-400 dark:text-zinc-500">/</span>
                                    <span class="font-mono text-xs">Ctrl K</span>
                                </button>
                            </div>

<div class="mt-12 grid gap-5 sm:grid-cols-2">
                                <div class="group relative flex items-center gap-4 rounded-4xl border border-zinc-200/50 bg-white/40 p-5 shadow-lg backdrop-blur-xl dark:border-white/10 dark:bg-zinc-900/40">
                                    <div class="relative flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-sky-100 to-indigo-50 text-sky-600 shadow-inner dark:from-sky-500/20 dark:to-indigo-500/10 dark:text-sky-400">
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                    </div>
                                    <div class="relative">
                                        <p class="font-mono text-[10px] font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">{{ $t(data_get($content, 'stats.loc_label')) }}</p>
                                        <p class="mt-1 text-sm font-extrabold text-zinc-950 dark:text-white">{{ $t(data_get($content, 'stats.loc_value')) }}</p>
                                    </div>
                                </div>
                                <div class="group relative flex items-center gap-4 rounded-4xl border border-zinc-200/50 bg-white/40 p-5 shadow-lg backdrop-blur-xl dark:border-white/10 dark:bg-zinc-900/40">
                                    <div class="relative flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-emerald-100 to-teal-50 text-emerald-600 shadow-inner dark:from-emerald-500/20 dark:to-teal-500/10 dark:text-emerald-400">
                                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                                        </svg>
                                    </div>
                                    <div class="relative">
                                        <p class="font-mono text-[10px] font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">{{ $t(data_get($content, 'stats.role_label')) }}</p>
                                        <p class="mt-1 text-sm font-extrabold text-zinc-950 dark:text-white">{{ $t(data_get($content, 'stats.role_value')) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-center">
            <a
                href="#about"
                class="group inline-flex items-center gap-3 rounded-full border border-zinc-200 bg-white/70 px-5 py-3 text-xs font-semibold text-zinc-800 shadow-sm backdrop-blur transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-400/60 dark:border-white/10 dark:bg-zinc-950/35 dark:text-zinc-200 dark:hover:bg-zinc-950/55 dark:focus-visible:ring-sky-300/30"
                aria-label="Scroll to about"
            >
                <span class="font-mono uppercase tracking-[0.22em] text-zinc-500 dark:text-zinc-400">scroll</span>
                <span aria-hidden="true" class="font-mono text-sm text-zinc-900 transition group-hover:translate-y-0.5 dark:text-white">↓</span>
            </a>
        </div>
    </header>

    {{-- About --}}
    <section id="about" class="mt-20 scroll-mt-24 sm:mt-24">
        <div class="max-w-3xl reveal" data-reveal>
            <h2 class="cyber-title text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white sm:text-4xl">{{ $t(data_get($content, 'about.title')) }}</h2>
        </div>

        <div class="mt-12 grid gap-8 lg:grid-cols-12 lg:items-start">
            <div class="lg:col-span-6 reveal" data-reveal>
                <div class="relative overflow-hidden rounded-3xl border border-zinc-200 bg-zinc-950/95 shadow-2xl shadow-zinc-950/10 dark:border-white/10 dark:shadow-black/30" data-tilt>
                    <div class="window-scanline pointer-events-none absolute inset-0 opacity-60"></div>
                    <div class="flex items-center justify-between border-b border-white/10 bg-white/5 px-5 py-4">
                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-red-500/80 shadow-[0_0_10px_rgba(239,68,68,0.25)]"></span>
                            <span class="h-3 w-3 rounded-full bg-amber-500/80 shadow-[0_0_10px_rgba(245,158,11,0.25)]"></span>
                            <span class="h-3 w-3 rounded-full bg-emerald-500/80 shadow-[0_0_10px_rgba(34,197,94,0.25)]"></span>
                        </div>
                        <div class="flex items-center gap-2 text-[10px] font-semibold tracking-wide text-zinc-300/80">
                            <span class="font-mono">{{ $t(data_get($content, 'about.code.file', 'Portfolio.php')) }}</span>
                        </div>
                        <div class="h-6 w-6"></div>
                    </div>

                    <div class="relative p-6">
                        <div id="code-typing-container" class="h-[420px] overflow-y-auto overscroll-contain pr-2 font-mono text-sm leading-relaxed text-zinc-100/90">
                            @foreach ((array) data_get($content, 'about.code.lines', []) as $line)
                                <div class="whitespace-pre-wrap wrap-break-word">{!! trim($line) === '' ? '&nbsp;' : e($t($line)) !!}</div>
                            @endforeach
                        </div>
                        <div class="mt-4 flex items-center justify-between border-t border-white/10 pt-4 text-[10px] font-mono text-zinc-300/70">
                            <span>PHP 8.5</span>
                            <span class="inline-flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full bg-sky-400"></span>
                                <span id="terminal-status-text">System Ready</span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 reveal" data-reveal>
                <div class="space-y-6">
                    @if (trim($t(data_get($content, 'about.lead'))) !== '')
                        <p class="mt-4 max-w-2xl text-base leading-relaxed text-zinc-600 dark:text-zinc-300 sm:text-lg">{{ $t(data_get($content, 'about.lead')) }}</p>
                    @endif
                    <div class="border-l-2 border-sky-400/40 pl-5">
                        <p class="text-[15px] leading-[1.75] text-zinc-600 dark:text-zinc-300">{{ $t(data_get($content, 'about.p1')) }}</p>
                    </div>
                    <div class="border-l-2 border-zinc-200/80 pl-5 dark:border-white/10">
                        <p class="text-[15px] leading-[1.75] text-zinc-600 dark:text-zinc-300">{{ $t(data_get($content, 'about.p2')) }}</p>
                    </div>
                    <div class="border-l-2 border-zinc-200/80 pl-5 dark:border-white/10">
                        <p class="text-[15px] leading-[1.75] text-zinc-600 dark:text-zinc-300">{{ $t(data_get($content, 'about.p3')) }}</p>
                    </div>
                </div>

                <div class="mt-8 rounded-3xl border border-zinc-200/80 bg-linear-to-br from-white/90 to-zinc-50/80 p-6 shadow-sm backdrop-blur dark:border-white/10 dark:from-zinc-900/60 dark:to-zinc-950/50">
                    <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">{{ $t(data_get($content, 'about.interests')) }}</p>
                    <div class="mt-4 flex flex-wrap gap-2.5">
                        @foreach ((array) data_get($content, 'about.hobbies', []) as $hobby)
                            <span class="rounded-xl border border-zinc-200/80 bg-white/80 px-3.5 py-1.5 text-[13px] font-semibold text-zinc-700 shadow-sm dark:border-white/10 dark:bg-zinc-800/50 dark:text-zinc-300">{{ $hobby }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Skills --}}
    <section id="skills" class="mt-20 scroll-mt-24 sm:mt-24">
        <div class="max-w-3xl reveal" data-reveal>
            <h2 class="cyber-title text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $t(data_get($content, 'skills.title')) }}</h2>
        </div>

        <div class="mt-10 grid gap-5 lg:grid-cols-3">
            @foreach ((array) data_get($content, 'skills.groups', []) as $group)
                <div class="rounded-3xl border border-zinc-200 bg-white/75 p-7 backdrop-blur dark:border-white/10 dark:bg-zinc-950/35 reveal" data-reveal data-tilt>
                    <h3 class="text-sm font-semibold text-zinc-950 dark:text-white">{{ $t(data_get($group, 'title')) }}</h3>
                    <div class="mt-6 space-y-4">
                        @foreach ((array) data_get($group, 'items', []) as $item)
                            <div>
                                <div class="flex items-center justify-between text-xs font-semibold text-zinc-600 dark:text-zinc-400">
                                    <span>{{ $t(data_get($item, 'label')) }}</span>
                                    <span>{{ (int) data_get($item, 'value') }}%</span>
                                </div>
                                <div class="mt-2 h-2 w-full rounded-full bg-zinc-200 dark:bg-white/10">
                                    <div class="h-2 rounded-full bg-linear-to-r from-sky-500 to-fuchsia-500" style="width: {{ (int) data_get($item, 'value') }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Experience --}}
    <section id="experience" class="mt-20 scroll-mt-24 sm:mt-24">
        <div class="max-w-3xl reveal" data-reveal>
            <h2 class="cyber-title text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $t(data_get($content, 'experience.title')) }}</h2>
        </div>

        <div class="mt-10 space-y-5">
            @foreach ((array) data_get($content, 'experience.items', []) as $job)
                <div class="relative rounded-3xl border border-zinc-200 bg-white/75 p-7 backdrop-blur dark:border-white/10 dark:bg-zinc-950/35 reveal" data-reveal>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-base font-semibold text-zinc-950 dark:text-white">{{ $t(data_get($job, 'role')) }}</p>
                            <p class="text-sm font-semibold text-sky-600 dark:text-sky-300">{{ $t(data_get($job, 'company')) }}</p>
                            <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-400">{{ $t(data_get($job, 'meta')) }}</p>
                        </div>
                        <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">{{ $t(data_get($job, 'period')) }}</p>
                    </div>
                    <ul class="mt-5 space-y-2 text-sm text-zinc-600 dark:text-zinc-300">
                        @foreach ((array) data_get($job, 'bullets', []) as $bullet)
                            <li class="flex gap-2"><span class="text-emerald-500">•</span><span>{{ $t($bullet) }}</span></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Projects --}}
    {{-- Project Drawer is still rendered but its home section header is removed --}}
    <x-project-drawer
        :sections="data_get($content, 'projects.sections', [])"
        :title="$t(data_get($content, 'projects.title'))"
        :description="$t(data_get($content, 'projects.description'))"
    />

    {{-- Contact --}}
    <section id="contact" class="mt-20 scroll-mt-24 sm:mt-24">
        <div class="relative overflow-hidden rounded-3xl border border-zinc-200 bg-white/75 p-8 backdrop-blur dark:border-white/10 dark:bg-zinc-950/35 sm:p-10 reveal" data-reveal>
            <div class="absolute -right-24 -top-24 h-64 w-64 rounded-full bg-emerald-500/15 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 h-64 w-64 rounded-full bg-sky-500/15 blur-3xl"></div>

            <div class="relative">
                <div class="flex flex-col items-start justify-between gap-8 md:flex-row md:items-center">
                    <div class="max-w-2xl">
                        <h2 class="cyber-title text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $t(data_get($content, 'contact.title')) }}</h2>
                        <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-300">{{ $t(data_get($content, 'contact.description')) }}</p>
                    </div>
                    <a
                        href="mailto:kayacekovic@gmail.com"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-zinc-950 px-7 py-3.5 text-sm font-semibold text-white shadow-sm ring-1 ring-zinc-950/10 transition hover:bg-zinc-900 dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100"
                    >
                        {{ $t(data_get($content, 'contact.cta')) }}
                        <span aria-hidden="true" class="text-white/70 dark:text-zinc-500">→</span>
                    </a>
                </div>

                <div class="mt-10 grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-zinc-200 bg-white/60 px-5 py-4 dark:border-white/10 dark:bg-zinc-950/35" data-tilt>
                        <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">{{ $t(data_get($content, 'contact.email_label')) }}</p>
                        <a class="mt-2 inline-block text-sm font-semibold text-zinc-950 hover:underline dark:text-white" href="mailto:kayacekovic@gmail.com">kayacekovic@gmail.com</a>
                    </div>
                    <div class="rounded-2xl border border-zinc-200 bg-white/60 px-5 py-4 dark:border-white/10 dark:bg-zinc-950/35" data-tilt>
                        <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">{{ $t(data_get($content, 'contact.linkedin_label')) }}</p>
                        <a class="mt-2 inline-block text-sm font-semibold text-zinc-950 hover:underline dark:text-white" target="_blank" rel="noopener noreferrer" href="https://www.linkedin.com/in/timurhan-kaya/">/in/timurhan-kaya</a>
                    </div>
                    <div class="rounded-2xl border border-zinc-200 bg-white/60 px-5 py-4 dark:border-white/10 dark:bg-zinc-950/35" data-tilt>
                        <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">{{ $t(data_get($content, 'contact.github_label')) }}</p>
                        <a class="mt-2 inline-block text-sm font-semibold text-zinc-950 hover:underline dark:text-white" target="_blank" rel="noopener noreferrer" href="https://github.com/kayacekovic">@kayacekovic</a>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
