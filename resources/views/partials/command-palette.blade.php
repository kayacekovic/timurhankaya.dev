@php
    $homeUrl = \App\Support\Seo::route('home');
    $portfolioUrl = $homeUrl.'#portfolio';
    $contactUrl = $homeUrl.'#contact';
    $cvUrl = \App\Support\Seo::route('cv');
    $gamesUrl = \App\Support\Seo::route('games.index');
    $imposterUrl = \App\Support\Seo::route('games.imposter.index');
    $vampireUrl = \App\Support\Seo::route('games.vampire.index');
    $productsUrl = \Illuminate\Support\Facades\Route::has('products.index')
        ? \App\Support\Seo::route('products.index')
        : $homeUrl.'#products';
@endphp

<div
    id="command-palette"
    class="fixed inset-0 z-100 hidden"
    aria-hidden="true"
    data-command-palette
>
    <button
        type="button"
        class="absolute inset-0 bg-zinc-950/35 backdrop-blur-sm dark:bg-black/55"
        aria-label="{{ __('portfolio.command_palette.aria_label') }}"
        data-command-palette-close
    ></button>
    <div class="absolute inset-x-0 top-16 mx-auto w-full max-w-2xl px-4 sm:px-6">
        <div
            class="overflow-hidden rounded-3xl border border-zinc-200 bg-white/90 shadow-2xl shadow-zinc-950/10 backdrop-blur dark:border-white/10 dark:bg-zinc-950/70 dark:shadow-black/30"
            role="dialog"
            aria-modal="true"
            aria-labelledby="command-palette-title"
        >
            <div class="flex items-center gap-3 border-b border-zinc-200/70 px-5 py-4 dark:border-white/10">
                <div class="flex h-9 w-9 items-center justify-center rounded-2xl bg-zinc-950 text-white ring-1 ring-zinc-950/10 dark:bg-white dark:text-zinc-950 dark:ring-white/10">
                    <span class="font-mono text-xs font-semibold">TK</span>
                </div>
                <div class="min-w-0 flex-1">
                    <p id="command-palette-title" class="text-sm font-semibold text-zinc-950 dark:text-white">{{ __('portfolio.command_palette.title') }}</p>
                    <p class="mt-0.5 text-xs text-zinc-600 dark:text-zinc-400">{{ __('portfolio.command_palette.subtitle') }}</p>
                </div>
                <kbd class="hidden rounded-xl border border-zinc-200 bg-white/70 px-2 py-1 text-xs font-semibold text-zinc-700 dark:border-white/10 dark:bg-zinc-950/35 dark:text-zinc-300 sm:inline">Esc</kbd>
            </div>

            <div class="px-5 py-4">
                <label class="block">
                    <span class="sr-only">{{ __('portfolio.command_palette.title') }}</span>
                    <input
                        type="text"
                        inputmode="search"
                        autocomplete="off"
                        class="w-full rounded-2xl border border-zinc-200 bg-white/70 px-4 py-3 text-sm font-semibold text-zinc-950 shadow-sm outline-none placeholder:text-zinc-400 focus:border-zinc-300 focus-visible:ring-2 focus-visible:ring-cyan-400/60 dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-white/20 dark:focus-visible:ring-cyan-300/30"
                        placeholder="{{ __('portfolio.command_palette.placeholder') }}"
                        data-command-palette-input
                    />
                </label>
            </div>

            <div class="max-h-[52vh] overflow-auto overscroll-contain px-3 pb-4">
                <div class="px-2 pb-2">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('portfolio.command_palette.navigate') }}</p>
                </div>
                <div class="space-y-1" data-command-palette-list>
                    <a
                        href="{{ $homeUrl }}"
                        class="flex w-full items-center justify-between gap-4 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-zinc-900 transition hover:bg-zinc-950/5 focus:bg-zinc-950/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60 dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10 dark:focus-visible:ring-cyan-300/30"
                        data-command
                        data-command-label="{{ __('portfolio.nav.home') }}"
                    >
                        <span class="min-w-0 truncate">{{ __('portfolio.nav.home') }}</span>
                        <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">/</span>
                    </a>
                    <a
                        href="{{ $portfolioUrl }}"
                        class="js-open-portfolio-drawer flex w-full items-center justify-between gap-4 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-zinc-900 transition hover:bg-zinc-950/5 focus:bg-zinc-950/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60 dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10 dark:focus-visible:ring-cyan-300/30"
                        data-command
                        data-command-label="{{ __('portfolio.nav.portfolio') }}"
                    >
                        <span class="min-w-0 truncate">{{ __('portfolio.nav.portfolio') }}</span>
                        <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">#portfolio</span>
                    </a>
                    <a
                        href="{{ $cvUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex w-full items-center justify-between gap-4 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-zinc-900 transition hover:bg-zinc-950/5 focus:bg-zinc-950/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60 dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10 dark:focus-visible:ring-cyan-300/30"
                        data-command
                        data-command-label="{{ __('portfolio.nav.cv_pdf') }}"
                    >
                        <span class="min-w-0 truncate">{{ __('portfolio.nav.cv_pdf') }}</span>
                        <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">/cv</span>
                    </a>
                    <a
                        href="{{ $gamesUrl }}"
                        class="flex w-full items-center justify-between gap-4 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-zinc-900 transition hover:bg-zinc-950/5 focus:bg-zinc-950/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60 dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10 dark:focus-visible:ring-cyan-300/30"
                        data-command
                        data-command-label="{{ __('portfolio.nav.games') }}"
                    >
                        <span class="min-w-0 truncate">{{ __('portfolio.nav.games') }}</span>
                        <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">/games</span>
                    </a>
                    <a
                        href="{{ $imposterUrl }}"
                        class="flex w-full items-center justify-between gap-4 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-zinc-900 transition hover:bg-zinc-950/5 focus:bg-zinc-950/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60 dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10 dark:focus-visible:ring-cyan-300/30"
                        data-command
                        data-command-label="{{ __('portfolio.nav.games') }} {{ __('imposter.title') }}"
                    >
                        <span class="flex min-w-0 items-center gap-2 pl-4 text-zinc-600 dark:text-zinc-400">
                            <svg class="h-4 w-4 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 10h.01"/><path d="M15 10h.01"/><path d="M12 2a8 8 0 0 0-8 8v12l3-3 2.5 2.5L12 19l2.5 2.5L17 19l3 3V10a8 8 0 0 0-8-8z"/></svg>
                            <span class="truncate">{{ __('imposter.title') }}</span>
                        </span>
                        <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">/games/imposter</span>
                    </a>
                    <a
                        href="{{ $vampireUrl }}"
                        class="flex w-full items-center justify-between gap-4 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-zinc-900 transition hover:bg-zinc-950/5 focus:bg-zinc-950/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60 dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10 dark:focus-visible:ring-cyan-300/30"
                        data-command
                        data-command-label="{{ __('portfolio.nav.games') }} {{ __('vampire.title') }}"
                    >
                        <span class="flex min-w-0 items-center gap-2 pl-4 text-zinc-600 dark:text-zinc-400">
                            <svg class="h-4 w-4 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                            <span class="truncate">{{ __('vampire.title') }}</span>
                        </span>
                        <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">/games/vampire</span>
                    </a>

                    <a
                        href="{{ $contactUrl }}"
                        class="flex w-full items-center justify-between gap-4 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-zinc-900 transition hover:bg-zinc-950/5 focus:bg-zinc-950/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60 dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10 dark:focus-visible:ring-cyan-300/30"
                        data-command
                        data-command-label="{{ __('portfolio.nav.contact') }}"
                    >
                        <span class="min-w-0 truncate">{{ __('portfolio.nav.contact') }}</span>
                        <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">#contact</span>
                    </a>
                    <a
                        href="{{ $productsUrl }}"
                        class="hidden flex w-full items-center justify-between gap-4 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-zinc-900 transition hover:bg-zinc-950/5 focus:bg-zinc-950/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60 dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10 dark:focus-visible:ring-cyan-300/30"
                        data-command
                        data-command-label="{{ __('portfolio.nav.products') }}"
                    >
                        <span class="min-w-0 truncate">{{ __('portfolio.nav.products') }}</span>
                        <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">/products</span>
                    </a>
                </div>

                <div class="mt-6 px-2 pb-2">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('portfolio.command_palette.actions') }}</p>
                </div>
                <div class="space-y-1" data-command-palette-list>
                    <button
                        type="button"
                        class="flex w-full items-center justify-between gap-4 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-zinc-900 transition hover:bg-zinc-950/5 focus:bg-zinc-950/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60 dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10 dark:focus-visible:ring-cyan-300/30"
                        data-command
                        data-command-label="{{ __('portfolio.command_palette.copy_email') }}"
                        data-command-copy="kayacekovic@gmail.com"
                    >
                        <span class="min-w-0 truncate">{{ __('portfolio.command_palette.copy_email') }}</span>
                        <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">clipboard</span>
                    </button>
                    <button
                        type="button"
                        class="flex w-full items-center justify-between gap-4 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-zinc-900 transition hover:bg-zinc-950/5 focus:bg-zinc-950/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-cyan-400/60 dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10 dark:focus-visible:ring-cyan-300/30"
                        data-command
                        data-command-label="{{ __('portfolio.command_palette.copy_link') }}"
                        data-command-copy-current-url="1"
                    >
                        <span class="min-w-0 truncate">{{ __('portfolio.command_palette.copy_link') }}</span>
                        <span class="font-mono text-xs text-zinc-500 dark:text-zinc-400">url</span>
                    </button>
                </div>

                <div class="mt-5 px-5">
                    <p class="text-xs text-zinc-600 dark:text-zinc-400">
                        {{ __('portfolio.command_palette.tip') }} <kbd class="rounded-lg border border-zinc-200 bg-white/70 px-1.5 py-0.5 font-mono text-[11px] font-semibold text-zinc-700 dark:border-white/10 dark:bg-zinc-950/35 dark:text-zinc-300">Ctrl</kbd> + <kbd class="rounded-lg border border-zinc-200 bg-white/70 px-1.5 py-0.5 font-mono text-[11px] font-semibold text-zinc-700 dark:border-white/10 dark:bg-zinc-950/35 dark:text-zinc-300">K</kbd> {{ __('portfolio.command_palette.tip_suffix') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
