@props([
    'sections',
    'title' => null,
    'description' => null,
])

@php
    $totalProjects = 0;
    $badgeCounts = [];
    $awardCount = 0;

    foreach ($sections as $section) {
        $items = $section['items'] ?? [];
        if (isset($section['groups'])) {
            foreach ($section['groups'] as $group) {
                $items = array_merge($items, $group['items'] ?? []);
            }
        }
        foreach ($items as $item) {
            $totalProjects++;
            $badge = $item['badge'] ?? null;
            if ($badge) {
                $badgeCounts[$badge] = ($badgeCounts[$badge] ?? 0) + 1;
            }
            if (!empty($item['award'])) {
                $awardCount++;
            }
        }
    }
    if ($awardCount > 0) {
        $badgeCounts['Ödüllü'] = $awardCount;
    }

    $badgeColors = [
        'FinTech'    => 'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-400',
        'InsureTech' => 'bg-violet-100 text-violet-700 dark:bg-violet-500/15 dark:text-violet-400',
        'SaaS'       => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400',
        'Web3'       => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
        'E-Ticaret'  => 'bg-rose-100 text-rose-700 dark:bg-rose-500/15 dark:text-rose-400',
        'Topluluk'   => 'bg-teal-100 text-teal-700 dark:bg-teal-500/15 dark:text-teal-400',
        'Kişisel'    => 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400',
        'Kurumsal'   => 'bg-orange-100 text-orange-700 dark:bg-orange-500/15 dark:text-orange-400',
        'Ödüllü'     => 'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
        'OYUN'       => 'bg-purple-100 text-purple-700 dark:bg-purple-500/15 dark:text-purple-400',
        'Mobil'      => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-500/15 dark:text-cyan-400',
    ];
@endphp

<div
    id="project-drawer"
    class="fixed inset-0 z-100 hidden"
    role="dialog"
    aria-modal="true"
    aria-labelledby="drawer-title"
