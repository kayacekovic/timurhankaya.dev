@php
$currentColor = $colorMap[$color] ?? $colorMap['sky'];
$canCancel    = (bool) session('games.identity');

$glowRgb = match($color) {
    'red'    => '239,68,68',
    'orange' => '249,115,22',
    'amber'  => '245,158,11',
    'green'  => '34,197,94',
    'teal'   => '20,184,166',
    'sky'    => '14,165,233',
    'purple' => '168,85,247',
    'pink'   => '236,72,153',
    default  => '14,165,233',
};
@endphp

{{-- Single root element keeps wire:id in place even when @teleport moves content --}}
<div>
@if (!$editing)
    {{-- ───────── CHARACTER CARD (inline) ───────── --}}
    <div class="mt-10 relative overflow-hidden rounded-3xl border border-zinc-200/60 dark:border-white/10"
         style="background: linear-gradient(135deg, rgba({{ $glowRgb }},0.10) 0%, transparent 55%), rgba(255,255,255,0.82); backdrop-filter: blur(24px);">

        <div class="pointer-events-none absolute inset-0 hidden dark:block"
             style="background: linear-gradient(135deg, rgba({{ $glowRgb }},0.13) 0%, transparent 60%), rgba(9,9,11,0.78); backdrop-filter: blur(24px);"></div>

        <div class="pointer-events-none absolute inset-0 cyber-noise opacity-60"></div>

        {{-- Colored top accent bar --}}
        <div class="absolute left-0 right-0 top-0 h-[2px]"
             style="background: linear-gradient(to right, rgba({{ $glowRgb }},0), rgba({{ $glowRgb }},0.9) 40%, rgba({{ $glowRgb }},0.4) 100%);"></div>

        <div class="relative flex items-center gap-4 px-5 py-4 sm:px-6">
            {{-- Avatar --}}
            <div class="relative shrink-0">
                <div class="absolute inset-0 -m-3 rounded-2xl blur-xl opacity-60"
                     style="background: radial-gradient(circle, rgba({{ $glowRgb }},0.6), transparent 70%);"></div>

                <div class="relative inline-flex h-14 w-14 items-center justify-center rounded-2xl text-3xl leading-none {{ $currentColor['bg'] }}"
                     style="box-shadow: 0 0 0 1px rgba({{ $glowRgb }},0.25), 0 8px 24px rgba({{ $glowRgb }},0.25);">
                    {{ $emoji }}
                </div>
            </div>

            {{-- Identity text --}}
            <div class="min-w-0 flex-1">
                <p class="font-mono text-[10px] font-semibold uppercase tracking-[0.22em] text-zinc-500 dark:text-zinc-400">
                    {{ __('games.identity.playingAs') }}
                </p>
                <p class="cyber-title mt-0.5 truncate text-base font-bold leading-tight text-zinc-950 dark:text-white">
                    {{ $name }}
                </p>
            </div>

            {{-- Change button --}}
            <button
                wire:click="edit"
                type="button"
                class="touch-manipulation shrink-0 rounded-lg border border-zinc-200/80 bg-white/60 px-3 py-1.5 text-xs font-semibold text-zinc-600 backdrop-blur transition hover:bg-white hover:text-zinc-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400/60 dark:border-white/10 dark:bg-zinc-900/50 dark:text-zinc-400 dark:hover:bg-zinc-800/60 dark:hover:text-white"
            >
                {{ __('games.identity.change') }}
            </button>
        </div>
    </div>

