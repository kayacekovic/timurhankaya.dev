@php
    $backUrl = \App\Support\Seo::route('games.vampire.index');
    $roomUrl = \App\Support\Seo::route('games.vampire.room', ['roomCode' => $roomCode]);
@endphp

<div class="flex flex-col gap-4"
    x-data="{
        roleVisible: true,
        phaseCountdown: 45,
        phaseCountdownStarted: null,
        phaseCountdownInterval: null,
        playBongg() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                if (ctx.state === 'suspended') ctx.resume();
                const gain = ctx.createGain();
                gain.gain.setValueAtTime(0, ctx.currentTime);
                gain.gain.linearRampToValueAtTime(1.0, ctx.currentTime + 0.05);
                gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.8);
                gain.connect(ctx.destination);
                const freqs = [350, 380, 410, 800];
                freqs.forEach(f => {
                    const osc = ctx.createOscillator();
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(f * 2, ctx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(f / 3, ctx.currentTime + 0.8);
                    osc.connect(gain);
                    osc.start();
                    osc.stop(ctx.currentTime + 0.8);
                });
            } catch (e) {
                console.error('Audio failed:', e);
            }
        },
        initPhaseCountdown(startedAt) {
            if (!startedAt) return;
            if (this.phaseCountdownInterval) clearInterval(this.phaseCountdownInterval);
            this.phaseCountdownStarted = new Date(startedAt + 'Z').getTime();
            this.phaseCountdown = 45;
            this.phaseCountdownInterval = setInterval(() => {
                const now = Date.now();
                const elapsed = Math.floor((now - this.phaseCountdownStarted) / 1000);
                this.phaseCountdown = Math.max(0, 45 - elapsed);
                if (this.phaseCountdown <= 0) {
                    clearInterval(this.phaseCountdownInterval);
                }
            }, 250);
        }
    }"
    x-on:play-bongg.window="playBongg()"
    x-on:show-notification.window="
        $nextTick(() => {
            setTimeout(() => { $wire.clearNotification() }, 8000);
        });
    "
