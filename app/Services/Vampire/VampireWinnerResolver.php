<?php

namespace App\Services\Vampire;

final class VampireWinnerResolver
{
    /**
     * @param  array<string, array<string, mixed>>  $players
     */
    public function resolve(array $players): ?string
    {
        $living = array_filter($players, fn (array $player): bool => (bool) ($player['alive'] ?? false));
        $livingVampires = count(array_filter($living, fn (array $player): bool => ($player['alignment'] ?? null) === 'vampire'));
        $livingVillagers = count(array_filter($living, fn (array $player): bool => ($player['alignment'] ?? null) === 'villager'));

        if ($livingVampires === 0) {
            return 'villagers';
        }

        if ($livingVampires >= $livingVillagers) {
            return 'vampires';
        }

        return null;
    }
}
