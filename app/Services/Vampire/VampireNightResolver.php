<?php

namespace App\Services\Vampire;

use App\Enums\VampireNightPhase;
use App\Enums\VampireRole;
use App\Enums\VampireRoomStatus;
use Carbon\CarbonImmutable;

final class VampireNightResolver
{
    public function __construct(
        private readonly VampireWinnerResolver $winnerResolver,
    ) {}

    /**
     * @param  array<string, mixed>  $room
     * @return array<string, mixed>
     */
    public function resolve(array $room): array
    {
        $players = (array) ($room['players'] ?? []);
        $nightVotes = (array) ($room['nightVotes'] ?? []);
        $doctorProtects = is_string($room['doctorProtects'] ?? null) ? (string) $room['doctorProtects'] : null;

        $killTarget = $this->resolveKillTarget($nightVotes);
        $saved = false;
        $killedId = null;
        $killedName = null;
        $savedByName = null;
        $hunterTriggered = false;

        if ($killTarget !== null && isset($players[$killTarget])) {
            $killedId = $killTarget;
            $killedName = (string) ($players[$killTarget]['name'] ?? 'Unknown');

            if ($killTarget === $doctorProtects) {
                $saved = true;
                $savedByName = $this->findRoleName($players, VampireRole::Doctor->value);
            } else {
                $players[$killTarget]['alive'] = false;
                $hunterTriggered = VampireRole::tryFrom((string) ($players[$killTarget]['role'] ?? '')) === VampireRole::Hunter;
            }
        }

        $room['players'] = $players;
        $room['nightResult'] = [
            'killedId' => $killedId,
            'killedName' => $killedName,
            'saved' => $saved,
            'savedById' => $saved ? $doctorProtects : null,
            'savedByName' => $savedByName,
            'hunterTriggered' => $hunterTriggered,
        ];

        if ($killedId !== null && ! $saved) {
            $room['history'][] = [
                'icon' => '✝',
                'type' => 'death',
                'key' => 'vampire.log.death_night',
                'params' => ['name' => $killedName],
                'night' => $room['nightNumber'],
            ];
        } elseif ($saved) {
            $room['history'][] = [
                'icon' => '💚',
                'type' => 'save',
                'key' => 'vampire.log.save_night',
                'params' => [],
                'night' => $room['nightNumber'],
            ];
        } else {
            $room['history'][] = [
                'icon' => '😮',
                'type' => 'narrative',
                'key' => 'vampire.log.no_kill_night',
                'params' => [],
                'night' => $room['nightNumber'],
            ];
        }

        $room['nightVotes'] = [];
        $room['lastProtectedId'] = $room['doctorProtects'] ?? null;
        $room['doctorProtects'] = null;
        $room['detectiveQuery'] = null;
        $room['nightPhase'] = VampireNightPhase::Resolved->value;
        $room['nightPhaseStartedAt'] = CarbonImmutable::now()->toIso8601String();

        if ($hunterTriggered) {
            $room['status'] = VampireRoomStatus::HunterLastShot->value;

            return $room;
        }

        $winner = $this->winnerResolver->resolve($players);
        if ($winner !== null) {
            $room['status'] = VampireRoomStatus::GameOver->value;
            $room['winner'] = $winner;
            $room['gameOverAt'] = CarbonImmutable::now()->toIso8601String();

            return $room;
        }

        $room['status'] = VampireRoomStatus::Dawn->value;

        return $room;
    }

    /**
     * @param  array<string, string>  $nightVotes
     */
    private function resolveKillTarget(array $nightVotes): ?string
    {
        $voteCounts = [];

        foreach ($nightVotes as $targetId) {
            if (! is_string($targetId) || $targetId === '') {
                continue;
            }

            $voteCounts[$targetId] = ($voteCounts[$targetId] ?? 0) + 1;
        }

        if ($voteCounts === []) {
            return null;
        }

        $maxVotes = max($voteCounts);
        $topTargets = array_keys(array_filter($voteCounts, fn (int $count): bool => $count === $maxVotes));

        return count($topTargets) === 1 ? $topTargets[0] : null;
    }

    /**
     * @param  array<string, array<string, mixed>>  $players
     */
    private function findRoleName(array $players, string $role): ?string
    {
        foreach ($players as $player) {
            if (($player['role'] ?? null) === $role) {
                return (string) ($player['name'] ?? '');
            }
        }

        return null;
    }
}