>
    {{-- Notification Modal --}}
    @if ($notification)
        <div
            x-data="{ 
                show: true,
                progress: 100,
                startCountdown() {
                    this.progress = 100;
                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => {
                            this.progress = 0;
                        });
                    });
                }
            }"
            x-init="startCountdown()"
            x-show="show"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
            role="dialog"
            aria-modal="true"
        >
            {{-- Backdrop --}}
            <div 
                x-show="show"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-zinc-950/60 backdrop-blur-md"
                aria-hidden="true"
            ></div>

            {{-- Modal Content --}}
            <div
                x-show="show"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="translate-y-12 opacity-0 scale-95"
                x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="translate-y-0 opacity-100 scale-100"
                x-transition:leave-end="translate-y-12 opacity-0 scale-95"
                class="relative w-full max-w-lg overflow-hidden rounded-[2.5rem] border border-zinc-200/50 bg-white/95 shadow-2xl backdrop-blur-2xl dark:border-white/10 dark:bg-zinc-900/95"
            >
                {{-- Sidebar Progress Bar --}}
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-zinc-100 dark:bg-zinc-800">
                    <div
                        class="w-full bg-linear-to-b from-amber-400 to-amber-600 transition-all ease-linear"
                        :style="`height: ${progress}%; transition-duration: 8000ms;`"
                    ></div>
                </div>

                <div class="flex items-start gap-6 p-8 pl-10">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-3xl bg-amber-500/10 text-4xl shadow-inner dark:bg-amber-500/20">
                        @if (str_contains(strtolower($notification['title']), 'şafak') || str_contains(strtolower($notification['title']), 'dawn'))
                            🌅
                        @elseif (str_contains(strtolower($notification['title']), 'gündüz') || str_contains(strtolower($notification['title']), 'day'))
                            ☀️
                        @elseif (str_contains(strtolower($notification['title']), 'oylama') || str_contains(strtolower($notification['title']), 'voting'))
                            ⚖️
                        @else
                            🔔
                        @endif
                    </div>
                    <div class="flex-1 pt-1">
                        <h3 class="cyber-title text-2xl font-black tracking-tight text-zinc-950 dark:text-white">
                            {{ $notification['title'] }}
                        </h3>
                        <p class="mt-2 text-lg leading-relaxed text-zinc-600 dark:text-zinc-300">
                            {{ $notification['message'] }}
                        </p>
                    </div>
                    <button 
                        @click="show = false; $wire.clearNotification()" 
                        class="shrink-0 -mr-2 -mt-2 rounded-2xl p-2 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-white/5 dark:hover:text-zinc-200 transition-colors"
                        aria-label="Kapat"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                {{-- Action Suggestion (Atmospheric) --}}
                <div class="border-t border-zinc-200/50 bg-zinc-50/50 px-8 py-4 dark:border-white/5 dark:bg-white/5">
                    <p class="font-mono text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500">
                        {{ __('vampire.log.continue_game') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if ($roomMissing)
        <x-games.room-missing-state
            :back-url="$backUrl"
            :back-label="__('vampire.back')"
            :room-code-label="__('vampire.roomCode')"
            :title="__('vampire.roomNotFound')"
            :description="__('vampire.roomNotFoundDesc')"
            accent="red"
        />
    @else
        <button
            type="button"
            wire:click="leave"
            class="touch-manipulation inline-flex w-fit items-center justify-center gap-2 rounded-2xl border border-zinc-200 bg-white/75 px-5 py-2.5 text-sm font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:border-white/8 dark:bg-zinc-950/40 dark:text-white dark:hover:bg-zinc-950/55 dark:focus-visible:ring-red-300/30"
        >
            <span aria-hidden="true" class="text-zinc-500 dark:text-zinc-400">&larr;</span>
            {{ $isJoined ? __('vampire.leave') : __('vampire.back') }}
        </button>

        <div
            class="relative overflow-hidden rounded-[2.5rem] border border-zinc-200 bg-white/80 shadow-sm backdrop-blur dark:border-white/8 dark:bg-zinc-950/80"
            @if ($status === \App\Enums\VampireRoomStatus::Lobby)
                wire:poll.2s="refreshRoom"
            @else
                wire:poll.5s="refreshRoom"
            @endif
        >
            <div class="pointer-events-none absolute inset-0">
                @php
                    $stageClass = match($status) {
                        \App\Enums\VampireRoomStatus::Night        => 'vampire-stage-night',
                        \App\Enums\VampireRoomStatus::Day          => 'vampire-stage-day',
                        \App\Enums\VampireRoomStatus::DayVoting    => 'vampire-stage-voting',
                        \App\Enums\VampireRoomStatus::Dawn         => 'vampire-stage-day opacity-80',
                        \App\Enums\VampireRoomStatus::HunterLastShot => 'vampire-stage-voting opacity-60',
                        default => 'imposter-stage opacity-[0.40]',
                    };
                @endphp
                <div class="absolute inset-0 {{ $stageClass }}"></div>
                <div class="absolute inset-0 window-scanline"></div>
                <div class="absolute inset-0 cyber-noise"></div>
                <div class="absolute inset-0 imposter-vignette"></div>
            </div>

            {{-- Toolbar --}}
            <div class="relative border-b border-zinc-200/50 bg-white/40 px-4 py-3 backdrop-blur sm:px-6 z-20 dark:border-white/5 dark:bg-zinc-950/60">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            type="button"
                            wire:click="openPlayers"
                            class="touch-manipulation inline-flex items-center gap-2 rounded-full border border-zinc-200 bg-white/75 px-4 py-2 text-xs font-semibold text-zinc-700 backdrop-blur transition hover:bg-white hover:border-red-300 hover:text-zinc-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:border-white/8 dark:bg-zinc-950/40 dark:text-zinc-300 dark:hover:bg-zinc-950/60 dark:hover:border-red-400/30 dark:hover:text-white dark:focus-visible:ring-red-300/30"
                            title="{{ __('vampire.players') }}"
                        >
                            <span class="inline-flex h-2 w-2 rounded-full bg-red-400 shadow-[0_0_0_4px_rgba(248,113,113,0.18)]"></span>
                            @if ($status === \App\Enums\VampireRoomStatus::Lobby)
                                {{ __('vampire.roomCode') }}
                                <span class="font-mono tracking-widest text-zinc-950 dark:text-white">{{ $roomCode }}</span>
                            @else
                                {{ __('vampire.players') }}
                            @endif
                            <svg class="h-3.5 w-3.5 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </button>

                        @if ($status === \App\Enums\VampireRoomStatus::Lobby)
                            <button
                                type="button"
                                class="touch-manipulation inline-flex items-center justify-center rounded-xl border border-zinc-200 bg-white/75 px-3 py-2 text-xs font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:border-white/8 dark:bg-zinc-950/40 dark:text-white dark:hover:bg-zinc-950/55 dark:focus-visible:ring-red-300/30"
                                data-copy-text="{{ $roomCode }}"
                                data-copy-label="{{ __('vampire.copyCode') }}"
                                data-copy-label-success="{{ __('portfolio.ui.copied') }}"
                            >
                                {{ __('vampire.copyCode') }}
                            </button>

                            <button
                                type="button"
                                class="touch-manipulation inline-flex items-center justify-center rounded-xl border border-zinc-200 bg-white/75 px-3 py-2 text-xs font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:border-white/8 dark:bg-zinc-950/40 dark:text-white dark:hover:bg-zinc-950/55 dark:focus-visible:ring-red-300/30"
                                data-copy-text="{{ $roomUrl }}"
                                data-copy-label="{{ __('vampire.copyLink') }}"
                                data-copy-label-success="{{ __('portfolio.ui.copied') }}"
                            >
                                {{ __('vampire.copyLink') }}
                            </button>

                            <div x-data="{ openQr: false }" class="relative z-50">
                                <button
                                    type="button"
                                    @click="openQr = !openQr"
                                    class="touch-manipulation inline-flex items-center justify-center rounded-xl border border-zinc-200 bg-white/75 p-2 text-zinc-600 shadow-sm backdrop-blur transition hover:bg-white hover:text-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:border-white/8 dark:bg-zinc-950/40 dark:text-zinc-400 dark:hover:bg-zinc-950/55 dark:hover:text-white dark:focus-visible:ring-red-300/30"
                                    title="QR Kod"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                </button>
                                
                                <div
                                    x-show="openQr"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    @click.away="openQr = false"
                                    x-cloak
                                    class="absolute left-0 mt-2 z-50 w-max rounded-2xl border border-zinc-200/80 bg-white/95 p-3 shadow-xl backdrop-blur-xl dark:border-white/8 dark:bg-zinc-900/90"
                                >
                                    <div class="relative">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($roomUrl) }}&color=09090b" alt="QR Code" class="h-48 w-48 rounded-xl bg-white dark:hidden">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($roomUrl) }}&color=fafafa&bgcolor=09090b" alt="QR Code" class="hidden h-48 w-48 rounded-xl bg-zinc-950 dark:block">
                                        
                                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                            <div class="rounded-lg bg-zinc-950 p-1.5 text-white shadow-sm dark:bg-white dark:text-zinc-950">
                                                <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($roomPassword !== null)
                                <div x-data="{ showPassword: false }" class="inline-flex items-center gap-1.5 rounded-xl border border-zinc-200 bg-white/75 pl-3 pr-2 py-1.5 text-xs font-semibold text-zinc-600 shadow-sm backdrop-blur dark:border-white/8 dark:bg-zinc-950/40 dark:text-zinc-300">
                                    <svg class="h-3.5 w-3.5 shrink-0 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                    <span class="font-mono tracking-wide" x-text="showPassword ? '{{ $roomPassword }}' : '••••••••'"></span>
                                    <button type="button" x-on:click="showPassword = !showPassword" class="ml-1 p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 focus:outline-none" title="Şifreyi Göster/Gizle">
                                        <svg x-show="!showPassword" class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        <svg x-cloak x-show="showPassword" class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Host action buttons --}}
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($isHost && $status === \App\Enums\VampireRoomStatus::Lobby)
                            <button
                                type="button"
                                wire:click="startGame"
                                class="touch-manipulation inline-flex items-center justify-center gap-2 rounded-2xl bg-zinc-950 px-5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-zinc-950/10 transition hover:bg-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100 dark:focus-visible:ring-red-300/30"
                            >
                                {{ __('vampire.start') }}
                                <span aria-hidden="true" class="text-white/70 dark:text-zinc-500">&rarr;</span>
                            </button>
                        @elseif ($isHost && $status === \App\Enums\VampireRoomStatus::Night)
                            <button
                                type="button"
                                wire:click="resolveDawn"
                                class="touch-manipulation inline-flex items-center justify-center gap-2 rounded-2xl bg-zinc-950 px-5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-zinc-950/10 transition hover:bg-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100 dark:focus-visible:ring-red-300/30"
                            >
                                {{ __('vampire.night.resolveDawn') }}
                                <span aria-hidden="true" class="text-white/70 dark:text-zinc-500">&rarr;</span>
                            </button>
                        @elseif ($isHost && $status === \App\Enums\VampireRoomStatus::Dawn)
                            <button
                                type="button"
                                wire:click="startDay"
                                class="touch-manipulation inline-flex items-center justify-center gap-2 rounded-2xl bg-zinc-950 px-5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-zinc-950/10 transition hover:bg-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100 dark:focus-visible:ring-red-300/30"
                            >
                                {{ __('vampire.dawn.startDay') }}
                                <span aria-hidden="true" class="text-white/70 dark:text-zinc-500">&rarr;</span>
                            </button>
                        @elseif ($isHost && $status === \App\Enums\VampireRoomStatus::Day)
                            <button
                                type="button"
                                wire:click="startDayVoting"
                                class="touch-manipulation inline-flex items-center justify-center gap-2 rounded-2xl bg-zinc-950 px-5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-zinc-950/10 transition hover:bg-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100 dark:focus-visible:ring-red-300/30"
                            >
                                {{ __('vampire.day.startVoting') }}
                                <span aria-hidden="true" class="text-white/70 dark:text-zinc-500">&rarr;</span>
                            </button>
                        @elseif ($isHost && $status === \App\Enums\VampireRoomStatus::DayVoting)
                            <button
                                type="button"
                                wire:click="revealDayVotes"
                                class="touch-manipulation inline-flex items-center justify-center gap-2 rounded-2xl bg-zinc-950 px-5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-zinc-950/10 transition hover:bg-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100 dark:focus-visible:ring-red-300/30"
                            >
                                {{ __('vampire.voting.reveal') }}
                                <span aria-hidden="true" class="text-white/70 dark:text-zinc-500">&rarr;</span>
                            </button>
                        @elseif ($isHost && $status === \App\Enums\VampireRoomStatus::DayResults)
                            @if ($dayResult !== null && ($dayResult['eliminatedId'] ?? null) !== null)
                                <button
                                    type="button"
                                    wire:click="confirmDayElimination(true)"
                                    class="touch-manipulation inline-flex items-center justify-center gap-2 rounded-2xl bg-zinc-950 px-5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-zinc-950/10 transition hover:bg-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100 dark:focus-visible:ring-red-300/30"
                                >
                                    {{ __('vampire.day.confirmElimination') }}
                                    <span aria-hidden="true" class="text-white/70 dark:text-zinc-500">&rarr;</span>
                                </button>
                                <button
                                    type="button"
                                    wire:click="confirmDayElimination(false)"
                                    class="touch-manipulation inline-flex items-center justify-center gap-2 rounded-2xl border border-zinc-200 bg-white/70 px-4 py-2.5 text-xs font-semibold text-zinc-700 shadow-sm backdrop-blur transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:border-white/8 dark:bg-zinc-950/35 dark:text-zinc-300 dark:hover:bg-zinc-950/55 dark:focus-visible:ring-red-300/30"
                                >
                                    {{ __('vampire.day.skipElimination') }}
                                </button>
                            @else
                                <button
                                    type="button"
                                    wire:click="confirmDayElimination(false)"
                                    class="touch-manipulation inline-flex items-center justify-center gap-2 rounded-2xl bg-zinc-950 px-5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-zinc-950/10 transition hover:bg-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100 dark:focus-visible:ring-red-300/30"
                                >
                                    {{ __('vampire.night.title', ['number' => $nightNumber + 1]) }}
                                    <span aria-hidden="true" class="text-white/70 dark:text-zinc-500">&rarr;</span>
                                </button>
                            @endif
                        @elseif ($isHost && $status === \App\Enums\VampireRoomStatus::GameOver)
                            <button
                                type="button"
                                wire:click="newRound"
                                class="touch-manipulation inline-flex items-center justify-center gap-2 rounded-2xl bg-zinc-950 px-5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-zinc-950/10 transition hover:bg-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100 dark:focus-visible:ring-red-300/30"
                            >
                                {{ __('vampire.gameover.newRound') }}
                                <span aria-hidden="true" class="text-white/70 dark:text-zinc-500">&rarr;</span>
                            </button>
                        @endif

                        @if ($isHost && in_array($status, [\App\Enums\VampireRoomStatus::Night, \App\Enums\VampireRoomStatus::Day, \App\Enums\VampireRoomStatus::Dawn, \App\Enums\VampireRoomStatus::DayVoting, \App\Enums\VampireRoomStatus::DayResults], true))
                            <button
                                type="button"
                                wire:click="declareGameOver"
                                class="touch-manipulation inline-flex items-center justify-center gap-2 rounded-2xl border border-zinc-200 bg-white/70 px-4 py-2.5 text-xs font-semibold text-zinc-700 shadow-sm backdrop-blur transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:border-white/8 dark:bg-zinc-950/35 dark:text-zinc-300 dark:hover:bg-zinc-950/55 dark:focus-visible:ring-red-300/30"
                            >
                                {{ __('vampire.gameover.declare') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="relative p-4 sm:p-6 lg:p-8">
                {{-- Phase Banner --}}
                @if (in_array($status, [\App\Enums\VampireRoomStatus::Night, \App\Enums\VampireRoomStatus::Day, \App\Enums\VampireRoomStatus::Dawn, \App\Enums\VampireRoomStatus::DayVoting, \App\Enums\VampireRoomStatus::HunterLastShot, \App\Enums\VampireRoomStatus::GameOver], true))
                    <div class="mb-8 flex flex-col items-center text-center">
                        @php
                            $phaseIcon = match($status) {
                                \App\Enums\VampireRoomStatus::Night        => '🌙',
                                \App\Enums\VampireRoomStatus::Dawn         => '🌅',
                                \App\Enums\VampireRoomStatus::Day          => '☀️',
                                \App\Enums\VampireRoomStatus::DayVoting    => '⚖️',
                                \App\Enums\VampireRoomStatus::HunterLastShot => '🎯',
                                \App\Enums\VampireRoomStatus::GameOver     => '🏆',
                                default => '🎭',
                            };
                            $phaseName = match($status) {
                                \App\Enums\VampireRoomStatus::Night        => __('vampire.night.title', ['number' => $nightNumber]),
                                \App\Enums\VampireRoomStatus::Dawn         => __('vampire.dawn.title'),
                                \App\Enums\VampireRoomStatus::Day          => __('vampire.day.title'),
                                \App\Enums\VampireRoomStatus::DayVoting    => __('vampire.voting.title'),
                                \App\Enums\VampireRoomStatus::HunterLastShot => __('vampire.hunter.title'),
                                \App\Enums\VampireRoomStatus::GameOver     => __('vampire.gameover.title'),
                                default => '',
                            };
                            $phaseColor = match($status) {
                                \App\Enums\VampireRoomStatus::Night        => 'text-indigo-400 dark:text-indigo-300',
                                \App\Enums\VampireRoomStatus::Dawn         => 'text-amber-400 dark:text-amber-300',
                                \App\Enums\VampireRoomStatus::Day          => 'text-yellow-500 dark:text-yellow-400',
                                \App\Enums\VampireRoomStatus::DayVoting    => 'text-rose-500 dark:text-rose-400',
                                \App\Enums\VampireRoomStatus::HunterLastShot => 'text-orange-500 dark:text-orange-400',
                                \App\Enums\VampireRoomStatus::GameOver     => 'text-emerald-500 dark:text-emerald-400',
                                default => 'text-zinc-500',
                            };
                        @endphp
                        <span class="mb-2 text-4xl drop-shadow-lg" aria-hidden="true">{{ $phaseIcon }}</span>
                        <h2 class="vampire-phase-banner text-2xl font-black uppercase tracking-widest {{ $phaseColor }}">
                            {{ $phaseName }}
                        </h2>
                        @if ($status === \App\Enums\VampireRoomStatus::Night && $nightPhase)
                            <p class="mt-1 font-mono text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500">
                                {{ __('vampire.night.phase.'.$nightPhase) }}
                            </p>
                        @endif
                        <div class="mt-4 h-px w-24 bg-linear-to-r from-transparent via-zinc-200/50 to-transparent dark:via-white/10"></div>
                    </div>
                @endif
                @php
                    $roleStyle = [
                        'card'   => "border-sky-200/60 bg-white/92 shadow-sky-900/10 dark:border-sky-400/15 dark:bg-zinc-950/88 dark:shadow-sky-900/20",
                        'header' => "border-sky-200/40 bg-sky-50/40 dark:border-sky-400/10 dark:bg-sky-950/15",
                        'body'   => "border-sky-200/30 dark:border-sky-400/10",
                        'action' => "border-sky-200/60 bg-sky-50/60 dark:border-sky-400/15 dark:bg-sky-950/20",
                    ];
                @endphp

                @if ($error)
                    <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50/80 px-4 py-3 text-sm font-semibold text-rose-700 backdrop-blur dark:border-rose-400/20 dark:bg-rose-950/30 dark:text-rose-200">
                        {{ $error }}
                    </div>
                @endif

                @if ($isJoined && $myRole !== null)
                    <div
                        class="mb-6 overflow-hidden rounded-3xl border shadow-xl backdrop-blur {{ $roleStyle['card'] }}"
                        role="region"
                        aria-label="{{ __('vampire.yourRole') }}"
                    >
                        <button
                            type="button"
                            x-on:click.stop.prevent="roleVisible = !roleVisible"
                            class="touch-manipulation flex w-full cursor-pointer items-start justify-between gap-4 border-b px-5 py-4 text-left backdrop-blur transition hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 focus-visible:ring-inset dark:focus-visible:ring-red-300/40 {{ $roleStyle['header'] }}"
                            :aria-expanded="roleVisible ? 'true' : 'false'"
                            aria-controls="vampire-role-card-body"
                            id="vampire-role-card-toggle"
                        >
                            <div>
                                <p class="text-sm font-semibold text-zinc-950 dark:text-white">{{ __('vampire.yourRole') }}</p>
                                <p class="mt-1 text-xs text-zinc-600 dark:text-zinc-300">{{ __('vampire.privateRole') }}</p>
                            </div>
                            <div class="flex shrink-0 items-center gap-3">
                                <span class="hidden text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 sm:inline">
                                    <span x-show="roleVisible">{{ __('vampire.roleHide') }}</span>
                                    <span x-show="!roleVisible" x-cloak>{{ __('vampire.roleShow') }}</span>
                                </span>
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl border border-zinc-200/80 bg-white/80 text-zinc-600 transition dark:border-white/8 dark:bg-zinc-800/60 dark:text-zinc-400" aria-hidden="true">
                                    <svg x-show="roleVisible" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    <svg x-show="!roleVisible" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" /></svg>
                                </span>
                            </div>
                        </button>

                        <div id="vampire-role-card-body" x-show="roleVisible" role="region" aria-labelledby="vampire-role-card-toggle">
                            <div class="border-t px-5 py-4 {{ $roleStyle['body'] }}">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">{{ __('vampire.youAre') }}</p>
                                        @php
                                            $roleNameMap = [
                                                \App\Enums\VampireRole::Vampire->value   => ['label' => __('vampire.roles.vampir'),   'class' => 'text-red-700 dark:text-red-300'],
                                                \App\Enums\VampireRole::Villager->value  => ['label' => __('vampire.roles.koylu'),    'class' => 'text-zinc-700 dark:text-zinc-200'],
                                                \App\Enums\VampireRole::Doctor->value    => ['label' => __('vampire.roles.doktor'),   'class' => 'text-emerald-700 dark:text-emerald-300'],
                                                \App\Enums\VampireRole::Detective->value => ['label' => __('vampire.roles.dedektif'), 'class' => 'text-sky-700 dark:text-sky-300'],
                                                \App\Enums\VampireRole::Hunter->value    => ['label' => __('vampire.roles.avci'),     'class' => 'text-amber-700 dark:text-amber-300'],
                                            ];
                                            $roleInfo = $roleNameMap[$myRole] ?? ['label' => $myRole, 'class' => 'text-zinc-700 dark:text-zinc-200'];
                                        @endphp
                                        <p class="cyber-title mt-1 text-2xl font-bold tracking-tight {{ $roleInfo['class'] }}">{{ $roleInfo['label'] }}</p>
                                    </div>
                                    @if (! $myAlive)
                                        <span class="rounded-2xl border border-zinc-200 bg-zinc-100/60 px-4 py-2 text-sm font-semibold text-zinc-500 dark:border-white/8 dark:bg-zinc-800/30 dark:text-zinc-400">{{ __('vampire.eliminated') }}</span>
                                    @elseif ($myAlignment === 'vampire')
                                        <span class="rounded-2xl border border-red-200/60 bg-red-50/60 px-4 py-2 text-sm font-semibold text-red-700 dark:border-red-400/15 dark:bg-red-950/20 dark:text-red-300">{{ __('vampire.roles.vampirTeam') }}</span>
                                    @else
                                        <span class="rounded-2xl border border-emerald-200/60 bg-emerald-50/60 px-4 py-2 text-sm font-semibold text-emerald-700 dark:border-emerald-400/15 dark:bg-emerald-950/20 dark:text-emerald-300">{{ __('vampire.roles.villagerTeam') }}</span>
                                    @endif
                                </div>

                                {{-- Detective result --}}
                                @if ($myRole === \App\Enums\VampireRole::Detective->value && $detectiveResult !== null && $myDetectiveTarget !== null)
                                    @php
                                        $targetName = collect($players)->firstWhere('id', $myDetectiveTarget)['name'] ?? '?';
                                        $isVampire = $detectiveResult === 'vampire';
                                    @endphp
                                    <div class="mt-3 rounded-2xl border {{ $isVampire ? 'border-red-200/50 bg-red-50/40 dark:border-red-400/20 dark:bg-red-950/20' : 'border-emerald-200/50 bg-emerald-50/40 dark:border-emerald-400/20 dark:bg-emerald-950/20' }} px-4 py-3">
                                        <p class="text-xs font-semibold {{ $isVampire ? 'text-red-700 dark:text-red-300' : 'text-emerald-700 dark:text-emerald-300' }}">
                                            {{ __('vampire.detective.result', ['name' => $targetName, 'result' => $isVampire ? __('vampire.detective.isVampire') : __('vampire.detective.isVillager')]) }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif


                {{-- Event Log --}}
                @if ($isJoined && count($eventLog) > 0 && ! in_array($status, [\App\Enums\VampireRoomStatus::Lobby, \App\Enums\VampireRoomStatus::Loading, \App\Enums\VampireRoomStatus::Missing]))
                    @php
                        $logBorderBg = match(true) {
                            $status === \App\Enums\VampireRoomStatus::Night                        => 'border-indigo-400/25 bg-indigo-950/20 dark:bg-indigo-950/25',
                            in_array($status, [\App\Enums\VampireRoomStatus::Dawn, \App\Enums\VampireRoomStatus::HunterLastShot]) => 'border-amber-400/25 bg-amber-950/20 dark:bg-amber-950/25',
                            in_array($status, [\App\Enums\VampireRoomStatus::Day, \App\Enums\VampireRoomStatus::DayVoting])       => 'border-yellow-400/25 bg-yellow-950/20 dark:bg-yellow-950/25',
                            $status === \App\Enums\VampireRoomStatus::DayResults                   => 'border-red-400/25 bg-red-950/20 dark:bg-red-950/25',
                            default                                                                 => 'border-zinc-600/40 bg-zinc-900/40',
                        };
                        $logDotClass = match(true) {
                            $status === \App\Enums\VampireRoomStatus::Night                        => 'bg-indigo-400',
                            in_array($status, [\App\Enums\VampireRoomStatus::Dawn, \App\Enums\VampireRoomStatus::HunterLastShot]) => 'bg-amber-400',
                            in_array($status, [\App\Enums\VampireRoomStatus::Day, \App\Enums\VampireRoomStatus::DayVoting])       => 'bg-yellow-400',
                            default                                                                 => 'bg-red-400',
                        };
                        $logLabelClass = match(true) {
                            $status === \App\Enums\VampireRoomStatus::Night                        => 'text-indigo-400',
                            in_array($status, [\App\Enums\VampireRoomStatus::Dawn, \App\Enums\VampireRoomStatus::HunterLastShot]) => 'text-amber-400',
                            in_array($status, [\App\Enums\VampireRoomStatus::Day, \App\Enums\VampireRoomStatus::DayVoting])       => 'text-yellow-500',
                            default                                                                 => 'text-red-400',
                        };
                        $alivePlayers = count(array_filter($players, fn($p) => $p['alive']));
                        $deadPlayers  = count($players) - $alivePlayers;
                    @endphp
                    <div class="mb-6 overflow-hidden rounded-3xl border shadow-sm backdrop-blur-md {{ $logBorderBg }}">
                        {{-- Header --}}
                        <div class="flex items-center justify-between border-b border-white/4 px-5 pb-3 pt-4">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex h-[7px] w-[7px] animate-pulse rounded-full {{ $logDotClass }}"></span>
                                <span class="font-mono text-[10px] font-bold uppercase tracking-[0.15em] {{ $logLabelClass }}">{{ __('vampire.eventLog') }}</span>
                            </div>
                            <span class="font-mono text-[10px] text-zinc-600 dark:text-zinc-500">{{ count($eventLog) }} {{ __('vampire.eventLogCount') }}</span>
                        </div>

                        {{-- Entries --}}
                        <div class="flex flex-col px-5 py-3 divide-y divide-white/4">
                            @php
                                $groupedHistory = collect($history)->groupBy('night');
                            @endphp
                            @forelse ($groupedHistory as $night => $events)
                                <div class="py-3 first:pt-0 last:pb-0">
                                    <div class="mb-3 flex items-center gap-2">
                                        <span class="font-mono text-[9px] font-black uppercase tracking-widest text-zinc-500">
                                            @if ($night == 0)
                                                {{ __('vampire.log.pre_game') }}
                                            @else
                                                {{ __('vampire.night.title', ['number' => $night]) }}
                                            @endif
                                        </span>
                                        <div class="h-px flex-1 bg-white/4"></div>
                                    </div>
                                    <div class="space-y-1.5">
                                        @foreach ($events as $h)
                                            <div class="flex items-start gap-3 rounded-xl px-1">
                                                <span class="mt-0.5 shrink-0 text-sm leading-none" aria-hidden="true">{{ $h['icon'] }}</span>
                                                <span class="text-[13px] leading-snug
                                                    @switch($h['type'])
                                                        @case('death')         text-red-500 font-bold dark:text-red-400 @break
                                                        @case('save')          text-emerald-500 font-bold dark:text-emerald-400 @break
                                                        @case('warning')       text-amber-400 font-medium @break
                                                        @case('gameover')      text-white font-black uppercase @break
                                                        @case('action')        text-indigo-400 font-medium dark:text-indigo-300 @break
                                                        @case('sub-narrative') ml-4 text-zinc-500 dark:text-zinc-400 text-xs @break
                                                        @default               text-zinc-600 dark:text-zinc-300
                                                    @endswitch
                                                ">{{ __($h['key'], $h['params'] ?? []) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="py-4 text-center text-xs text-zinc-500">{{ __('vampire.log.empty') }}</div>
                            @endforelse
                        </div>

                        {{-- Player count footer --}}
                        @if ($deadPlayers > 0)
                            <div class="flex items-center gap-3 border-t border-white/4 px-5 pb-3 pt-2 font-mono text-[10px]">
                                <span class="text-emerald-600 dark:text-emerald-700">● {{ $alivePlayers }} {{ __('vampire.alive') }}</span>
                                <span class="text-zinc-700">·</span>
                                <span class="text-red-900 dark:text-red-900">✝ {{ $deadPlayers }} {{ __('vampire.dead') }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Lobby --}}
                @if ($status === \App\Enums\VampireRoomStatus::Lobby)
                    <div class="grid gap-4 lg:grid-cols-12">
                        <div class="lg:col-span-7">
                            <div class="rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-md dark:border-white/8 dark:bg-zinc-900/60">
                                <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">{{ __('vampire.howToPlay') }}</p>
                                <div class="mt-4 space-y-3">
                                    @foreach ([__('vampire.rulesLine1'), __('vampire.rulesLine2'), __('vampire.rulesLine3'), __('vampire.rulesLine4')] as $i => $rule)
                                        <div class="flex gap-3 text-sm text-zinc-700 dark:text-zinc-200">
                                            <span class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-zinc-200/80 bg-white/80 font-mono text-[11px] font-semibold text-zinc-500 dark:border-white/8 dark:bg-zinc-800/60 dark:text-zinc-400">{{ $i + 1 }}</span>
                                            <p class="pt-0.5">{{ $rule }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-4 rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-md dark:border-white/8 dark:bg-zinc-900/60">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400 shadow-[0_0_0_4px_rgba(52,211,153,0.16)]"></span>
                                    <p class="text-sm font-semibold text-zinc-950 dark:text-white">{{ __('vampire.lobby') }}</p>
                                    @if ($isJoined)
                                        <button wire:click="bongg" type="button" class="ml-auto flex shrink-0 items-center gap-1.5 rounded-xl border border-red-200/50 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100 focus:outline-none dark:border-red-400/20 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-900/60" title="Oyuncuları Dürtt!">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                            Bongg!
                                        </button>
                                    @endif
                                </div>
                                <p class="mt-3 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">
                                    {{ $isJoined ? __('vampire.waitingForStartDesc') : __('vampire.joinThisRoomDesc') }}
                                </p>

                                @if (count($players) > 0)
                                    <div class="mt-4 space-y-1">
                                        @foreach ($players as $player)
                                            @php $playerBg = \App\View\PlayerColors::all($player['color'])['bg']; @endphp
                                            <div class="flex min-w-0 items-center gap-2 rounded-2xl px-2 py-1 {{ $player['isMe'] ? $playerBg : 'bg-zinc-50 dark:bg-white/5' }}">
                                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-lg {{ \App\View\PlayerColors::avatar($player['color']) }}">{{ $player['emoji'] }}</span>
                                                <span class="min-w-0 truncate text-xs font-semibold text-zinc-800 dark:text-zinc-200">{{ $player['name'] }}</span>
                                                @if ($player['isHost'])
                                                    <span class="ml-auto shrink-0 h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                                @elseif ($player['isMe'])
                                                    <span class="ml-auto shrink-0 rounded-full bg-red-400/15 px-1.5 py-0.5 font-mono text-[9px] font-semibold text-red-600 dark:text-red-300">Ben</span>
                                                @elseif ($isHost && $status === \App\Enums\VampireRoomStatus::Lobby)
                                                    <button
                                                        type="button"
                                                        wire:click="openKickConfirm('{{ $player['id'] }}', '{{ addslashes($player['name']) }}')"
                                                        class="ml-auto flex h-7 w-7 items-center justify-center rounded-lg text-zinc-400 transition-colors hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-950/30"
                                                        title="{{ __('vampire.kick') }}"
                                                    >
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                    </button>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="lg:col-span-5">
                            @if ($isHost)
                                {{-- Config panel --}}
                                <div class="rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-md dark:border-white/8 dark:bg-zinc-900/60">
                                    <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">{{ __('vampire.config.title') }}</p>
                                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('vampire.config.hint') }}</p>

                                    <div class="mt-4 space-y-3">
                                        <div class="flex items-center justify-between gap-4">
                                            <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ __('vampire.roles.vampir') }}</label>
                                            <input
                                                wire:model.live="configVampireCount"
                                                wire:change="updateConfig"
                                                type="number"
                                                min="1"
                                                max="8"
                                                class="w-20 rounded-xl border border-zinc-200 bg-white/80 px-3 py-2 text-center text-sm font-semibold text-zinc-950 outline-none transition focus-visible:ring-2 focus-visible:ring-red-400/60 dark:border-white/8 dark:bg-zinc-800/50 dark:text-white dark:focus-visible:ring-red-300/30"
                                            />
                                        </div>
                                        <div class="flex items-center justify-between gap-4">
                                            <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">{{ __('vampire.roles.koylu') }}</label>
                                            <input
                                                wire:model.live="configVillagerCount"
                                                wire:change="updateConfig"
                                                type="number"
                                                min="1"
                                                max="12"
                                                class="w-20 rounded-xl border border-zinc-200 bg-white/80 px-3 py-2 text-center text-sm font-semibold text-zinc-950 outline-none transition focus-visible:ring-2 focus-visible:ring-red-400/60 dark:border-white/8 dark:bg-zinc-800/50 dark:text-white dark:focus-visible:ring-red-300/30"
                                            />
                                        </div>

                                        <div class="border-t border-zinc-200/50 pt-3 dark:border-white/10">
                                            <p class="mb-2 text-xs font-semibold text-zinc-500 dark:text-zinc-400">{{ __('vampire.config.specialRoles') }}</p>
                                            @foreach ([
                                                ['field' => 'configHasDoktor', 'label' => __('vampire.roles.doktor'), 'color' => 'emerald'],
                                                ['field' => 'configHasDedektif', 'label' => __('vampire.roles.dedektif'), 'color' => 'sky'],
                                                ['field' => 'configHasAvci', 'label' => __('vampire.roles.avci'), 'color' => 'amber'],
                                            ] as $toggle)
                                                <label class="flex cursor-pointer items-center justify-between gap-3 rounded-2xl border border-zinc-200/80 bg-white/60 px-4 py-3 text-sm font-semibold text-zinc-700 transition hover:bg-white/80 dark:border-white/[0.08] dark:bg-zinc-800/40 dark:text-zinc-300 dark:hover:bg-zinc-800/60 mt-2 first:mt-0">
                                                    {{ $toggle['label'] }}
                                                    <input
                                                        wire:model.live="{{ $toggle['field'] }}"
                                                        wire:change="updateConfig"
                                                        type="checkbox"
                                                        class="h-4 w-4 rounded border-zinc-300 text-red-600 focus:ring-red-500 dark:border-zinc-600 dark:bg-zinc-700"
                                                    />
                                                </label>
                                            @endforeach
                                        </div>

                                        @php
                                            $specialCount = ($configHasDoktor ? 1 : 0) + ($configHasDedektif ? 1 : 0) + ($configHasAvci ? 1 : 0);
                                            $totalRequired = $configVampireCount + $configVillagerCount + $specialCount;
                                            $totalPlayers = count($players);
                                            $configMatch = $totalRequired === $totalPlayers;
                                        @endphp
                                        <div class="mt-3 rounded-2xl border {{ $configMatch ? 'border-emerald-200/50 bg-emerald-50/40 dark:border-emerald-400/20 dark:bg-emerald-950/20' : 'border-amber-200/50 bg-amber-50/40 dark:border-amber-400/20 dark:bg-amber-950/20' }} px-4 py-3">
                                            <p class="text-xs font-semibold {{ $configMatch ? 'text-emerald-700 dark:text-emerald-300' : 'text-amber-700 dark:text-amber-300' }}">
                                                {{ __('vampire.config.total', ['required' => $totalRequired, 'players' => $totalPlayers]) }}
                                                @if ($configMatch)
                                                    — {{ __('vampire.config.ok') }}
                                                @else
                                                    — {{ __('vampire.config.mismatch') }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <livewire:games.identity />
                            @elseif (! $isJoined)
                                <div class="rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-md dark:border-white/8 dark:bg-zinc-900/60">
                                    @if ($status !== \App\Enums\VampireRoomStatus::Lobby)
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border border-amber-200/80 bg-amber-100/80 dark:border-amber-400/20 dark:bg-amber-900/30">
                                                <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </span>
                                            <div>
                                                <h3 class="cyber-title text-base font-bold text-zinc-950 dark:text-white">{{ __('vampire.gameAlreadyStarted') }}</h3>
                                                <p class="mt-1 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">{{ __('vampire.gameAlreadyStartedDesc') }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <h3 class="cyber-title text-base font-bold text-zinc-950 dark:text-white">{{ __('vampire.joinThisRoom') }}</h3>
                                        <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">{{ __('vampire.joinThisRoomDesc') }}</p>

                                        <div class="mt-6 space-y-3">
                                            <div class="-mt-4">
                                                <livewire:games.identity />
                                            </div>

                                            @if ($isPasswordProtected)
                                                <label class="block">
                                                    <span class="mb-1.5 block text-xs font-semibold text-zinc-600 dark:text-zinc-400">{{ __('vampire.password') }}</span>
                                                    <input
                                                        wire:model.live="joinPassword"
                                                        type="password"
                                                        autocomplete="off"
                                                        spellcheck="false"
                                                        class="w-full rounded-2xl border bg-white/70 px-4 py-3 text-sm font-semibold text-zinc-950 shadow-sm outline-none transition placeholder:text-zinc-400 @error('joinPassword') border-rose-500 focus:border-rose-500 focus-visible:ring-rose-400/60 dark:border-rose-500/50 dark:focus:border-rose-500/50 dark:focus-visible:ring-rose-300/30 @else border-zinc-200 focus:border-zinc-300 focus-visible:ring-2 focus-visible:ring-red-400/60 dark:border-white/8 dark:focus:border-white/20 dark:focus-visible:ring-red-300/30 @enderror dark:bg-zinc-950/35 dark:text-white dark:placeholder:text-zinc-500"
                                                        placeholder="{{ __('vampire.placeholder.password') }}"
                                                    />
                                                    @error('joinPassword')
                                                        <span class="mt-1.5 block text-xs font-medium text-rose-600 dark:text-rose-400">{{ $message }}</span>
                                                    @enderror
                                                </label>
                                            @endif

                                            @if ($error)
                                                <div class="rounded-2xl border border-rose-200 bg-rose-50/80 px-4 py-3 text-sm font-semibold text-rose-700 dark:border-rose-400/20 dark:bg-rose-950/30 dark:text-rose-200">{{ $error }}</div>
                                            @endif

                                            <button
                                                type="button"
                                                wire:click="join"
                                                @disabled(!\App\Support\GameIdentity::exists())
                                                class="touch-manipulation inline-flex w-full items-center justify-center gap-3 rounded-2xl bg-zinc-950 px-8 py-4 text-base font-semibold text-white shadow-lg ring-1 ring-zinc-950/10 transition hover:bg-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100 dark:focus-visible:ring-red-300/30"
                                            >
                                                {{ __('vampire.join') }}
                                                <span aria-hidden="true" class="text-white/70 dark:text-zinc-500">→</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-md dark:border-white/8 dark:bg-zinc-900/60">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex h-2.5 w-2.5 animate-pulse rounded-full bg-red-400"></span>
                                        <h3 class="cyber-title text-base font-bold text-zinc-950 dark:text-white">{{ __('vampire.waitingForStart') }}</h3>
                                    </div>
                                    <p class="mt-3 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">{{ __('vampire.waitingForStartDesc') }}</p>
                                </div>
                                <livewire:games.identity />
                            @endif
                        </div>
                    </div>

                {{-- Night Phase --}}
                @elseif ($status === \App\Enums\VampireRoomStatus::Night)
                    <div class="space-y-4">
                        <div class="rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-md dark:border-white/8 dark:bg-zinc-900/60">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-2.5 w-2.5 animate-pulse rounded-full bg-indigo-400"></span>
                                <p class="text-sm font-semibold text-zinc-950 dark:text-white">
                                    {{ __('vampire.night.title', ['number' => $nightNumber]) }}
                                </p>
                            </div>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ __('vampire.night.desc') }}</p>
                        </div>

                        {{-- Action Panels --}}
                        @if ($myAlive)
                            {{-- Vampire voting panel --}}
                            @if ($myAlignment === 'vampire')
                                <div class="rounded-3xl border shadow-sm backdrop-blur-md {{ $roleStyle['action'] }} p-6">
                                    <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.18em] text-red-600/70 dark:text-red-300/60">{{ __('vampire.night.vampireVote') }}</p>
                                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ __('vampire.night.vampireVoteDesc') }}</p>

                                    <div class="mt-4 space-y-2">
                                        @foreach ($players as $player)
                                            @if (! $player['isMe'] && $player['alive'])
                                                <button
                                                    wire:key="night-action-{{ $player['id'] }}"
                                                    type="button"
                                                    wire:click="castNightVote('{{ $player['id'] }}')"
                                                    class="touch-manipulation flex w-full items-center justify-between gap-4 rounded-2xl border px-4 py-3.5 text-left text-sm font-semibold shadow-sm backdrop-blur transition focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:focus-visible:ring-red-300/30 {{ $myNightVote === $player['id'] ? 'border-red-400/40 bg-red-50/50 text-red-900 ring-1 ring-red-400/30 dark:border-red-400/15 dark:bg-red-500/[0.06] dark:text-red-100 dark:ring-red-300/10' : 'border-zinc-200/80 bg-white/60 text-zinc-900 hover:bg-white/80 hover:ring-1 hover:ring-red-400/30 dark:border-white/[0.08] dark:bg-zinc-800/40 dark:text-white dark:hover:bg-zinc-800/60 dark:hover:ring-red-400/20' }}"
                                                >
                                                    <span class="flex min-w-0 items-center gap-3">
                                                        <div class="relative">
                                                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-base {{ \App\View\PlayerColors::avatar($player['color']) }}">{{ $player['emoji'] }}</span>
                                                            @if ($player['isVampire'])
                                                                <span class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[10px] text-white ring-2 ring-white dark:ring-zinc-900" title="{{ __('vampire.vampire') }}">🧛</span>
                                                            @endif
                                                        </div>
                                                        <div class="flex flex-col min-w-0">
                                                            <span class="min-w-0 truncate">{{ $player['name'] }}</span>
                                                            @if ($player['isVampire'])
                                                                <span class="text-[10px] font-bold uppercase tracking-wider text-red-600 dark:text-red-400">{{ __('vampire.vampire') }}</span>
                                                            @endif
                                                            @if ($player['interrogated'] && isset($detectiveInvestigationResults[$player['id']]))
                                                                @php $investigationResult = $detectiveInvestigationResults[$player['id']]; @endphp
                                                                <span class="text-[10px] font-semibold {{ $investigationResult === 'vampire' ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                                                    {{ $investigationResult === 'vampire' ? __('vampire.detective.result.vampire') : __('vampire.detective.result.villager') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </span>
                                                    <div class="flex items-center gap-3">
                                                        @if (isset($nightVoteCounts[$player['id']]) && $nightVoteCounts[$player['id']] > 0)
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700 dark:bg-red-900/30 dark:text-red-300">
                                                                <span class="h-1.5 w-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                                                {{ $nightVoteCounts[$player['id']] }}
                                                            </span>
                                                        @endif
                                                        <span class="font-mono text-[11px] font-semibold {{ $myNightVote === $player['id'] ? 'text-red-600 dark:text-red-300' : 'text-zinc-400 dark:text-zinc-500' }}">
                                                            {{ $myNightVote === $player['id'] ? __('vampire.voting.selected') : __('vampire.voting.select') }}
                                                        </span>
                                                    </div>
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Doctor protection panel --}}
                            @if ($myRole === \App\Enums\VampireRole::Doctor->value)
                                <div class="rounded-3xl border shadow-sm backdrop-blur-md {{ $roleStyle['action'] }} p-6">
                                    <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.18em] text-emerald-600/70 dark:text-emerald-300/60">{{ __('vampire.night.doctorProtect') }}</p>
                                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ __('vampire.night.doctorProtectDesc') }}</p>

                                    <div class="mt-4 space-y-2">
                                        @foreach ($players as $player)
                                            @if ($player['alive'])
                                                <button
                                                    type="button"
                                                    wire:click="doctorProtect('{{ $player['id'] }}')"
                                                    class="touch-manipulation flex w-full items-center justify-between gap-4 rounded-2xl border px-4 py-3.5 text-left text-sm font-semibold shadow-sm backdrop-blur transition focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400/60 dark:focus-visible:ring-emerald-300/30 {{ $myDoctorTarget === $player['id'] ? 'border-emerald-400/40 bg-emerald-50/50 ring-1 ring-emerald-400/30 dark:border-emerald-400/15 dark:bg-emerald-500/[0.06] dark:ring-emerald-300/10' : 'border-zinc-200/80 bg-white/60 hover:bg-white/80 hover:ring-1 hover:ring-emerald-400/30 dark:border-white/[0.08] dark:bg-zinc-800/40 dark:hover:bg-zinc-800/60 dark:hover:ring-emerald-400/20' }} text-zinc-900 dark:text-white"
                                                >
                                                    <span class="flex min-w-0 items-center gap-3">
                                                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-base {{ \App\View\PlayerColors::avatar($player['color']) }}">{{ $player['emoji'] }}</span>
                                                        <span class="min-w-0 truncate">{{ $player['name'] }}</span>
                                                    </span>
                                                    <span class="font-mono text-[11px] font-semibold {{ $myDoctorTarget === $player['id'] ? 'text-emerald-600 dark:text-emerald-300' : 'text-zinc-400 dark:text-zinc-500' }}">{{ $myDoctorTarget === $player['id'] ? __('vampire.voting.selected') : __('vampire.voting.select') }}</span>
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Detective query panel --}}
                            @if ($myRole === \App\Enums\VampireRole::Detective->value)
                                <div class="rounded-3xl border shadow-sm backdrop-blur-md {{ $roleStyle['action'] }} p-6">
                                    <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.18em] text-sky-600/70 dark:text-sky-300/60">{{ __('vampire.night.detectiveQuery') }}</p>
                                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ __('vampire.night.detectiveQueryDesc') }}</p>

                                    <div class="mt-4 space-y-2">
                                        @foreach ($players as $player)
                                            @if (! $player['isMe'] && $player['alive'] && ! $player['interrogated'])
                                                <button
                                                    type="button"
                                                    wire:click="detectiveQuery('{{ $player['id'] }}')"
                                                    class="touch-manipulation flex w-full items-center justify-between gap-4 rounded-2xl border px-4 py-3.5 text-left text-sm font-semibold shadow-sm backdrop-blur transition focus:outline-none focus-visible:ring-2 focus-visible:ring-sky-400/60 dark:focus-visible:ring-sky-300/30 {{ $myDetectiveTarget === $player['id'] ? 'border-sky-400/40 bg-sky-50/50 ring-1 ring-sky-400/30 dark:border-sky-400/15 dark:bg-sky-500/[0.06] dark:ring-sky-300/10' : 'border-zinc-200/80 bg-white/60 hover:bg-white/80 hover:ring-1 hover:ring-sky-400/30 dark:border-white/8 dark:bg-zinc-800/40 dark:hover:bg-zinc-800/60 dark:hover:ring-sky-400/20' }} text-zinc-900 dark:text-white"
                                                >
                                                    <span class="flex min-w-0 items-center gap-3">
                                                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-base {{ \App\View\PlayerColors::avatar($player['color']) }}">{{ $player['emoji'] }}</span>
                                                        <span class="min-w-0 truncate">{{ $player['name'] }}</span>
                                                    </span>
                                                    <span class="font-mono text-[11px] font-semibold {{ $myDetectiveTarget === $player['id'] ? 'text-sky-600 dark:text-sky-300' : 'text-zinc-400 dark:text-zinc-500' }}">{{ $myDetectiveTarget === $player['id'] ? __('vampire.voting.selected') : __('vampire.voting.select') }}</span>
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if ($myRole === \App\Enums\VampireRole::Villager->value)
                                <div class="rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-md dark:border-white/8 dark:bg-zinc-900/60">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex h-2.5 w-2.5 animate-pulse rounded-full bg-indigo-400"></span>
                                        <p class="text-sm font-semibold text-zinc-950 dark:text-white">{{ __('vampire.night.waiting') }}</p>
                                    </div>
                                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ __('vampire.night.waitingDesc') }}</p>
                                </div>
                            @endif
                        @else
                            <div class="rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-md dark:border-white/8 dark:bg-zinc-900/60">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-2.5 w-2.5 animate-pulse rounded-full bg-zinc-400"></span>
                                    <p class="text-sm font-semibold text-zinc-950 dark:text-white">{{ __('vampire.night.waiting') }}</p>
                                </div>
                                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ __('vampire.night.waitingDesc') }}</p>
                            </div>
                        @endif
                    </div>


                {{-- Dawn --}}
                @elseif ($status === \App\Enums\VampireRoomStatus::Dawn)
                    <div class="space-y-4">
                        <div class="rounded-3xl border border-amber-200/60 bg-gradient-to-br from-amber-50/60 to-white/70 p-6 shadow-sm backdrop-blur-md dark:border-amber-400/20 dark:from-amber-950/30 dark:to-zinc-900/60">
                            <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-600/70 dark:text-amber-300/60">{{ __('vampire.dawn.title') }}</p>

                            @if ($nightResult !== null)
                                @if (($nightResult['killedId'] ?? null) !== null && ($nightResult['savedByName'] ?? null) === null)
                                    <p class="cyber-title mt-2 text-xl font-bold text-zinc-950 dark:text-white">
                                        {{ __('vampire.dawn.killed', ['name' => $nightResult['killedName'] ?? '?']) }}
                                    </p>
                                @elseif (($nightResult['killedId'] ?? null) !== null && ($nightResult['savedByName'] ?? null) !== null)
                                    <p class="cyber-title mt-2 text-xl font-bold text-zinc-950 dark:text-white">
                                        {{ __('vampire.dawn.savedBy', ['name' => $nightResult['savedByName']]) }}
                                    </p>
                                @else
                                    <p class="cyber-title mt-2 text-xl font-bold text-zinc-950 dark:text-white">{{ __('vampire.dawn.noKill') }}</p>
                                @endif
                            @else
                                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ __('vampire.dawn.desc') }}</p>
                            @endif
                        </div>

                        {{-- Living players summary --}}
                        <div class="rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-md dark:border-white/8 dark:bg-zinc-900/60">
                            <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">{{ __('vampire.dawn.survivors') }}</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($players as $player)
                                    <span class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-semibold {{ $player['alive'] ? 'border-zinc-200 bg-white/60 text-zinc-700 dark:border-white/8 dark:bg-zinc-800/50 dark:text-zinc-300' : 'border-zinc-200/50 bg-zinc-100/40 text-zinc-400 line-through dark:border-white/5 dark:bg-zinc-800/20 dark:text-zinc-500' }}">
                                        {{ $player['name'] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                {{-- Hunter Last Shot --}}
                @elseif ($status === \App\Enums\VampireRoomStatus::HunterLastShot)
                    <div class="space-y-4">
                        <div class="rounded-3xl border border-amber-200/60 bg-gradient-to-br from-amber-50/60 to-white/70 p-6 shadow-sm backdrop-blur-md dark:border-amber-400/20 dark:from-amber-950/30 dark:to-zinc-900/60">
                            <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-600/70 dark:text-amber-300/60">{{ __('vampire.hunter.title') }}</p>
                            <p class="cyber-title mt-2 text-xl font-bold text-zinc-950 dark:text-white">{{ __('vampire.hunter.subtitle') }}</p>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ __('vampire.hunter.desc') }}</p>
                        </div>

                        @if ($myRole === \App\Enums\VampireRole::Hunter->value && $myAlive === false)
                            <div class="rounded-3xl border shadow-sm backdrop-blur-md {{ $roleStyle['action'] }} p-6">
                                <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.18em] text-amber-600/70 dark:text-amber-300/60">{{ __('vampire.hunter.chooseTarget') }}</p>
                                <div class="mt-4 space-y-2">
                                    @foreach ($players as $player)
                                        @if (! $player['isMe'] && $player['alive'])
                                            <button
                                                wire:key="hunter-shot-{{ $player['id'] }}"
                                                type="button"
                                                wire:click="hunterShoot('{{ $player['id'] }}')"
                                                class="touch-manipulation flex w-full items-center justify-between gap-4 rounded-2xl border border-amber-200/80 bg-white/60 px-4 py-3.5 text-left text-sm font-semibold shadow-sm backdrop-blur transition hover:bg-amber-50/40 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400/60 dark:border-amber-400/15 dark:bg-zinc-800/40 dark:text-white dark:hover:bg-amber-950/20"
                                            >
                                                <span class="flex min-w-0 items-center gap-3">
                                                    <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-base {{ \App\View\PlayerColors::avatar($player['color']) }}">{{ $player['emoji'] }}</span>
                                                    <span class="min-w-0 truncate text-zinc-900 dark:text-white">{{ $player['name'] }}</span>
                                                </span>
                                                <span class="font-mono text-[11px] font-semibold text-amber-600 dark:text-amber-300">{{ __('vampire.hunter.shoot') }}</span>
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @elseif ($isJoined)
                            <div class="rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-md dark:border-white/8 dark:bg-zinc-900/60">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-2.5 w-2.5 animate-pulse rounded-full bg-amber-400"></span>
                                    <p class="text-sm font-semibold text-zinc-950 dark:text-white">{{ __('vampire.hunter.waiting') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                {{-- Day --}}
                @elseif ($status === \App\Enums\VampireRoomStatus::Day)
                    <div class="space-y-4">
                        <div class="rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-md dark:border-white/8 dark:bg-zinc-900/60">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-2.5 w-2.5 animate-pulse rounded-full bg-yellow-400"></span>
                                <p class="text-sm font-semibold text-zinc-950 dark:text-white">{{ __('vampire.day.title') }}</p>
                            </div>
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ __('vampire.day.desc') }}</p>
                        </div>

                        <div class="rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-md dark:border-white/8 dark:bg-zinc-900/60">
                            <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">{{ __('vampire.day.alivePlayers') }}</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($players as $player)
                                    <span wire:key="day-alive-badge-{{ $player['id'] }}" class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1.5 text-xs font-semibold {{ $player['alive'] ? 'border-zinc-200 bg-white/60 text-zinc-700 dark:border-white/8 dark:bg-zinc-800/50 dark:text-zinc-300' : 'border-zinc-200/50 bg-zinc-100/40 text-zinc-400 line-through dark:border-white/5 dark:bg-zinc-800/20 dark:text-zinc-500' }}">
                                        {{ $player['name'] }}
                                        @if ($player['isHost'])
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                {{-- Day Voting --}}
                @elseif ($status === \App\Enums\VampireRoomStatus::DayVoting)
                    <div class="rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-md dark:border-white/8 dark:bg-zinc-900/60">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="cyber-title text-base font-bold text-zinc-950 dark:text-white">{{ __('vampire.voting.title') }}</p>
                                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-300">{{ __('vampire.voting.lead') }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                @if ($isJoined && $myAlive)
                                    <button wire:click="bongg" type="button" class="flex shrink-0 items-center gap-1.5 rounded-xl border border-red-200/50 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100 focus:outline-none dark:border-red-400/20 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-900/60" title="Oyuncuları Dürtt!">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                        Bongg!
                                    </button>
                                @endif
                                @if ($myDayVote)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-3 py-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                        {{ __('vampire.voting.voted') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if ($isJoined && $myAlive)
                            <div class="mt-5 space-y-2">
                                @foreach ($players as $player)
                                    @if (! $player['isMe'] && $player['alive'])
                                        @php $playerVotes = (int) ($dayVoteCounts[$player['id']] ?? 0); @endphp
                                        <button
                                            wire:key="day-vote-btn-{{ $player['id'] }}"
                                            type="button"
                                            wire:click="castDayVote('{{ $player['id'] }}')"
                                            class="touch-manipulation flex w-full items-center justify-between gap-4 rounded-2xl border px-4 py-3.5 text-left text-sm font-semibold shadow-sm backdrop-blur transition focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:focus-visible:ring-red-300/30 {{ $myDayVote === $player['id'] ? 'border-red-400/40 bg-red-50/50 text-red-900 ring-1 ring-red-400/30 dark:border-red-400/15 dark:bg-red-500/[0.06] dark:text-red-100 dark:ring-red-300/10' : 'border-zinc-200/80 bg-white/60 text-zinc-900 hover:bg-white/80 dark:border-white/[0.08] dark:bg-zinc-800/40 dark:text-white dark:hover:bg-zinc-800/60' }}"
                                        >
                                            <span class="flex min-w-0 items-center gap-3">
                                                <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-base {{ \App\View\PlayerColors::avatar($player['color']) }}">{{ $player['emoji'] }}</span>
                                                <span class="min-w-0 truncate">{{ $player['name'] }}</span>
                                                <span class="inline-flex min-w-7 shrink-0 items-center justify-center rounded-full bg-zinc-200/80 px-2 py-0.5 font-mono text-[11px] font-semibold text-zinc-600 dark:bg-zinc-700/60 dark:text-zinc-300">{{ $playerVotes }}</span>
                                            </span>
                                            <span class="font-mono text-[11px] font-semibold {{ $myDayVote === $player['id'] ? 'text-red-600 dark:text-red-300' : 'text-zinc-400 dark:text-zinc-500' }}">{{ $myDayVote === $player['id'] ? __('vampire.voting.selected') : __('vampire.voting.select') }}</span>
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        @elseif ($isJoined && ! $myAlive)
                            <p class="mt-4 text-sm text-zinc-500 dark:text-zinc-400">{{ __('vampire.voting.eliminatedCannotVote') }}</p>
                        @endif
                    </div>

                {{-- Day Results --}}
                @elseif ($status === \App\Enums\VampireRoomStatus::DayResults)
                    @php $maxVotes = max(1, max(array_values($dayVoteCounts) ?: [0])); @endphp
                    <div class="space-y-4">
                        @if ($dayResult !== null && ($dayResult['eliminatedId'] ?? null) !== null)
                            <div class="rounded-3xl border border-rose-200/50 bg-gradient-to-br from-rose-50/60 to-white/70 p-6 shadow-sm backdrop-blur-md dark:border-rose-400/15 dark:from-rose-950/30 dark:to-zinc-900/60">
                                <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.18em] text-rose-600/70 dark:text-rose-300/60">{{ __('vampire.day.eliminationResult') }}</p>
                                <p class="cyber-title mt-2 text-xl font-bold text-zinc-950 dark:text-white">{{ $dayResult['eliminatedName'] ?? '' }}</p>
                                @if ($isHost)
                                    <div class="mt-4">
                                        {{-- Buttons moved to header --}}
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Vote tally --}}
                        <div class="rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-md dark:border-white/8 dark:bg-zinc-900/60">
                            <p class="cyber-title text-base font-bold text-zinc-950 dark:text-white">{{ __('vampire.voting.resultsTitle') }}</p>
                            <div class="mt-5 space-y-2">
                                @foreach ($players as $player)
                                    @php
                                        $pVotes = (int) ($dayVoteCounts[$player['id']] ?? 0);
                                        $pVoters = $dayVoteMap[$player['id']] ?? [];
                                    @endphp
                                    <div class="relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white/60 px-4 py-3.5 text-sm font-semibold shadow-sm backdrop-blur-md dark:border-white/[0.08] dark:bg-zinc-800/40">
                                        @if ($pVotes > 0)
                                            <div class="absolute inset-y-0 left-0 bg-red-400/8 dark:bg-red-400/6" style="width: {{ round(($pVotes / $maxVotes) * 100) }}%"></div>
                                        @endif
                                        <div class="relative flex items-center justify-between gap-4">
                                            <span class="flex min-w-0 items-center gap-3">
                                                <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-base {{ \App\View\PlayerColors::avatar($player['color']) }}">{{ $player['emoji'] }}</span>
                                                <span class="min-w-0 truncate text-zinc-900 dark:text-white {{ $player['alive'] ? '' : 'line-through text-zinc-400 dark:text-zinc-500' }}">{{ $player['name'] }}</span>
                                            </span>
                                            <span class="inline-flex min-w-7 items-center justify-center rounded-full font-mono text-xs font-semibold {{ $pVotes > 0 ? 'bg-red-400/10 text-red-700 dark:text-red-200' : 'text-zinc-400 dark:text-zinc-500' }}">{{ $pVotes }}</span>
                                        </div>
                                        @if (count($pVoters) > 0)
                                            <div class="relative mt-2 flex flex-wrap gap-1.5">
                                                @foreach ($pVoters as $voterName)
                                                    <span class="inline-flex items-center gap-1 rounded-full border border-zinc-200/60 bg-white/50 px-2 py-0.5 text-[11px] font-semibold text-zinc-500 dark:border-white/[0.06] dark:bg-zinc-800/30 dark:text-zinc-400">{{ $voterName }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                {{-- Game Over --}}
                @elseif ($status === \App\Enums\VampireRoomStatus::GameOver)
                    <div class="space-y-4">
                        @php
                            $vampiresWon = $winner === 'vampires';
                            $villageWon = $winner === 'villagers';
                        @endphp
                        <div class="rounded-3xl border {{ $vampiresWon ? 'border-red-200/50 bg-gradient-to-br from-red-50/60 to-white/70 dark:border-red-400/15 dark:from-red-950/30 dark:to-zinc-900/60' : 'border-emerald-200/50 bg-gradient-to-br from-emerald-50/60 to-white/70 dark:border-emerald-400/15 dark:from-emerald-950/30 dark:to-zinc-900/60' }} p-6 shadow-sm backdrop-blur-md">
                            <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.18em] {{ $vampiresWon ? 'text-red-600/70 dark:text-red-300/60' : 'text-emerald-600/70 dark:text-emerald-300/60' }}">{{ __('vampire.gameover.title') }}</p>
                            <p class="cyber-title mt-2 text-2xl font-bold text-zinc-950 dark:text-white">
                                @if ($vampiresWon)
                                    {{ __('vampire.gameover.vampiresWin') }}
                                @elseif ($villageWon)
                                    {{ __('vampire.gameover.villagersWin') }}
                                @else
                                    {{ __('vampire.gameover.declared') }}
                                @endif
                            </p>
                        </div>

                        {{-- Reveal all roles --}}
                        <div class="rounded-3xl border border-zinc-200/80 bg-white/70 p-6 shadow-sm backdrop-blur-md dark:border-white/8 dark:bg-zinc-900/60">
                            <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">{{ __('vampire.gameover.roles') }}</p>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('vampire.gameover.rolesHint') }}</p>
                            <div class="mt-3 space-y-2">
                                @foreach ($players as $player)
                                    <div class="flex items-center justify-between rounded-2xl border border-zinc-200/80 bg-white/60 px-4 py-3 text-sm font-semibold dark:border-white/[0.08] dark:bg-zinc-800/40">
                                        <span class="flex min-w-0 items-center gap-3">
                                            <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-base {{ \App\View\PlayerColors::avatar($player['color']) }}">{{ $player['emoji'] }}</span>
                                            <span class="min-w-0 truncate text-zinc-900 dark:text-white {{ $player['alive'] ? '' : 'line-through text-zinc-400 dark:text-zinc-500' }}">{{ $player['name'] }}</span>
                                        </span>
                                        <span class="text-xs text-zinc-400 dark:text-zinc-500">{{ $player['alive'] ? __('vampire.alive') : __('vampire.dead') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                @else
                    {{-- Fallback / joined but game started state --}}
                    @if (! $isJoined)
                        <div class="rounded-3xl border border-amber-200/60 bg-amber-50/60 p-6 shadow-sm backdrop-blur-md dark:border-amber-400/20 dark:bg-amber-950/25">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl border border-amber-200/80 bg-amber-100/80 dark:border-amber-400/20 dark:bg-amber-900/30">
                                    <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </span>
                                <div>
                                    <h3 class="cyber-title text-base font-bold text-zinc-950 dark:text-white">{{ __('vampire.gameAlreadyStarted') }}</h3>
                                    <p class="mt-1 text-sm leading-relaxed text-zinc-600 dark:text-zinc-300">{{ __('vampire.gameAlreadyStartedDesc') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Players modal --}}
        @if ($showPlayers)
            <div class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:items-center" wire:keydown.window.escape="closePlayers">
                <button
                    type="button"
                    wire:click="closePlayers"
                    class="absolute inset-0 cursor-default bg-black/45 backdrop-blur-sm"
                    aria-label="Close players"
                    aria-hidden="true"
                    tabindex="-1"
                ></button>

                <div
                    role="dialog"
                    aria-modal="true"
                    aria-label="{{ __('vampire.players') }}"
                    class="relative w-full max-w-md overflow-hidden rounded-3xl border border-zinc-200 bg-white/95 shadow-2xl shadow-zinc-950/20 dark:border-white/10 dark:bg-zinc-950/95 dark:shadow-black/30"
                >
                    <div class="flex items-center justify-between border-b border-zinc-200/70 bg-white/70 px-5 py-4 backdrop-blur dark:border-white/8 dark:bg-zinc-950/55">
                        <div class="flex items-center gap-3">
                            <h3 class="cyber-title text-base font-bold text-zinc-950 dark:text-white">{{ __('vampire.players') }}</h3>
                            <span class="rounded-full bg-red-400/10 px-2.5 py-1 font-mono text-[11px] font-semibold text-red-700 dark:text-red-200">{{ count($players) }}</span>
                        </div>
                        <button
                            type="button"
                            wire:click="closePlayers"
                            class="touch-manipulation inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-zinc-200 bg-white/70 text-sm font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 dark:border-white/8 dark:bg-zinc-950/40 dark:text-white dark:hover:bg-zinc-950/55"
                            aria-label="Close players"
                        >
                            &times;
                        </button>
                    </div>

                    <div class="max-h-[72svh] overflow-auto overscroll-contain p-4">
                        <div class="space-y-2">
                            @forelse ($players as $player)
                                <div class="flex items-center justify-between rounded-2xl border bg-white/70 px-4 py-3.5 text-sm font-semibold shadow-sm backdrop-blur {{ $player['isMe'] ? 'border-red-400/30 ring-1 ring-red-400/20 dark:border-red-400/15 dark:ring-red-300/10' : 'border-zinc-200 dark:border-white/10' }} text-zinc-900 dark:bg-zinc-800/50 dark:text-white">
                                    <span class="flex min-w-0 items-center gap-3">
                                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-base {{ \App\View\PlayerColors::avatar($player['color']) }}">{{ $player['emoji'] }}</span>
                                        <span class="min-w-0 truncate {{ $player['alive'] ? '' : 'line-through text-zinc-400 dark:text-zinc-500' }}">{{ $player['name'] }}</span>
                                    </span>
                                    <div class="flex items-center gap-2">
                                        @if ($player['isHost'])
                                            <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-300">{{ __('vampire.host') }}</span>
                                        @endif
                                        @if (! $player['alive'])
                                            <span class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-semibold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">{{ __('vampire.dead') }}</span>
                                        @endif
                                        @if ($player['isMe'])
                                            <span class="rounded-full bg-red-400/10 px-2.5 py-1 text-xs font-semibold text-red-700 dark:text-red-200">Ben</span>
                                        @endif
                                        @if ($isHost && ! $player['isMe'] && $status === \App\Enums\VampireRoomStatus::Lobby)
                                            <button
                                                type="button"
                                                wire:click="openKickConfirm('{{ $player['id'] }}', '{{ addslashes($player['name']) }}')"
                                                class="flex h-7 w-7 items-center justify-center rounded-lg text-zinc-400 transition-colors hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-950/30"
                                                title="{{ __('vampire.kick') }}"
                                            >
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="py-6 text-center text-sm text-zinc-600 dark:text-zinc-300">{{ __('vampire.noOneYet') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
    {{-- Custom Modal for Kick Confirmation --}}
    @if ($confirmKickPlayerId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/60 p-4 backdrop-blur-md transition-opacity">
            <div class="w-full max-w-sm scale-100 transform overflow-hidden rounded-3xl bg-white p-6 text-left align-middle shadow-2xl transition-all border border-zinc-200 dark:bg-zinc-900 dark:border-white/10" @click.away="$wire.closeKickConfirm()">
                <h3 class="vampire-phase-banner text-lg font-bold text-zinc-900 dark:text-white">
                    {{ __('vampire.modal.kickConfirm.title') }}
                </h3>
                <div class="mt-3">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        {!! __('vampire.modal.kickConfirm.desc', ['name' => '<span class="font-bold text-zinc-900 dark:text-white">' . e($confirmKickPlayerName) . '</span>']) !!}
                    </p>
                </div>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row-reverse">
                    <button type="button" wire:click="kickPlayer('{{ $confirmKickPlayerId }}')" class="inline-flex w-full justify-center rounded-2xl bg-red-600 px-5 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-red-500 focus:outline-none sm:w-auto">
                        {{ __('vampire.modal.kickConfirm.confirm') }}
                    </button>
                    <button type="button" wire:click="closeKickConfirm" class="inline-flex w-full justify-center rounded-2xl bg-zinc-100 px-5 py-3 text-sm font-bold text-zinc-900 shadow-sm transition hover:bg-zinc-200 sm:w-auto dark:bg-zinc-800 dark:text-white dark:hover:bg-zinc-700">
                        {{ __('vampire.modal.kickConfirm.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
