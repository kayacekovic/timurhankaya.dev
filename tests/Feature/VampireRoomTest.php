<?php

use App\Enums\VampireRole;
use App\Services\Vampire\VampireRoomService;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

it('creates a room with a host', function () {
    Cache::flush();

    $rooms = app(VampireRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');

    expect($created)->toHaveKeys(['code', 'hostPlayerId']);
    expect($created['code'])->toBeString()->not->toBeEmpty();
    expect($created['hostPlayerId'])->toBeString()->not->toBeEmpty();

    $room = $rooms->getRoom($created['code']);

    expect($room)->not->toBeNull();
    expect($room['status'])->toBe('lobby');
    expect($room['hostPlayerId'])->toBe($created['hostPlayerId']);
    expect($room['players'])->toHaveKey($created['hostPlayerId']);

    $hostPlayer = $room['players'][$created['hostPlayerId']];
    expect($hostPlayer['color'])->toBe('red');
    expect($hostPlayer['emoji'])->toBe('🧛');
});

it('joins a room successfully', function () {
    Cache::flush();

    $rooms = app(VampireRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $joined = $rooms->joinRoom($created['code'], 'Guest', null, 'sky', '🐺');

    expect($joined)->not->toBeNull();
    expect($joined)->toHaveKey('playerId');

    $room = $rooms->getRoom($created['code']);
    expect($room['players'])->toHaveCount(2);

    $guestPlayer = $room['players'][$joined['playerId']];
    expect($guestPlayer['color'])->toBe('sky');
    expect($guestPlayer['emoji'])->toBe('🐺');
});

it('validates config: total must equal player count', function () {
    Cache::flush();

    $rooms = app(VampireRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $rooms->joinRoom($created['code'], 'P2', null, 'sky', '🐺');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');
    $rooms->joinRoom($created['code'], 'P4', null, 'green', '👑');

    // 4 players, config sums to 3 (1 vampir + 2 koylu = 3) — mismatch
    $rooms->updateConfig($created['code'], $created['hostPlayerId'], [
        'vampireCount' => 1,
        'villagerCount' => 2,
        'hasDoktor' => false,
        'hasDedektif' => false,
        'hasAvci' => false,
    ]);

    $result = $rooms->startGame($created['code'], $created['hostPlayerId']);

    // startGame returns room unchanged (stays lobby) on validation failure
    expect($result)->not->toBeNull();
    expect($result['status'])->toBe('lobby');
});

it('starts a game when config matches player count', function () {
    Cache::flush();

    $rooms = app(VampireRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $rooms->joinRoom($created['code'], 'P2', null, 'sky', '🐺');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');
    $rooms->joinRoom($created['code'], 'P4', null, 'green', '👑');

    // 4 players: 1 vampir + 2 koylu + 1 doktor = 4
    $rooms->updateConfig($created['code'], $created['hostPlayerId'], [
        'vampireCount' => 1,
        'villagerCount' => 2,
        'hasDoktor' => true,
        'hasDedektif' => false,
        'hasAvci' => false,
    ]);

    $result = $rooms->startGame($created['code'], $created['hostPlayerId']);

    expect($result)->not->toBeNull();
    expect($result['status'])->toBe('night');
    expect($result['nightPhase'])->toBe('vampires');
    expect($result['nightNumber'])->toBe(1);
});

it('distributes roles with correct alignments', function () {
    Cache::flush();

    $rooms = app(VampireRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $rooms->joinRoom($created['code'], 'P2', null, 'sky', '🐺');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');
    $rooms->joinRoom($created['code'], 'P4', null, 'green', '👑');

    $rooms->updateConfig($created['code'], $created['hostPlayerId'], [
        'vampireCount' => 1,
        'villagerCount' => 2,
        'hasDoktor' => true,
        'hasDedektif' => false,
        'hasAvci' => false,
    ]);

    $result = $rooms->startGame($created['code'], $created['hostPlayerId']);
    $players = (array) $result['players'];

    $vampires = array_filter($players, fn ($p) => $p['alignment'] === 'vampire');
    $villagers = array_filter($players, fn ($p) => $p['alignment'] === 'villager');

    expect($vampires)->toHaveCount(1);
    expect($villagers)->toHaveCount(3);

    foreach ($vampires as $v) {
        expect($v['role'])->toBe(VampireRole::Vampire->value);
    }

    $villagerRoles = array_column(array_values($villagers), 'role');
    expect(in_array(VampireRole::Villager->value, $villagerRoles, true))->toBeTrue();
    expect(in_array(VampireRole::Doctor->value, $villagerRoles, true))->toBeTrue();
});

it('does not expose role or alignment in the public players array', function () {
    Cache::flush();

    $rooms = app(VampireRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $joined = $rooms->joinRoom($created['code'], 'P2');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');
    $rooms->joinRoom($created['code'], 'P4', null, 'green', '👑');

    $rooms->updateConfig($created['code'], $created['hostPlayerId'], [
        'vampireCount' => 1,
        'villagerCount' => 3,
        'hasDoktor' => false,
        'hasDedektif' => false,
        'hasAvci' => false,
    ]);

    $rooms->startGame($created['code'], $created['hostPlayerId']);

    $component = Livewire::test('games.vampire.room', ['roomCode' => $created['code']])
        ->call('refreshRoom');

    $players = $component->get('players');

    expect($players)->toBeArray();
    expect(json_encode($players))->not->toContain('"role"');
    expect(json_encode($players))->not->toContain('"alignment"');
});

it('computes night vote majority and kills target', function () {
    Cache::flush();

    $rooms = app(VampireRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $p2 = $rooms->joinRoom($created['code'], 'P2');
    $p3 = $rooms->joinRoom($created['code'], 'P3');
    $p4 = $rooms->joinRoom($created['code'], 'P4');

    $rooms->updateConfig($created['code'], $created['hostPlayerId'], [
        'vampireCount' => 1,
        'villagerCount' => 3,
        'hasDoktor' => false,
        'hasDedektif' => false,
        'hasAvci' => false,
    ]);

    $rooms->startGame($created['code'], $created['hostPlayerId']);

    $room = $rooms->getRoom($created['code']);
    $players = (array) $room['players'];

    $vampireId = array_key_first(array_filter($players, fn ($p) => $p['alignment'] === 'vampire'));
    $villagerIds = array_keys(array_filter($players, fn ($p) => $p['alignment'] === 'villager'));
    $target = $villagerIds[0];

    $rooms->castNightVote($created['code'], $vampireId, $target);
    $result = $rooms->resolveDawn($created['code'], $created['hostPlayerId']);

    expect($result)->not->toBeNull();
    expect($result['players'][$target]['alive'])->toBeFalse();
});

it('doctor protection cancels the kill', function () {
    Cache::flush();

    $rooms = app(VampireRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $rooms->joinRoom($created['code'], 'P2', null, 'sky', '🐺');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');
    $rooms->joinRoom($created['code'], 'P4', null, 'green', '👑');

    $rooms->updateConfig($created['code'], $created['hostPlayerId'], [
        'vampireCount' => 1,
        'villagerCount' => 2,
        'hasDoktor' => true,
        'hasDedektif' => false,
        'hasAvci' => false,
    ]);

    $rooms->startGame($created['code'], $created['hostPlayerId']);

    $room = $rooms->getRoom($created['code']);
    $players = (array) $room['players'];

    $vampireId = array_key_first(array_filter($players, fn ($p) => $p['alignment'] === 'vampire'));
    $villagerIds = array_keys(array_filter($players, fn ($p) => $p['alignment'] === 'villager'));
    $target = $villagerIds[0];
    $doctorId = array_key_first(array_filter($players, fn ($p) => $p['role'] === VampireRole::Doctor->value));

    // Vampire votes to kill target
    $rooms->castNightVote($created['code'], $vampireId, $target);

    // Doctor protects the same target (Simultaneous night, no advancePhase call)
    $rooms->doctorProtect($created['code'], $doctorId, $target);

    $result = $rooms->resolveDawn($created['code'], $created['hostPlayerId']);

    expect($result)->not->toBeNull();
    expect($result['players'][$target]['alive'])->toBeTrue();
    expect($result['nightResult']['killedId'])->toBe($target);
    expect($result['nightResult']['savedById'])->toBe($target);
});

it('triggers HunterLastShot when hunter is killed at night', function () {
    Cache::flush();

    $rooms = app(VampireRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $rooms->joinRoom($created['code'], 'P2', null, 'sky', '🐺');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');
    $rooms->joinRoom($created['code'], 'P4', null, 'green', '👑');

    $rooms->updateConfig($created['code'], $created['hostPlayerId'], [
        'vampireCount' => 1,
        'villagerCount' => 2,
        'hasDoktor' => false,
        'hasDedektif' => false,
        'hasAvci' => true,
    ]);

    $rooms->startGame($created['code'], $created['hostPlayerId']);

    $room = $rooms->getRoom($created['code']);
    $players = (array) $room['players'];

    $vampireId = array_key_first(array_filter($players, fn ($p) => $p['alignment'] === 'vampire'));
    $hunterId = array_key_first(array_filter($players, fn ($p) => $p['role'] === VampireRole::Hunter->value));

    $rooms->castNightVote($created['code'], $vampireId, $hunterId);
    $result = $rooms->resolveDawn($created['code'], $created['hostPlayerId']);

    expect($result)->not->toBeNull();
    expect($result['status'])->toBe('hunter_last_shot');
    expect($result['nightResult']['hunterTriggered'])->toBeTrue();
});

it('villagers win when all vampires are eliminated', function () {
    Cache::flush();

    $rooms = app(VampireRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $rooms->joinRoom($created['code'], 'P2', null, 'sky', '🐺');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');
    $rooms->joinRoom($created['code'], 'P4', null, 'green', '👑');

    $rooms->updateConfig($created['code'], $created['hostPlayerId'], [
        'vampireCount' => 1,
        'villagerCount' => 3,
        'hasDoktor' => false,
        'hasDedektif' => false,
        'hasAvci' => false,
    ]);

    $rooms->startGame($created['code'], $created['hostPlayerId']);

    $room = $rooms->getRoom($created['code']);
    $players = (array) $room['players'];

    $vampireId = array_key_first(array_filter($players, fn ($p) => $p['alignment'] === 'vampire'));
    $villagerIds = array_keys(array_filter($players, fn ($p) => $p['alignment'] === 'villager'));

    // Must go through Night → Dawn → Day before voting
    // No night votes cast, so resolveDawn → no kill → Dawn
    $rooms->resolveDawn($created['code'], $created['hostPlayerId']);
    $rooms->startDay($created['code'], $created['hostPlayerId']);
    $rooms->startDayVoting($created['code'], $created['hostPlayerId']);

    foreach ($villagerIds as $voterId) {
        $rooms->castDayVote($created['code'], $voterId, $vampireId);
    }

    $rooms->revealDayVotes($created['code'], $created['hostPlayerId']);
    $result = $rooms->confirmDayElimination($created['code'], $created['hostPlayerId'], true);

    expect($result)->not->toBeNull();
    expect($result['status'])->toBe('game_over');
    expect($result['winner'])->toBe('villagers');
});

it('vampires win when their count equals villager count', function () {
    Cache::flush();

    $rooms = app(VampireRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $rooms->joinRoom($created['code'], 'P2', null, 'sky', '🐺');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');
    $rooms->joinRoom($created['code'], 'P4', null, 'green', '👑');

    $rooms->updateConfig($created['code'], $created['hostPlayerId'], [
        'vampireCount' => 1,
        'villagerCount' => 3,
        'hasDoktor' => false,
        'hasDedektif' => false,
        'hasAvci' => false,
    ]);

    $rooms->startGame($created['code'], $created['hostPlayerId']);

    // Kill 2 villagers at night (two separate nights)
    $room = $rooms->getRoom($created['code']);
    $players = (array) $room['players'];

    $vampireId = array_key_first(array_filter($players, fn ($p) => $p['alignment'] === 'vampire'));
    $villagerIds = array_keys(array_filter($players, fn ($p) => $p['alignment'] === 'villager'));

    // Night 1: kill villager 1
    $rooms->castNightVote($created['code'], $vampireId, $villagerIds[0]);
    $rooms->resolveDawn($created['code'], $created['hostPlayerId']);

    // proceed through day without eliminating anyone
    $rooms->startDay($created['code'], $created['hostPlayerId']);
    $rooms->startDayVoting($created['code'], $created['hostPlayerId']);
    $rooms->revealDayVotes($created['code'], $created['hostPlayerId']);
    $rooms->confirmDayElimination($created['code'], $created['hostPlayerId'], false);

    // Night 2: kill villager 2 (now 1 vampire vs 1 villager → vampires win)
    $rooms->castNightVote($created['code'], $vampireId, $villagerIds[1]);
    $result = $rooms->resolveDawn($created['code'], $created['hostPlayerId']);

    expect($result)->not->toBeNull();
    expect($result['status'])->toBe('game_over');
    expect($result['winner'])->toBe('vampires');
});

it('returns to lobby after new round and resets roles', function () {
    Cache::flush();

    $rooms = app(VampireRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $rooms->joinRoom($created['code'], 'P2', null, 'sky', '🐺');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');
    $rooms->joinRoom($created['code'], 'P4', null, 'green', '👑');

    $rooms->updateConfig($created['code'], $created['hostPlayerId'], [
        'vampireCount' => 1,
        'villagerCount' => 3,
        'hasDoktor' => false,
        'hasDedektif' => false,
        'hasAvci' => false,
    ]);

    $rooms->startGame($created['code'], $created['hostPlayerId']);
    $rooms->declareGameOver($created['code'], $created['hostPlayerId'], 'host_declared');

    $backToLobby = $rooms->startNewRound($created['code'], $created['hostPlayerId']);

    expect($backToLobby)->not->toBeNull();
    expect($backToLobby['status'])->toBe('lobby');
    expect($backToLobby['winner'])->toBeNull();
    expect($backToLobby['nightNumber'])->toBe(0);

    foreach ($backToLobby['players'] as $player) {
        expect($player['role'])->toBeNull();
        expect($player['alignment'])->toBeNull();
        expect($player['alive'])->toBeTrue();
        expect($player['color'])->not->toBeEmpty();
        expect($player['emoji'])->not->toBeEmpty();
    }
});
