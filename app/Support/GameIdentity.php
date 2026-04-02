<?php

namespace App\Support;

final class GameIdentity
{
    public static function get(): ?array
    {
        $identity = session('games.identity');

        return is_array($identity) && ! empty($identity['name']) ? $identity : null;
    }

    public static function exists(): bool
    {
        return self::get() !== null;
    }

    public static function name(): string
    {
        return (string) (self::get()['name'] ?? '');
    }

    public static function color(): string
    {
        return (string) (self::get()['color'] ?? 'sky');
    }

    public static function emoji(): string
    {
        return (string) (self::get()['emoji'] ?? '🎭');
    }
}
