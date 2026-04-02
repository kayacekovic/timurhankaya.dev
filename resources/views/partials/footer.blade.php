@php
    $homeUrl = \App\Support\Seo::route('home');
    $gamesUrl = \App\Support\Seo::route('games.index');
    $contactUrl = \App\Support\Seo::route('contact');
@endphp

<footer class="mt-24 border-t border-zinc-200/60 py-12 dark:border-white/10">
    <div class="mx-auto grid max-w-6xl gap-8 px-4 sm:px-6 lg:px-8 md:grid-cols-12 md:items-start">
        <div class="md:col-span-5">
            <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.22em] text-zinc-600 dark:text-zinc-400">{{ __('portfolio.ui.builtWithCare') }}</p>
            <p class="mt-2 text-sm font-semibold text-zinc-950 dark:text-white">{{ __('portfolio.ui.stackLine') }}</p>
            <p class="mt-2 max-w-sm text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">
                {{ __('portfolio.ui.tagline') }}
            </p>
            <p class="mt-4 text-xs text-zinc-500 dark:text-zinc-400">
                © {{ now()->year }} Timurhan Kaya
            </p>
        </div>

        <div class="md:col-span-4">
            <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">{{ __('portfolio.ui.navigate') }}</p>
            <div class="mt-3 grid gap-2 text-sm">
                <a class="font-semibold text-zinc-700 underline-offset-4 hover:text-zinc-950 hover:underline dark:text-zinc-300 dark:hover:text-white" href="{{ $homeUrl }}">{{ __('portfolio.nav.home') }}</a>
                <a class="font-semibold text-zinc-700 underline-offset-4 hover:text-zinc-950 hover:underline dark:text-zinc-300 dark:hover:text-white" href="{{ $homeUrl }}#portfolio">{{ __('portfolio.nav.portfolio') }}</a>
                <a class="font-semibold text-zinc-700 underline-offset-4 hover:text-zinc-950 hover:underline dark:text-zinc-300 dark:hover:text-white" href="{{ $gamesUrl }}">{{ __('portfolio.nav.games') }}</a>
                <a class="font-semibold text-zinc-700 underline-offset-4 hover:text-zinc-950 hover:underline dark:text-zinc-300 dark:hover:text-white" href="{{ $contactUrl }}">{{ __('portfolio.nav.contact') }}</a>
            </div>
        </div>

        <div class="md:col-span-3">
            <p class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">{{ __('portfolio.ui.connect') }}</p>
            <div class="mt-3 flex flex-wrap gap-2">
                <a
                    class="inline-flex items-center gap-2 rounded-2xl border border-zinc-200 bg-white/70 px-4 py-2 text-sm font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:hover:bg-zinc-950/55"
                    href="mailto:kayacekovic@gmail.com"
                >
                    <span class="font-mono text-xs">@</span>
                    {{ __('portfolio.ui.email') }}
                </a>
                <a
                    class="inline-flex items-center gap-2 rounded-2xl border border-zinc-200 bg-white/70 px-4 py-2 text-sm font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:hover:bg-zinc-950/55"
                    href="https://www.linkedin.com/in/timurhan-kaya/"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    in
                    LinkedIn
                </a>
                <a
                    class="inline-flex items-center gap-2 rounded-2xl border border-zinc-200 bg-white/70 px-4 py-2 text-sm font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:hover:bg-zinc-950/55"
                    href="https://github.com/kayacekovic"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <span class="font-mono text-xs">gh</span>
                    GitHub
                </a>
            </div>
        </div>
    </div>
</footer>
