@props([
    'backUrl',
    'backLabel',
    'roomCodeLabel',
    'title',
    'description',
    'accent' => 'cyan',
])

@php
    $badgeClasses = match ($accent) {
        'red' => 'bg-rose-500/10 text-rose-700 dark:text-rose-300',
        default => 'bg-rose-500/10 text-rose-700 dark:text-rose-300',
    };

    $borderClass = $accent === 'red' ? 'dark:border-white/8 dark:bg-zinc-950/75' : 'dark:border-white/10 dark:bg-zinc-950/75';
@endphp

<x-games.room-back-link :href="$backUrl" :label="$backLabel" :accent="$accent" />

<div class="relative overflow-hidden rounded-3xl border border-zinc-200 bg-white/75 p-8 backdrop-blur sm:p-10 {{ $borderClass }}">
    <div class="pointer-events-none absolute inset-0">
        <div class="absolute inset-0 cyber-noise"></div>
    </div>
    <div class="relative">
        <div class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold {{ $badgeClasses }}">
            <span class="inline-flex h-2 w-2 rounded-full bg-rose-400"></span>
            {{ $roomCodeLabel }}
        </div>
        <h2 class="cyber-title mt-4 text-xl font-bold text-zinc-950 dark:text-white">{{ $title }}</h2>
        <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">{{ $description }}</p>
    </div>
</div>