>
    {{-- Backdrop --}}
    <div
        id="drawer-backdrop"
        class="absolute inset-0 bg-zinc-950/40 backdrop-blur-sm transition-opacity duration-500 ease-out opacity-0"
        aria-hidden="true"
    ></div>

    {{-- Content Container --}}
    <div class="fixed inset-y-0 right-0 flex max-w-full">
        <div
            id="drawer-surface"
            class="relative w-screen max-w-2xl translate-x-full transition-transform duration-500 ease-out sm:duration-700"
        >
            <div class="flex h-full flex-col overflow-hidden border-l border-zinc-200 bg-white/95 shadow-2xl backdrop-blur-xl dark:border-white/10 dark:bg-zinc-950/95">

                {{-- Header --}}
                <div class="sticky top-0 z-10 border-b border-zinc-200 bg-white/80 px-6 py-5 backdrop-blur-md dark:border-white/5 dark:bg-zinc-950/80">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 id="drawer-title" class="text-2xl font-bold tracking-tight text-zinc-950 dark:text-white">
                                {{ $title ?: __('portfolio.nav.portfolio') }}
                            </h2>
                            @if (is_string($description) && $description !== '')
                                <p class="mt-1.5 text-sm text-zinc-600 dark:text-zinc-300">{{ $description }}</p>
                            @endif
                        </div>
                        <button
                            type="button"
                            class="group rounded-full p-2 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-500 dark:hover:bg-white/5 dark:hover:text-zinc-300"
                            id="close-drawer"
                        >
                            <span class="sr-only">Close panel</span>
                            <svg class="h-6 w-6 transition-transform group-hover:rotate-90" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Filter chips --}}
                    <div class="mt-4 flex flex-wrap gap-2" id="drawer-filters" role="group" aria-label="Filtrele">
                        <button
                            type="button"
                            class="drawer-filter-chip drawer-filter-chip--active inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-[11px] font-bold transition"
                            data-filter="all"
                        >
                            Tümü
                            <span class="rounded-full bg-zinc-200 px-1.5 py-0.5 text-[9px] font-bold text-zinc-600 dark:bg-zinc-700 dark:text-zinc-300">{{ $totalProjects }}</span>
                        </button>
                        @foreach ($badgeCounts as $badge => $count)
                            <button
                                type="button"
                                class="drawer-filter-chip inline-flex items-center gap-1.5 rounded-full border border-zinc-200 bg-white px-3 py-1 text-[11px] font-bold text-zinc-600 transition hover:border-zinc-300 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-400 dark:hover:border-white/20"
                                data-filter="{{ $badge }}"
                            >
                                @if ($badge === 'Ödüllü')
                                    <svg class="h-3 w-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endif
                                {{ $badge }}
                                <span class="rounded-full bg-zinc-100 px-1.5 py-0.5 text-[9px] font-bold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">{{ $count }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Scrollable Content --}}
                <div class="relative flex-1 overflow-y-auto p-6 sm:p-8" id="drawer-content">
                    <div class="space-y-12">
                        @foreach ($sections as $section)
                            <section data-section>
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-zinc-100 text-zinc-600 dark:bg-zinc-900 dark:text-zinc-400">
                                        @switch(data_get($section, 'icon'))
                                            @case('enterprise')
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                                @break
                                            @case('agency')
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                                                @break
                                            @case('freelance')
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                                @break
                                            @case('blockchain')
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                                @break
                                            @default
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                        @endswitch
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-zinc-950 dark:text-white">{{ $section['title'] }}</h3>
                                        @if(isset($section['subtitle']))
                                            <p class="text-xs font-medium text-zinc-500/80 dark:text-zinc-500">{{ $section['subtitle'] }}</p>
                                        @endif
                                    </div>
                                </div>

                                @if (isset($section['groups']))
                                    <div class="space-y-10">
                                        @foreach ($section['groups'] as $group)
                                            <div class="pl-4 sm:pl-6 border-l border-zinc-200/60 dark:border-white/5">
                                                <h4 class="text-xs font-bold uppercase tracking-[0.15em] text-zinc-400 dark:text-zinc-600 mb-6">{{ $group['title'] }}</h4>
                                                <div class="grid gap-4">
                                                    @foreach ($group['items'] as $item)
                                                        <x-project-drawer-item :item="$item" :badgeColors="$badgeColors" />
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="flex flex-col gap-3">
                                        @foreach ($section['items'] as $item)
                                            <x-project-drawer-item :item="$item" :badgeColors="$badgeColors" />
                                        @endforeach
                                    </div>
                                @endif
                            </section>
                        @endforeach
                    </div>

                    {{-- Empty state (shown when filter has no results) --}}
                    <div id="drawer-empty-state" class="hidden py-16 text-center">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Bu kategoride proje bulunamadı.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .drawer-filter-chip--active {
        background-color: rgb(9 9 11);
        border-color: rgb(9 9 11);
        color: white;
    }
    .dark .drawer-filter-chip--active {
        background-color: white;
        border-color: white;
        color: rgb(9 9 11);
    }
    .drawer-filter-chip--active span {
        background-color: rgba(255,255,255,0.2);
        color: white;
    }
    .dark .drawer-filter-chip--active span {
        background-color: rgba(0,0,0,0.15);
        color: rgb(9 9 11);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const filtersEl = document.getElementById('drawer-filters');
        const emptyState = document.getElementById('drawer-empty-state');
        if (!filtersEl) return;

        filtersEl.addEventListener('click', (e) => {
            const chip = e.target.closest('[data-filter]');
            if (!chip) return;

            const filter = chip.dataset.filter;

            // Update active chip
            filtersEl.querySelectorAll('.drawer-filter-chip').forEach(c => c.classList.remove('drawer-filter-chip--active'));
            chip.classList.add('drawer-filter-chip--active');

            // Filter items
            const sections = document.querySelectorAll('[data-section]');
            let totalVisible = 0;

            sections.forEach(section => {
                const items = section.querySelectorAll('[data-badge-item]');
                let sectionVisible = 0;

                items.forEach(item => {
                    const badge = item.dataset.badgeItem;
                    const isAward = item.dataset.award === 'true';
                    const show = filter === 'all'
                        || badge === filter
                        || (filter === 'Ödüllü' && isAward);

                    item.style.display = show ? '' : 'none';
                    if (show) sectionVisible++;
                });

                section.style.display = sectionVisible > 0 ? '' : 'none';
                totalVisible += sectionVisible;
            });

            if (emptyState) {
                emptyState.classList.toggle('hidden', totalVisible > 0);
            }
        });
    });
</script>