@else
    {{-- ───────── IDENTITY MODAL ───────── --}}
    @teleport('body')
    <div
        class="fixed inset-0 z-[100] flex items-center justify-center p-4"
        @if($canCancel) wire:keydown.window.escape="cancelEdit" @endif
    >
        {{-- Backdrop --}}
        @if($canCancel)
            <button
                type="button"
                wire:click="cancelEdit"
                class="absolute inset-0 cursor-default bg-zinc-950/50 backdrop-blur-sm"
                aria-hidden="true"
                tabindex="-1"
            ></button>
        @else
            <div class="absolute inset-0 bg-zinc-950/60 backdrop-blur-sm"></div>
        @endif

        {{-- Modal card --}}
        <div
            role="dialog"
            aria-modal="true"
            class="relative w-full max-w-lg overflow-hidden rounded-3xl border border-zinc-200/60 bg-white/90 shadow-2xl shadow-zinc-950/20 backdrop-blur-xl dark:border-white/10 dark:bg-zinc-950/90 dark:shadow-black/40"
        >
            <div class="pointer-events-none absolute inset-0 cyber-noise"></div>

            {{-- Colored accent bar --}}
            <div class="absolute left-0 right-0 top-0 h-[2px]"
                 style="background: linear-gradient(to right, rgba({{ $glowRgb }},0), rgba({{ $glowRgb }},0.9) 45%, rgba({{ $glowRgb }},0.35) 100%);"></div>

            {{-- Live preview header --}}
            <div class="relative border-b border-zinc-200/50 dark:border-white/[0.07]">
                <div class="absolute inset-0"
                     style="background: linear-gradient(135deg, rgba({{ $glowRgb }},0.09) 0%, transparent 65%);"></div>

                <div class="relative flex items-center justify-between gap-4 px-6 py-5">
                    <div class="flex items-center gap-5">
                        {{-- Live avatar preview --}}
                        <div class="relative shrink-0">
                            <div class="absolute inset-0 -m-2.5 rounded-2xl blur-xl opacity-50"
                                 style="background: radial-gradient(circle, rgba({{ $glowRgb }},0.7), transparent 70%);"></div>

                            <div class="relative inline-flex h-18 w-18 items-center justify-center rounded-2xl text-5xl leading-none transition-all duration-300 {{ $currentColor['bg'] }}"
                                 style="box-shadow: 0 0 0 1px rgba({{ $glowRgb }},0.22), 0 8px 32px rgba({{ $glowRgb }},0.25);">
                                {{ $emoji }}
                            </div>
                        </div>

                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-[0.22em] text-zinc-400 dark:text-zinc-500">
                                {{ __('games.identity.title') }}
                            </p>
                            @if ($name)
                                <p class="cyber-title mt-0.5 text-lg font-bold text-zinc-950 dark:text-white">{{ $name }}</p>
                            @else
                                <p class="cyber-title mt-0.5 text-lg font-bold tracking-widest text-zinc-300 dark:text-zinc-700">· · ·</p>
                            @endif
                        </div>
                    </div>

                    @if($canCancel)
                        <button
                            type="button"
                            wire:click="cancelEdit"
                            class="touch-manipulation inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-zinc-200/80 bg-white/60 text-sm text-zinc-500 transition hover:bg-white hover:text-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400/60 dark:border-white/10 dark:bg-zinc-900/50 dark:text-zinc-400 dark:hover:bg-zinc-800/60 dark:hover:text-white"
                            aria-label="Close"
                        >&times;</button>
                    @endif
                </div>
            </div>

            {{-- Form body --}}
            <form wire:submit="save" class="relative space-y-6 p-6 sm:p-7">

                {{-- Name --}}
                <div>
                    <p class="font-mono text-[10px] font-semibold uppercase tracking-[0.22em] text-zinc-500 dark:text-zinc-400">
                        {{ __('games.identity.name') }}
                    </p>
                    <input
                        wire:model.live="name"
                        type="text"
                        name="name"
                        autocomplete="nickname"
                        maxlength="24"
                        autofocus
                        class="mt-2.5 w-full rounded-2xl border border-zinc-200 bg-white/70 px-5 py-3.5 text-base font-semibold text-zinc-950 shadow-sm outline-none transition placeholder:font-normal placeholder:text-zinc-400 focus:border-zinc-300 focus-visible:ring-2 focus-visible:ring-zinc-400/60 dark:border-white/10 dark:bg-zinc-900/50 dark:text-white dark:placeholder:text-zinc-600 dark:focus:border-white/20 dark:focus-visible:ring-zinc-300/30"
                        placeholder="{{ __('games.identity.namePlaceholder') }}"
                    />
                    @error('name') <span class="mt-2 block text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</span> @enderror
                </div>

                {{-- Color + Emoji --}}
                <div class="grid gap-6 sm:grid-cols-2">
                    {{-- Color --}}
                    <div>
                        <p class="font-mono text-[10px] font-semibold uppercase tracking-[0.22em] text-zinc-500 dark:text-zinc-400">
                            {{ __('games.identity.color') }}
                        </p>
                        <div class="mt-4 flex flex-wrap gap-3">
                            @foreach ($colorMap as $key => $cls)
                                <button
                                    wire:click="setColor('{{ $key }}')"
                                    type="button"
                                    class="touch-manipulation h-11 w-11 rounded-full transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 {{ $cls['swatch'] }} {{ $color === $key ? 'scale-115 ring-2 ring-offset-2 '.$cls['ring'] : 'opacity-40 hover:opacity-100 hover:scale-110' }}"
                                    aria-label="{{ $key }}"
                                ></button>
                            @endforeach
                        </div>
                        @error('color') <span class="mt-2 block text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</span> @enderror
                    </div>

                    {{-- Emoji --}}
                    <div>
                        <p class="font-mono text-[10px] font-semibold uppercase tracking-[0.22em] text-zinc-500 dark:text-zinc-400">
                            {{ __('games.identity.emoji') }}
                        </p>
                        <div class="mt-4 grid grid-cols-4 gap-2.5">
                            @foreach ($emojis as $e)
                                <button
                                    wire:click="setEmoji('{{ $e }}')"
                                    type="button"
                                    class="touch-manipulation cursor-pointer inline-flex aspect-square items-center justify-center rounded-2xl p-3 text-4xl transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400/60 {{ $emoji === $e ? $currentColor['bg'].' scale-105 ring-2 ring-inset '.$currentColor['ring'] : 'bg-zinc-100 dark:bg-zinc-800/80 opacity-50 hover:opacity-100 hover:scale-105' }}"
                                    aria-label="{{ $e }}"
                                >{{ $e }}</button>
                            @endforeach
                        </div>
                        @error('emoji') <span class="mt-2 block text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end border-t border-zinc-100 pt-5 dark:border-white/[0.06]">
                    <button
                        type="submit"
                        class="touch-manipulation inline-flex items-center gap-3 rounded-2xl bg-zinc-950 px-8 py-4 text-base font-semibold text-white shadow-lg ring-1 ring-zinc-950/10 transition hover:bg-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400/60 dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100"
                    >
                        {{ __('games.identity.save') }}
                        <span aria-hidden="true" class="text-white/60 dark:text-zinc-500">→</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endteleport
@endif
</div>
