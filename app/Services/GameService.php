<?php

namespace App\Services;

use App\Enums\GameSessionStatus;
use App\Models\Game;
use App\Models\GamePlayer;
use App\Models\GameSession;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class GameService
{
    /**
     * Create a new game session.
     */
    public function createSession(Game $game, array $initialMetadata = []): GameSession
    {
        $session = GameSession::create([
            'game_id' => $game->id,
            'status' => GameSessionStatus::WAITING,
            'room_code' => strtoupper(Str::random(6)),
            'metadata' => $initialMetadata,
        ]);

        // Initialize Redis state
        $this->syncStateToRedis($session, [
            'board' => [],
            'turn' => null,
            'last_action_at' => now()->timestamp,
        ]);

        return $session;
    }

    /**
     * Add a player to a session.
     */
    public function joinSession(GameSession $session, int $userId): GamePlayer
    {
        if ($session->players()->count() >= $session->game->max_players) {
            throw new \Exception('Oda dolu.');
        }

        $player = GamePlayer::firstOrCreate([
            'game_session_id' => $session->id,
            'user_id' => $userId,
        ]);

        // Track active player in Redis
        Redis::sadd("game:session:{$session->room_code}:players", $userId);

        return $player;
    }

    /**
     * Update game state in Redis.
     */
    public function updateRedisState(string $roomCode, array $data): void
    {
        $key = "game:session:{$roomCode}:state";

        $encodedData = array_map(function ($value) {
            return is_scalar($value) ? $value : json_encode($value);
        }, $data);

        Redis::hmset($key, $encodedData);
        Redis::hset($key, 'updated_at', now()->timestamp);
    }

    /**
     * Get real-time state from Redis.
     */
    public function getRedisState(string $roomCode): array
    {
        $state = Redis::hgetall("game:session:{$roomCode}:state");

        return array_map(function ($value) {
            $decoded = json_decode($value, true);

            return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $value;
        }, $state);
    }

    /**
     * Sync state from MySQL to Redis (e.g., on session resume).
     */
    protected function syncStateToRedis(GameSession $session, array $state): void
    {
        $key = "game:session:{$session->room_code}:state";

        // Predis requires string values for hmset
        $encodedState = array_map(function ($value) {
            return is_scalar($value) ? $value : json_encode($value);
        }, $state);

        Redis::hmset($key, $encodedState);
    }

    /**
     * Finalize session and persist results.
     */
    public function completeSession(GameSession $session, array $finalMetadata = []): void
    {
        $session->update([
            'status' => GameSessionStatus::COMPLETED,
            'finished_at' => now(),
            'metadata' => array_merge($session->metadata ?? [], $finalMetadata),
        ]);

        // Cleanup Redis
        Redis::del("game:session:{$session->room_code}:state");
        Redis::del("game:session:{$session->room_code}:players");
    }
}
