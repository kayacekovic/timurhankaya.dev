@props([
    'href',
    'label',
    'accent' => 'cyan',
])

@php
    [$focusClass, $borderClass] = match ($accent) {
        'red' => ['focus-visible:ring-red-400/60 dark:focus-visible:ring-red-300/30', 'dark:border-white/8'],
        default => ['focus-visible:ring-cyan-400/60 dark:focus-visible:ring-cyan-300/30', 'dark:border-white/10'],
    };
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->merge([
        'class' => "inline-flex w-fit items-center justify-center gap-2 rounded-2xl border border-zinc-200 bg-white/75 px-5 py-2.5 text-sm font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white focus:outline-none {$focusClass} {$borderClass} dark:bg-zinc-950/40 dark:text-white dark:hover:bg-zinc-950/55",
    ]) }}
>
    <span aria-hidden="true" class="text-zinc-500 dark:text-zinc-400">&larr;</span>
    {{ $label }}
</a>
