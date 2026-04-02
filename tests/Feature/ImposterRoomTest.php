<?php

use App\Services\Imposter\ImposterRoomService;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

it('creates a room with a host', function () {
    Cache::flush();

    $rooms = app(ImposterRoomService::class);
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

it('starts a game and assigns exactly one imposter', function () {
    Cache::flush();

    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $joined = $rooms->joinRoom($created['code'], 'Guest', null, 'sky', '🐺');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');

    expect($joined)->not->toBeNull();

    $started = $rooms->startGame($created['code'], $created['hostPlayerId']);
    expect($started)->not->toBeNull();
    expect($started['status'])->toBe('started');

    $players = (array) $started['players'];
    $roles = collect($players)->pluck('role')->all();

    expect($roles)->toHaveCount(3);
    expect(array_values(array_filter($roles, fn (mixed $r) => $r === 'imposter')))->toHaveCount(1);
});

it('does not expose other players roles to the browser', function () {
    Cache::flush();

    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $joined = $rooms->joinRoom($created['code'], 'Guest', null, 'sky', '🐺');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');

    expect($joined)->not->toBeNull();

    $rooms->startGame($created['code'], $created['hostPlayerId']);

    $component = Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom');

    $players = $component->get('players');

    expect($players)->toBeArray();
    expect(json_encode($players))->not->toContain('"role"');
    expect(json_encode($players))->not->toContain('"word"');
});

it('supports voting and revealing results', function () {
    Cache::flush();

    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $joined = $rooms->joinRoom($created['code'], 'Guest', null, 'sky', '🐺');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');

    expect($joined)->not->toBeNull();

    $started = $rooms->startGame($created['code'], $created['hostPlayerId']);
    expect($started)->not->toBeNull();
    expect($started['status'])->toBe('started');

    $voting = $rooms->startVoting($created['code'], $created['hostPlayerId']);
    expect($voting)->not->toBeNull();
    expect($voting['status'])->toBe('voting');

    $playerIds = array_keys((array) $started['players']);

    // Vote in shuffled round-robin order by following currentVoterId
    $voted = [];
    for ($i = 0; $i < count($playerIds); $i++) {
        $room = $rooms->getRoom($created['code']);
        $currentVoterId = (string) ($room['currentVoterId'] ?? '');
        $targetId = collect($playerIds)->first(fn ($id) => $id !== $currentVoterId);
        $rooms->castVote($created['code'], $currentVoterId, $targetId);
        $voted[] = $currentVoterId;
    }

    $results = $rooms->revealVotes($created['code'], $created['hostPlayerId']);
    expect($results)->not->toBeNull();
    expect($results['status'])->toBe('results');

    $votes = (array) ($results['votes'] ?? []);
    expect($votes)->toHaveKeys($playerIds);
});

it('returns to lobby after new round so new players can join', function () {
    Cache::flush();

    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $joined = $rooms->joinRoom($created['code'], 'Guest', null, 'sky', '🐺');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');

    expect($joined)->not->toBeNull();

    $rooms->startGame($created['code'], $created['hostPlayerId']);
    $rooms->startVoting($created['code'], $created['hostPlayerId']);
    $rooms->castVote($created['code'], $created['hostPlayerId'], (string) $joined['playerId']);
    $rooms->castVote($created['code'], (string) $joined['playerId'], $created['hostPlayerId']);
    $rooms->revealVotes($created['code'], $created['hostPlayerId']);

    $backToLobby = $rooms->startNewRound($created['code'], $created['hostPlayerId']);
    expect($backToLobby)->not->toBeNull();
    expect($backToLobby['status'])->toBe('lobby');
    expect($backToLobby['word'])->toBeNull();
    expect($backToLobby['votes'])->toBe([]);

    $room = $rooms->getRoom($created['code']);
    foreach ($room['players'] as $player) {
        expect($player['role'])->toBeNull();
    }

    $newPlayer = $rooms->joinRoom($created['code'], 'Newcomer', null, 'green', '👑');
    expect($newPlayer)->not->toBeNull();
    expect($room['players'])->toHaveKey($created['hostPlayerId']);
    $afterJoin = $rooms->getRoom($created['code']);
    expect($afterJoin['players'])->toHaveCount(4);
});

it('allows retracting a vote during voting', function () {
    Cache::flush();

    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $joined = $rooms->joinRoom($created['code'], 'Guest', null, 'sky', '🐺');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');

    expect($joined)->not->toBeNull();

    $started = $rooms->startGame($created['code'], $created['hostPlayerId']);
    expect($started)->not->toBeNull();

    $rooms->startVoting($created['code'], $created['hostPlayerId']);

    // The first player in the voter queue is currentVoterId
    $room = $rooms->getRoom($created['code']);
    $firstVoterId = (string) ($room['currentVoterId'] ?? '');
    $playerIds = array_keys((array) $started['players']);
    $targetId = $playerIds[0] !== $firstVoterId ? $playerIds[0] : $playerIds[1];

    $rooms->castVote($created['code'], $firstVoterId, $targetId);
    $room = $rooms->getRoom($created['code']);
    expect($room['votes'])->toHaveKey($firstVoterId);

    $rooms->retractVote($created['code'], $firstVoterId);
    $room = $rooms->getRoom($created['code']);
    expect($room['votes'])->not->toHaveKey($firstVoterId);
});

it('does not expose the revealed word to non-joined visitors', function () {
    Cache::flush();

    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $joined = $rooms->joinRoom($created['code'], 'Guest', null, 'sky', '🐺');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');

    expect($joined)->not->toBeNull();

    $started = $rooms->startGame($created['code'], $created['hostPlayerId']);
    expect($started)->not->toBeNull();

    $rooms->startVoting($created['code'], $created['hostPlayerId']);
    $rooms->revealVotes($created['code'], $created['hostPlayerId']);

    $component = Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom');

    expect($component->get('isJoined'))->toBeFalse();
    expect($component->get('revealedWord'))->toBeNull();
    expect($component->get('imposterName'))->toBeNull();
});

it('shows the word only to joined crew during the round', function () {
    Cache::flush();

    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host', null, 'red', '🧛');
    $joined = $rooms->joinRoom($created['code'], 'Guest', null, 'sky', '🐺');
    $rooms->joinRoom($created['code'], 'P3', null, 'purple', '🕵️');

    expect($joined)->not->toBeNull();

    $started = $rooms->startGame($created['code'], $created['hostPlayerId']);
    expect($started)->not->toBeNull();
    expect($started['status'])->toBe('started');

    $players = (array) ($started['players'] ?? []);
    $word = (string) ($started['word'] ?? '');

    $crew = collect($players)->first(fn (array $p): bool => ($p['role'] ?? null) === 'crew');
    $imposter = collect($players)->first(fn (array $p): bool => ($p['role'] ?? null) === 'imposter');

    $crewId = is_array($crew) ? (string) ($crew['id'] ?? '') : '';
    $imposterId = is_array($imposter) ? (string) ($imposter['id'] ?? '') : '';

    expect($word)->not->toBeEmpty();
    expect($crewId)->not->toBeEmpty();
    expect($imposterId)->not->toBeEmpty();

    $this->withSession(['imposter.player.'.$created['code'] => $crewId]);

    $crew = Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom');

    expect($crew->get('isJoined'))->toBeTrue();
    expect($crew->get('myRole'))->toBe('crew');
    expect($crew->get('myWord'))->toBe($word);

    $this->withSession(['imposter.player.'.$created['code'] => $imposterId]);

    $imposterComponent = Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom');

    expect($imposterComponent->get('isJoined'))->toBeTrue();
    expect($imposterComponent->get('myRole'))->toBe('imposter');
    expect($imposterComponent->get('myWord'))->toBeNull();
});
