<?php

namespace App\View;

final class PlayerColors
{
    /**
     * Returns the combined avatar class string (bg + text) for a given color.
     * Used in room player lists and voting screens.
     *
     * All Tailwind class strings appear literally here so the JIT scanner picks them up.
     *
     * @return array<string, array{bg: string, text: string, ring: string, swatch: string}>
     */
    public static function map(): array
    {
        return [
            'red' => ['bg' => 'bg-red-500/15 dark:bg-red-400/15',       'text' => 'text-red-700 dark:text-red-300',       'ring' => 'ring-red-400/60 dark:ring-red-300/40',       'swatch' => 'bg-red-500'],
            'orange' => ['bg' => 'bg-orange-500/15 dark:bg-orange-400/15', 'text' => 'text-orange-700 dark:text-orange-300', 'ring' => 'ring-orange-400/60 dark:ring-orange-300/40', 'swatch' => 'bg-orange-500'],
            'amber' => ['bg' => 'bg-amber-500/15 dark:bg-amber-400/15',   'text' => 'text-amber-700 dark:text-amber-300',   'ring' => 'ring-amber-400/60 dark:ring-amber-300/40',   'swatch' => 'bg-amber-500'],
            'green' => ['bg' => 'bg-green-500/15 dark:bg-green-400/15',   'text' => 'text-green-700 dark:text-green-300',   'ring' => 'ring-green-400/60 dark:ring-green-300/40',   'swatch' => 'bg-green-500'],
            'teal' => ['bg' => 'bg-teal-500/15 dark:bg-teal-400/15',     'text' => 'text-teal-700 dark:text-teal-300',     'ring' => 'ring-teal-400/60 dark:ring-teal-300/40',     'swatch' => 'bg-teal-500'],
            'sky' => ['bg' => 'bg-sky-500/15 dark:bg-sky-400/15',       'text' => 'text-sky-700 dark:text-sky-300',       'ring' => 'ring-sky-400/60 dark:ring-sky-300/40',       'swatch' => 'bg-sky-500'],
            'purple' => ['bg' => 'bg-purple-500/15 dark:bg-purple-400/15', 'text' => 'text-purple-700 dark:text-purple-300', 'ring' => 'ring-purple-400/60 dark:ring-purple-300/40', 'swatch' => 'bg-purple-500'],
            'pink' => ['bg' => 'bg-pink-500/15 dark:bg-pink-400/15',     'text' => 'text-pink-700 dark:text-pink-300',     'ring' => 'ring-pink-400/60 dark:ring-pink-300/40',     'swatch' => 'bg-pink-500'],
        ];
    }

    /** Returns the full color data array for a given color name (with sky fallback). */
    public static function all(string $color): array
    {
        return self::map()[$color] ?? self::map()['sky'];
    }

    /** Returns the combined "bg + text" class string used for avatar chips. */
    public static function avatar(string $color): string
    {
        $c = self::all($color);

        return $c['bg'].' '.$c['text'];
    }

    /** Returns all valid color names (drives Identity component validation). */
    public static function names(): array
    {
        return array_keys(self::map());
    }
}
