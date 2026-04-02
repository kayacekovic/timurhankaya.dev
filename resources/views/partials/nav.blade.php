@php
    $homeUrl = \App\Support\Seo::route('home');
    $gamesUrl = \App\Support\Seo::route('games.index');
    $imposterUrl = \App\Support\Seo::route('games.imposter.index');
    $vampireUrl = \App\Support\Seo::route('games.vampire.index');
    $productsUrl = \Illuminate\Support\Facades\Route::has('products.index')
        ? \App\Support\Seo::route('products.index')
        : $homeUrl.'#products';
    $localeToggleUrl = request()->fullUrlWithQuery(['lang' => app()->getLocale() === 'tr' ? 'en' : 'tr']);
@endphp

<header class="fixed top-0 z-[100] w-full" style="transform: translateZ(0); -webkit-transform: translateZ(0);">
    <div class="border-b border-zinc-200/60 bg-white/70 backdrop-blur-xl dark:border-white/10 dark:bg-zinc-950/45">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ $homeUrl }}" class="group inline-flex items-center gap-3" aria-label="Home">
                <span class="relative inline-flex h-9 w-9 items-center justify-center rounded-xl bg-zinc-950 text-white shadow-sm ring-1 ring-zinc-950/10 dark:bg-white dark:text-zinc-950 dark:ring-white/10">
                    <span class="font-mono text-xs font-semibold tracking-[0.18em]">TK</span>
                    <span class="pointer-events-none absolute -inset-px rounded-xl opacity-0 ring-2 ring-cyan-400/40 transition group-hover:opacity-100"></span>
                </span>
                <span class="hidden sm:block">
                    <span class="block text-sm font-semibold tracking-tight text-zinc-950 dark:text-white">Timurhan Kaya</span>
                    <span class="block text-xs text-zinc-600 dark:text-zinc-400">{{ __('portfolio.nav.subtitle') }}</span>
                </span>
            </a>

            <nav class="hidden items-center gap-6 md:flex h-full" aria-label="Primary">
                <a
                    class="text-sm font-semibold text-zinc-700 underline-offset-8 transition hover:text-zinc-950 hover:underline dark:text-zinc-300 dark:hover:text-white {{ request()->routeIs('home') ? 'text-zinc-950 dark:text-white' : '' }}"
                    href="{{ $homeUrl }}"
                >
                    {{ __('portfolio.nav.home') }}
                </a>
                
                <div class="group relative flex h-full items-center">
                    <a href="{{ $homeUrl }}#about" class="cursor-pointer text-sm font-semibold text-zinc-700 underline-offset-8 transition hover:text-zinc-950 hover:underline dark:text-zinc-300 dark:hover:text-white">
                        {{ __('portfolio.nav.about') }}
                    </a>
                    <div class="absolute left-1/2 top-full -translate-x-1/2 w-48 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 overflow-hidden rounded-b-2xl border border-t-0 border-zinc-200 bg-white/95 shadow-2xl shadow-zinc-950/10 backdrop-blur-xl dark:border-white/10 dark:bg-zinc-950/90 dark:shadow-black/30">
                        <div class="grid gap-1 p-2">
                            <a class="rounded-xl px-3 py-2 text-sm font-semibold text-zinc-900 hover:bg-zinc-950/5 dark:text-white dark:hover:bg-white/10" href="{{ $homeUrl }}#about">{{ __('portfolio.nav.about') }}</a>
                            <a class="rounded-xl px-3 py-2 text-sm font-semibold text-zinc-900 hover:bg-zinc-950/5 dark:text-white dark:hover:bg-white/10" href="{{ $homeUrl }}#skills">{{ __('portfolio.nav.skills') }}</a>
                            <a class="rounded-xl px-3 py-2 text-sm font-semibold text-zinc-900 hover:bg-zinc-950/5 dark:text-white dark:hover:bg-white/10" href="{{ $homeUrl }}#experience">{{ __('portfolio.nav.experience') }}</a>
                            <a class="js-open-portfolio-drawer rounded-xl px-3 py-2 text-sm font-semibold text-zinc-900 hover:bg-zinc-950/5 dark:text-white dark:hover:bg-white/10" href="{{ $homeUrl }}#portfolio">{{ __('portfolio.nav.portfolio') }}</a>
                            <a class="rounded-xl px-3 py-2 text-sm font-semibold text-zinc-900 hover:bg-zinc-950/5 dark:text-white dark:hover:bg-white/10" href="{{ $homeUrl }}#contact">{{ __('portfolio.nav.contact') }}</a>
                        </div>
                    </div>
                </div>

                <div class="group relative flex h-full items-center">
                    <a href="{{ $gamesUrl }}" class="cursor-pointer text-sm font-semibold text-zinc-700 underline-offset-8 transition hover:text-zinc-950 hover:underline dark:text-zinc-300 dark:hover:text-white {{ request()->routeIs('games.*') ? 'text-zinc-950 dark:text-white' : '' }}">
                        {{ __('portfolio.nav.games') }}
                    </a>
                    <div class="absolute left-1/2 top-full -translate-x-1/2 min-w-56 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 overflow-hidden rounded-b-2xl border border-t-0 border-zinc-200 bg-white/95 shadow-2xl shadow-zinc-950/10 backdrop-blur-xl dark:border-white/10 dark:bg-zinc-950/90 dark:shadow-black/30">
                        <div class="grid gap-1 p-2">
                            <a class="flex items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-sm font-semibold text-zinc-900 hover:bg-zinc-950/5 dark:text-white dark:hover:bg-white/10" href="{{ $imposterUrl }}">
                                <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded bg-zinc-950 text-white shadow-sm dark:bg-white dark:text-zinc-950">
                                    <svg class="h-3.5 w-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 10h.01"/><path d="M15 10h.01"/><path d="M12 2a8 8 0 0 0-8 8v12l3-3 2.5 2.5L12 19l2.5 2.5L17 19l3 3V10a8 8 0 0 0-8-8z"/></svg>
                                </span>
                                {{ __('imposter.title') }}
                            </a>
                            <a class="flex items-center gap-3 whitespace-nowrap rounded-xl px-3 py-2 text-sm font-semibold text-zinc-900 hover:bg-zinc-950/5 dark:text-white dark:hover:bg-white/10" href="{{ $vampireUrl }}">
                                <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded bg-zinc-950 text-white shadow-sm dark:bg-white dark:text-zinc-950">
                                    <svg class="h-3.5 w-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                                </span>
                                {{ __('vampire.title') }}
                            </a>
                        </div>
                    </div>
                </div>

                <a
                    class="hidden text-sm font-semibold text-zinc-700 underline-offset-8 transition hover:text-zinc-950 hover:underline dark:text-zinc-300 dark:hover:text-white"
                    href="{{ $productsUrl }}"
                >
                    {{ __('portfolio.nav.products') }}
                </a>

            </nav>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    class="hidden sm:inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white/70 px-3 py-2 text-xs font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/70 dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:hover:bg-zinc-950/55 dark:focus-visible:ring-cyan-300/40"
                    data-command-palette-trigger
                    aria-label="{{ __('portfolio.command_palette.aria_label') }}"
                >
                    <span class="font-mono text-[11px] tracking-tight">⌘K</span>
                    <span class="text-zinc-400 dark:text-zinc-500">/</span>
                    <span class="font-mono text-[11px] tracking-tight">Ctrl K</span>
                </button>

                <a
                    href="{{ $localeToggleUrl }}"
                    class="hidden sm:inline-flex items-center justify-center rounded-xl border border-zinc-200 bg-white/70 px-3 py-2 text-xs font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:hover:bg-zinc-950/55"
                    aria-label="Toggle language"
                >
                    {{ app()->getLocale() === 'tr' ? 'EN' : 'TR' }}
                </a>

                <a
                    href="{{ $imposterUrl }}"
                    class="hidden sm:inline-flex items-center gap-2 rounded-xl bg-zinc-950 px-4 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-zinc-950/10 transition hover:bg-zinc-900 dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100"
                >
                    <span class="cyber-gradient-text">{{ __('imposter.title') }}</span>
                    <span aria-hidden="true" class="text-white/70 dark:text-zinc-500">→</span>
                </a>

                <button
                    type="button"
                    class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-xl border border-zinc-200 bg-white/70 text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/70 dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:hover:bg-zinc-950/55 dark:focus-visible:ring-cyan-300/40 md:hidden touch-manipulation cursor-pointer"
                    style="-webkit-tap-highlight-color: transparent; touch-action: manipulation;"
                    aria-label="Open menu"
                    aria-expanded="false"
                    data-mobile-nav-toggle
                >
                    <span class="font-mono text-xs font-semibold">≡</span>
                </button>
            </div>
        </div>
    </div>

    <div class="mx-auto hidden max-w-6xl px-4 pb-4 sm:px-6 lg:px-8 md:hidden" data-mobile-nav>
        <div class="mt-3 overflow-hidden rounded-2xl border border-zinc-200/80 bg-white/95 shadow-xl shadow-zinc-950/8 backdrop-blur-xl dark:border-white/15 dark:bg-zinc-900/95 dark:shadow-black/20">
            <nav class="flex flex-col gap-0.5 p-2.5" aria-label="Mobile">
                <a class="min-h-[48px] rounded-xl px-4 py-3.5 text-[15px] font-semibold text-zinc-800 transition active:bg-zinc-100 dark:text-zinc-100 dark:active:bg-white/10" href="{{ $homeUrl }}">{{ __('portfolio.nav.home') }}</a>


                <div class="my-1 border-t border-zinc-200/70 py-1.5 dark:border-white/10">
                    <p class="px-3 py-1.5 text-[11px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('portfolio.nav.about') }}</p>
                    <div class="grid gap-0.5">
                        <a class="min-h-[44px] rounded-xl px-4 py-3 text-[15px] font-medium text-zinc-700 transition active:bg-zinc-100 dark:text-zinc-300 dark:active:bg-white/10" href="{{ $homeUrl }}#about">{{ __('portfolio.nav.about') }}</a>
                        <a class="min-h-[44px] rounded-xl px-4 py-3 text-[15px] font-medium text-zinc-700 transition active:bg-zinc-100 dark:text-zinc-300 dark:active:bg-white/10" href="{{ $homeUrl }}#skills">{{ __('portfolio.nav.skills') }}</a>
                        <a class="min-h-[44px] rounded-xl px-4 py-3 text-[15px] font-medium text-zinc-700 transition active:bg-zinc-100 dark:text-zinc-300 dark:active:bg-white/10" href="{{ $homeUrl }}#experience">{{ __('portfolio.nav.experience') }}</a>
                        <a class="js-open-portfolio-drawer min-h-[44px] rounded-xl px-4 py-3 text-[15px] font-medium text-zinc-700 transition active:bg-zinc-100 dark:text-zinc-300 dark:active:bg-white/10" href="{{ $homeUrl }}#portfolio">{{ __('portfolio.nav.portfolio') }}</a>
                        <a class="min-h-[44px] rounded-xl px-4 py-3 text-[15px] font-medium text-zinc-700 transition active:bg-zinc-100 dark:text-zinc-300 dark:active:bg-white/10" href="{{ $homeUrl }}#contact">{{ __('portfolio.nav.contact') }}</a>
                    </div>
                </div>

                <div class="my-1 border-t border-zinc-200/70 py-1.5 dark:border-white/10">
                    <p class="px-3 py-1.5 text-[11px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500">{{ __('portfolio.nav.games') }}</p>
                    <div class="grid gap-0.5">
                        <a class="min-h-[44px] rounded-xl px-4 py-3 text-[15px] font-medium text-zinc-700 transition active:bg-zinc-100 dark:text-zinc-300 dark:active:bg-white/10" href="{{ $gamesUrl }}">{{ __('portfolio.nav.games') }} (Tümü)</a>
                        <a class="flex items-center gap-3 min-h-[44px] rounded-xl px-4 py-3 text-[15px] font-medium text-zinc-700 transition active:bg-zinc-100 dark:text-zinc-300 dark:active:bg-white/10" href="{{ $imposterUrl }}">
                            <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded bg-zinc-950 text-white shadow-sm dark:bg-white dark:text-zinc-950">
                                <svg class="h-3.5 w-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 10h.01"/><path d="M15 10h.01"/><path d="M12 2a8 8 0 0 0-8 8v12l3-3 2.5 2.5L12 19l2.5 2.5L17 19l3 3V10a8 8 0 0 0-8-8z"/></svg>
                            </span>
                            {{ __('imposter.title') }}
                        </a>
                        <a class="flex items-center gap-3 min-h-[44px] rounded-xl px-4 py-3 text-[15px] font-medium text-zinc-700 transition active:bg-zinc-100 dark:text-zinc-300 dark:active:bg-white/10" href="{{ $vampireUrl }}">
                            <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded bg-zinc-950 text-white shadow-sm dark:bg-white dark:text-zinc-950">
                                <svg class="h-3.5 w-3.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                            </span>
                            {{ __('vampire.title') }}
                        </a>
                    </div>
                </div>

                <a class="hidden min-h-[48px] rounded-xl px-4 py-3.5 text-[15px] font-semibold text-zinc-800 transition active:bg-zinc-100 dark:text-zinc-100 dark:active:bg-white/10" href="{{ $productsUrl }}">{{ __('portfolio.nav.products') }}</a>

                <div class="mt-2 flex gap-2 border-t border-zinc-200/70 pt-2.5 dark:border-white/10">
                    <a
                        href="{{ $localeToggleUrl }}"
                        class="min-h-[44px] w-full inline-flex items-center justify-center rounded-xl border border-zinc-200 bg-zinc-50/80 px-4 text-sm font-semibold text-zinc-800 transition active:bg-zinc-100 dark:border-white/15 dark:bg-white/5 dark:text-zinc-200 dark:active:bg-white/10"
                    >
                        {{ app()->getLocale() === 'tr' ? 'EN' : 'TR' }}
                    </a>
                </div>
            </nav>
        </div>
    </div>
</header>
