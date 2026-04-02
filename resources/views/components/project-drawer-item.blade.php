@props(['item', 'badgeColors' => []])

@php
    $badge = $item['badge'] ?? null;
    $isAward = !empty($item['award']);
    $isArchived = !empty($item['archived']);
    $hasUrl = !empty($item['url']);
    $highlights = $item['highlights'] ?? [];
    $year = $item['year'] ?? null;
    $context = $item['context'] ?? null;
    $contextLabel = $item['context_label'] ?? ($context === 'company' ? 'Şirket' : ($context === 'freelance' ? 'Freelance' : null));
    $status = $item['status'] ?? null;
    $highlightsTitle = $item['highlights_title'] ?? 'Ne Yaptım';
    $hasDetail = count($highlights) > 0;
    $isTurkish = app()->getLocale() === 'tr';
    $archivedLabel = $isTurkish ? 'Arşiv' : 'Archived';

    $badgeClass = $badge ? ($badgeColors[$badge] ?? 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400') : null;

    $iconMap = [
        'Laravel'       => 'laravel',
        'PHP'           => 'php',
        'Symfony'       => 'symfony',
        'React'         => 'react',
        'Vue.js'        => 'vuedotjs',
        'Node.js'       => 'nodedotjs',
        'Nuxt.js'       => 'nuxtdotjs',
        'Tailwind CSS'  => 'tailwindcss',
        'Elasticsearch' => 'elasticsearch',
        'MongoDB'       => 'mongodb',
        'PostgreSQL'    => 'postgresql',
        'Sass'          => 'sass',
        'HTML'          => 'html5',
        'CSS'           => 'css3',
        'JavaScript'    => 'javascript',
        'Alpine.js'     => 'alpinedotjs',
        'Redis'         => 'redis',
        'React Native'  => 'react',
        'Inertia.js'    => 'inertia',
        'Livewire'      => 'livewire',
        'Bootstrap'     => 'bootstrap',
        'Go'            => 'go',
        'AWS'           => 'amazonaws',
        'Next.js'       => 'nextdotjs',
        'Kubernetes'    => 'kubernetes',
        'RabbitMQ'      => 'rabbitmq',
        'Varnish'       => 'varnish',
        'Memcached'     => 'memcached',
        'Fluentd'       => 'fluentd',
        'Elastic'       => 'elasticsearch',
        'Python'        => 'python',
        'Docker'        => 'docker',
        'TimescaleDB'   => 'timescaledb',
        'Socket.io'     => 'socketdotio',
    ];

    $colorMap = [
        'Laravel'       => 'bg-red-50 text-red-600 border-red-200/50 dark:bg-red-500/10 dark:text-red-400 dark:border-red-500/20',
        'PHP'           => 'bg-indigo-50 text-indigo-600 border-indigo-200/50 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20',
        'Symfony'       => 'bg-zinc-100 text-zinc-900 border-zinc-300 dark:bg-white/10 dark:text-zinc-100 dark:border-white/20',
        'React'         => 'bg-sky-50 text-sky-600 border-sky-200/50 dark:bg-sky-500/10 dark:text-sky-400 dark:border-sky-500/20',
        'React Native'  => 'bg-sky-50 text-sky-600 border-sky-200/50 dark:bg-sky-500/10 dark:text-sky-400 dark:border-sky-500/20',
        'Vue.js'        => 'bg-emerald-50 text-emerald-600 border-emerald-200/50 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
        'Nuxt.js'       => 'bg-emerald-50 text-emerald-600 border-emerald-200/50 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
        'Node.js'       => 'bg-green-50 text-green-600 border-green-200/50 dark:bg-green-500/10 dark:text-green-400 dark:border-green-500/20',
        'Tailwind CSS'  => 'bg-cyan-50 text-cyan-600 border-cyan-200/50 dark:bg-cyan-500/10 dark:text-cyan-400 dark:border-cyan-500/20',
        'Elasticsearch' => 'bg-blue-50 text-blue-700 border-blue-200/50 dark:bg-blue-600/10 dark:text-blue-400 dark:border-blue-600/20',
        'MongoDB'       => 'bg-emerald-50 text-emerald-700 border-emerald-200/50 dark:bg-emerald-600/10 dark:text-emerald-400 dark:border-emerald-600/20',
        'PostgreSQL'    => 'bg-blue-50 text-blue-600 border-blue-200/50 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
        'Redis'         => 'bg-red-50 text-red-700 border-red-200/50 dark:bg-red-600/10 dark:text-red-400 dark:border-red-600/20',
        'Go'            => 'bg-sky-50 text-sky-600 border-sky-200/50 dark:bg-sky-500/10 dark:text-sky-400 dark:border-sky-500/20',
        'AWS'           => 'bg-orange-50 text-orange-600 border-orange-200/50 dark:bg-orange-500/10 dark:text-orange-400 dark:border-orange-500/20',
        'Bootstrap'     => 'bg-purple-50 text-purple-600 border-purple-200/50 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20',
        'Sass'          => 'bg-pink-50 text-pink-600 border-pink-200/50 dark:bg-pink-500/10 dark:text-pink-400 dark:border-pink-500/20',
        'JavaScript'    => 'bg-yellow-50 text-yellow-700 border-yellow-200/50 dark:bg-yellow-500/10 dark:text-yellow-400 dark:border-yellow-500/20',
        'Inertia.js'    => 'bg-purple-50 text-purple-600 border-purple-200/50 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20',
        'Livewire'      => 'bg-pink-50 text-pink-600 border-pink-200/50 dark:bg-pink-500/10 dark:text-pink-400 dark:border-pink-500/20',
        'Alpine.js'     => 'bg-sky-50 text-sky-700 border-sky-200/50 dark:bg-sky-400/10 dark:text-sky-400 dark:border-sky-400/20',
        'Next.js'       => 'bg-zinc-100 text-zinc-900 border-zinc-300 dark:bg-black dark:text-white dark:border-white/20',
        'Kubernetes'    => 'bg-blue-50 text-blue-600 border-blue-200/50 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
        'RabbitMQ'      => 'bg-orange-50 text-orange-700 border-orange-200/50 dark:bg-orange-600/10 dark:text-orange-400 dark:border-orange-600/20',
        'Varnish'       => 'bg-zinc-50 text-zinc-600 border-zinc-200 dark:bg-zinc-800/50 dark:text-zinc-300',
        'Memcached'     => 'bg-blue-50 text-blue-500 border-blue-100 dark:bg-blue-900/20 dark:text-blue-300',
        'Fluentd'       => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400',
        'Elastic'       => 'bg-blue-50 text-blue-700 border-blue-200/50 dark:bg-blue-600/10 dark:text-blue-400 dark:border-blue-600/20',
        'Python'        => 'bg-blue-50 text-blue-600 border-blue-200/50 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
        'Docker'        => 'bg-sky-50 text-sky-600 border-sky-200/50 dark:bg-sky-500/10 dark:text-sky-400 dark:border-sky-500/20',
        'TimescaleDB'   => 'bg-blue-50 text-blue-600 border-blue-200/50 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20',
        'Socket.io'     => 'bg-zinc-100 text-zinc-900 border-zinc-200 dark:bg-white/10 dark:text-white dark:border-white/10',
    ];

    $itemId = 'drawer-item-' . \Illuminate\Support\Str::slug($item['name']);
@endphp

<div
    class="group relative rounded-xl border border-zinc-200/60 bg-white shadow-sm transition dark:border-white/5 dark:bg-zinc-900/40 @if($isArchived) opacity-60 @endif"
    data-badge-item="{{ $badge }}"
    data-award="{{ $isAward ? 'true' : 'false' }}"
>
    {{-- Main row — clickable if has detail --}}
    <div
        class="flex items-start justify-between gap-3 p-4 @if($hasDetail) cursor-pointer select-none @endif"
        @if($hasDetail) onclick="toggleDrawerItem('{{ $itemId }}')" @endif
    >
        <div class="min-w-0 flex-1">
            {{-- Name row --}}
            <div class="flex flex-wrap items-center gap-1.5">
                <h5 class="text-[13px] font-bold text-zinc-950 dark:text-white transition group-hover:text-sky-600 dark:group-hover:text-sky-400">
                    {{ $item['name'] }}
                </h5>

                @if ($year)
                    <span class="font-mono text-[9px] font-semibold text-zinc-400 dark:text-zinc-600">{{ $year }}</span>
                @endif



                @if ($isArchived)
                    <span class="inline-flex rounded-full bg-zinc-100 px-1.5 py-0.5 text-[8px] font-bold uppercase tracking-wider text-zinc-400 dark:bg-white/5 dark:text-zinc-500">{{ $archivedLabel }}</span>
                @elseif (!$hasUrl)
                    <span class="inline-flex rounded-full bg-zinc-100 px-1.5 py-0.5 text-[8px] font-bold uppercase tracking-wider text-zinc-500 dark:bg-white/5 dark:text-zinc-500">Private</span>
                @endif

                @if ($isAward)
                    <span class="inline-flex items-center gap-0.5 rounded-full bg-amber-100 px-1.5 py-0.5 text-[8px] font-bold uppercase tracking-wider text-amber-600 dark:bg-amber-500/15 dark:text-amber-400">
                        <svg class="h-2.5 w-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        Ödüllü
                    </span>
                @endif
            </div>

            {{-- Description --}}
            @if(isset($item['description']))
                <p class="mt-1 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">
                    {{ $item['description'] }}
                </p>
            @endif

            {{-- Bottom row: badge + stack --}}
            <div class="mt-2.5 flex flex-wrap items-center gap-1.5">
                @if ($badge && $badgeClass)
                    <span class="inline-flex rounded-full px-2 py-0.5 text-[9px] font-bold uppercase tracking-wider {{ $badgeClass }}">{{ $badge }}</span>
                @endif

                @if (isset($item['stack']) && is_array($item['stack']))
                    @foreach ($item['stack'] as $tech)
                        @php
                            $techName = trim($tech);
                            $slug = $iconMap[$techName] ?? null;
                            $tagStyle = $colorMap[$techName] ?? 'border-zinc-200 bg-white text-zinc-600 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-400';
                        @endphp
                        <span class="inline-flex items-center gap-1 rounded-md border px-1.5 py-0.5 font-mono text-[8px] font-bold uppercase tracking-wide {{ $tagStyle }}">
                            @if($slug)
                                <img
                                    src="https://cdn.jsdelivr.net/npm/simple-icons@v11/icons/{{ $slug }}.svg"
                                    alt="{{ $tech }}"
                                    class="h-2 w-2 opacity-70 group-hover:opacity-100 transition-opacity grayscale group-hover:grayscale-0 dark:invert"
                                    loading="lazy"
                                />
                            @endif
                            {{ $techName }}
                        </span>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Right side: expand toggle OR link button --}}
        <div class="flex shrink-0 items-center gap-1.5">
            @php
                $links = $item['links'] ?? [];
                $hasMultipleLinks = count($links) > 0;
            @endphp

            @if (($hasUrl || $hasMultipleLinks) && !$isArchived)
                <div class="relative group/links-dropdown" onclick="event.stopPropagation()">
                    <a
                        href="{{ $hasUrl ? $item['url'] : 'javascript:void(0)' }}"
                        target="{{ $hasUrl ? '_blank' : '_self' }}"
                        rel="noopener noreferrer"
                        class="flex h-7 w-7 items-center justify-center rounded-lg bg-zinc-100 text-zinc-400 transition hover:bg-sky-500 hover:text-white dark:bg-white/5 dark:text-zinc-500 dark:hover:bg-sky-500 dark:hover:text-white"
                        aria-label="Visit {{ $item['name'] }}"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>

                    @if ($hasMultipleLinks)
                        <div class="pointer-events-none invisible absolute right-0 top-full z-20 mt-0 pt-1 w-48 origin-top-right rounded-xl transition-all opacity-0 group-hover/links-dropdown:pointer-events-auto group-hover/links-dropdown:visible group-hover/links-dropdown:opacity-100">
                            <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white p-1.5 shadow-xl dark:border-white/10 dark:bg-zinc-950">
                            @foreach ($links as $link)
                                <a
                                    href="{{ $link['url'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex items-center justify-between rounded-lg px-2.5 py-2 text-[10px] font-medium text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-white/5"
                                >
                                    {{ $link['label'] }}
                                    <svg class="h-2.5 w-2.5 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                </a>
                            @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @if ($hasDetail)
                <div
                    id="{{ $itemId }}-chevron"
                    class="flex h-7 w-7 items-center justify-center rounded-lg text-zinc-400 transition dark:text-zinc-600"
                    aria-hidden="true"
                >
                    <svg class="h-3.5 w-3.5 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            @endif
        </div>
    </div>

    {{-- Expandable highlights panel --}}
    @if ($hasDetail)
        <div id="{{ $itemId }}" class="hidden border-t border-zinc-100 px-4 pb-4 pt-3 dark:border-white/5">
            <p class="mb-2 font-mono text-[9px] font-bold uppercase tracking-[0.15em] text-zinc-400 dark:text-zinc-600">{{ $highlightsTitle }}</p>
            <ul class="space-y-1.5">
                @foreach ($highlights as $hl)
                    <li class="flex items-start gap-2 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">
                        <span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-sky-400"></span>
                        {!! preg_replace('/\*\*(.*?)\*\*/', '<strong class="font-extrabold text-zinc-900 dark:text-white">$1</strong>', e($hl)) !!}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

@once
<script>
    function toggleDrawerItem(id) {
        const panel = document.getElementById(id);
        const chevron = document.getElementById(id + '-chevron');
        if (!panel) return;
        const isOpen = !panel.classList.contains('hidden');
        panel.classList.toggle('hidden', isOpen);
        if (chevron) chevron.querySelector('svg').style.transform = isOpen ? '' : 'rotate(180deg)';
    }
</script>
@endonce
