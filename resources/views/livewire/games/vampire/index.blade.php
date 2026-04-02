<div>
<livewire:games.identity />

<div class="mt-6 grid gap-4 lg:grid-cols-2">
    <div class="relative overflow-hidden rounded-3xl border border-zinc-200 bg-white/75 p-6 backdrop-blur dark:border-white/10 dark:bg-zinc-950/35 sm:p-8">
        <h2 class="cyber-title text-lg font-semibold text-zinc-950 dark:text-white">{{ __('vampire.createRoom') }}</h2>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ __('vampire.createRoomDesc') }}</p>

        <form wire:submit="createRoom" class="mt-6 space-y-3">
            <label class="block">
                <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">{{ __('vampire.password') }} <span class="font-normal text-zinc-400 dark:text-zinc-500">{{ __('vampire.passwordOptional') }}</span></span>
                <input
                    wire:model.live="createPassword"
                    type="password"
                    name="createPassword"
                    autocomplete="off"
                    spellcheck="false"
                    class="mt-2 w-full rounded-2xl border border-zinc-200 bg-white/70 px-4 py-3 text-sm font-semibold text-zinc-950 shadow-sm outline-none transition placeholder:text-zinc-400 focus:border-zinc-300 focus-visible:ring-2 focus-visible:ring-red-400/60 dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-white/20 dark:focus-visible:ring-red-300/30"
                    placeholder="{{ __('vampire.placeholder.password') }}"
                />
            </label>

            @if ($error)
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 dark:border-rose-400/20 dark:bg-rose-950/30 dark:text-rose-200">
                    {{ $error }}
                </div>
            @endif

            <button
                type="submit"
                @disabled(!$hasIdentity)
                class="touch-manipulation inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-zinc-950 px-6 py-3 text-sm font-semibold text-white shadow-sm ring-1 ring-zinc-950/10 transition hover:bg-zinc-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-white dark:text-zinc-950 dark:ring-white/10 dark:hover:bg-zinc-100 dark:focus-visible:ring-red-300/30"
            >
                {{ __('vampire.create') }}
                <span aria-hidden="true" class="text-white/70 dark:text-zinc-500">→</span>
            </button>
        </form>
    </div>

    <div class="relative overflow-hidden rounded-3xl border border-zinc-200 bg-white/75 p-6 backdrop-blur dark:border-white/10 dark:bg-zinc-950/35 sm:p-8">
        <h2 class="cyber-title text-lg font-semibold text-zinc-950 dark:text-white">{{ __('vampire.joinRoom') }}</h2>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">{{ __('vampire.joinRoomDesc') }}</p>

        <form wire:submit="joinRoom" class="mt-6 space-y-3">
            <label class="block">
                <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">{{ __('vampire.roomCode') }}</span>
                <input
                    wire:model.live="roomCode"
                    type="text"
                    name="roomCode"
                    inputmode="text"
                    autocomplete="off"
                    spellcheck="false"
                    autocapitalize="characters"
                    class="mt-2 w-full rounded-2xl border border-zinc-200 bg-white/70 px-4 py-3 text-sm font-semibold uppercase tracking-widest text-zinc-950 shadow-sm outline-none transition placeholder:text-zinc-400 focus:border-zinc-300 focus-visible:ring-2 focus-visible:ring-red-400/60 dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-white/20 dark:focus-visible:ring-red-300/30"
                    placeholder="{{ __('vampire.placeholder.roomCode') }}"
                />
                @error('roomCode') <span class="mt-2 block text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">{{ __('vampire.password') }} <span class="font-normal text-zinc-400 dark:text-zinc-500">{{ __('vampire.passwordIfRequired') }}</span></span>
                <input
                    wire:model.live="joinPassword"
                    type="password"
                    name="joinPassword"
                    autocomplete="off"
                    spellcheck="false"
                    class="mt-2 w-full rounded-2xl border border-zinc-200 bg-white/70 px-4 py-3 text-sm font-semibold text-zinc-950 shadow-sm outline-none transition placeholder:text-zinc-400 focus:border-zinc-300 focus-visible:ring-2 focus-visible:ring-red-400/60 dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:placeholder:text-zinc-500 dark:focus:border-white/20 dark:focus-visible:ring-red-300/30"
                    placeholder="{{ __('vampire.placeholder.password') }}"
                />
            </label>

            @if ($error)
                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 dark:border-rose-400/20 dark:bg-rose-950/30 dark:text-rose-200">
                    {{ $error }}
                </div>
            @endif

            <button
                type="submit"
                @disabled(!$hasIdentity)
                class="touch-manipulation inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-zinc-200 bg-white/70 px-6 py-3 text-sm font-semibold text-zinc-900 shadow-sm backdrop-blur transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/60 disabled:opacity-50 disabled:cursor-not-allowed dark:border-white/10 dark:bg-zinc-950/35 dark:text-white dark:hover:bg-zinc-950/55 dark:focus-visible:ring-red-300/30"
            >
                {{ __('vampire.joinAction') }}
                <span aria-hidden="true">→</span>
            </button>
        </form>
    </div>
</div>
</div>
