<?php

namespace App\Livewire\Games;

use App\View\PlayerColors;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class Identity extends Component
{
    public string $name = '';

    public string $color = 'sky';

    public string $emoji = '🎭';

    public bool $editing = false;

    private const ALLOWED_EMOJIS = ['🐺', '🧛', '🕵️', '🏹', '🧙', '👁️', '🗡️', '🛡️', '🌙', '☀️', '🔮', '👑', '🦇', '🩸', '⚔️', '🎭'];

    public function mount(): void
    {
        $identity = session('games.identity');

        if (is_array($identity) && ! empty($identity['name'])) {
            $this->name = (string) $identity['name'];
            $this->color = in_array($identity['color'] ?? '', PlayerColors::names(), true)
                ? (string) $identity['color']
                : 'sky';
            $this->emoji = in_array($identity['emoji'] ?? '', self::ALLOWED_EMOJIS, true)
                ? (string) $identity['emoji']
                : '🎭';
            $this->editing = false;
        } else {
            $this->editing = true;
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:24'],
            'color' => ['required', 'string', Rule::in(PlayerColors::names())],
            'emoji' => ['required', 'string', Rule::in(self::ALLOWED_EMOJIS)],
        ]);

        session()->put('games.identity', [
            'name' => trim($this->name),
            'color' => $this->color,
            'emoji' => $this->emoji,
        ]);

        $this->editing = false;
        $this->dispatch('identity-saved');
    }

    public function setColor(string $color): void
    {
        if (array_key_exists($color, PlayerColors::map())) {
            $this->color = $color;
        }
    }

    public function setEmoji(string $emoji): void
    {
        if (in_array($emoji, self::ALLOWED_EMOJIS, true)) {
            $this->emoji = $emoji;
        }
    }

    public function edit(): void
    {
        $this->editing = true;
    }

    public function cancelEdit(): void
    {
        // Only allow cancellation when an identity already exists
        if (session('games.identity')) {
            $this->editing = false;
        }
    }

    public function render(): View
    {
        return view('livewire.games.identity', [
            'colorMap' => PlayerColors::map(),
            'emojis' => self::ALLOWED_EMOJIS,
        ]);
    }
}
