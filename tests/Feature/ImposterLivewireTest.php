<?php

use App\Enums\ImposterRoomStatus;
use App\Services\Imposter\ImposterRoomService;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

beforeEach(fn () => Cache::flush());

// ─── Index Livewire Component ─────────────────────────────────────────────────

test('index: shows error when creating room without identity', function () {
    Livewire::test('games.imposter.index')
        ->call('createRoom', app(ImposterRoomService::class))
        ->assertHasNoErrors()
        ->assertSet('error', (string) __('imposter.identityRequired'));
});

test('index: shows error when joining room without identity', function () {
    Livewire::test('games.imposter.index')
        ->call('joinRoom', app(ImposterRoomService::class))
        ->assertHasNoErrors()
        ->assertSet('error', (string) __('imposter.identityRequired'));
});

// Note: Index redirect and session-joined tests are covered in ImposterRoomTest.php

// ─── Room Livewire Component ─────────────────────────────────────────────────

test('room: shows missing room state for non-existent code', function () {
    Livewire::test('games.imposter.room', ['roomCode' => 'NOSUCH'])
        ->assertSet('roomMissing', true)
        ->assertSet('status', ImposterRoomStatus::Missing);
});

test('room: join requires identity', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('join', $rooms)
        ->assertSet('error', (string) __('imposter.joinError'));
});

test('room: join fails with wrong password', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host', 'correct');
    $this->withSession(['games.identity' => ['name' => 'Player', 'color' => 'sky', 'emoji' => '🎭']]);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->set('joinPassword', 'wrong')
        ->call('join', $rooms)
        ->assertHasErrors('joinPassword');
});

test('room: start fails with fewer than 3 players', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $joined = $rooms->joinRoom($created['code'], 'Guest');
    $this->withSession([
        'games.identity' => ['name' => 'Player', 'color' => 'sky', 'emoji' => '🎭'],
        'imposter.player.'.$created['code'] => $joined['playerId'],
    ]);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->call('start', $rooms)
        ->assertSet('error', (string) __('imposter.errorMinPlayers'));
});

test('room: start succeeds with 3 players and host', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $rooms->joinRoom($created['code'], 'P1');
    $rooms->joinRoom($created['code'], 'P2');
    $this->withSession([
        'games.identity' => ['name' => 'Player', 'color' => 'sky', 'emoji' => '🎭'],
        'imposter.player.'.$created['code'] => $created['hostPlayerId'],
    ]);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->call('start', $rooms)
        ->assertSet('status', ImposterRoomStatus::Started);
});

test('room: voting can be started after game starts', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $rooms->joinRoom($created['code'], 'P1');
    $rooms->joinRoom($created['code'], 'P2');
    $rooms->startGame($created['code'], $created['hostPlayerId']);
    $this->withSession([
        'games.identity' => ['name' => 'Player', 'color' => 'sky', 'emoji' => '🎭'],
        'imposter.player.'.$created['code'] => $created['hostPlayerId'],
    ]);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->call('startVoting', $rooms)
        ->assertSet('status', ImposterRoomStatus::Voting);
});

test('room: voting participant can vote and see their vote', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $rooms->joinRoom($created['code'], 'P1');
    $rooms->joinRoom($created['code'], 'P2');
    $rooms->startGame($created['code'], $created['hostPlayerId']);
    $rooms->startVoting($created['code'], $created['hostPlayerId']);

    $room = $rooms->getRoom($created['code']);
    $playerIds = array_keys((array) $room['players']);
    $firstVoterId = (string) $room['currentVoterId'];
    $targetId = collect($playerIds)->first(fn ($id) => $id !== $firstVoterId);

    $this->withSession([
        'games.identity' => ['name' => 'Player', 'color' => 'sky', 'emoji' => '🎭'],
        'imposter.player.'.$created['code'] => $firstVoterId,
    ]);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->call('vote', $rooms, $targetId)
        ->assertSet('myVote', $targetId);
});

test('room: imposterGuessed transitions to results with winner imposter', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $rooms->joinRoom($created['code'], 'P1');
    $rooms->joinRoom($created['code'], 'P2');
    $rooms->startGame($created['code'], $created['hostPlayerId']);
    $this->withSession([
        'games.identity' => ['name' => 'Player', 'color' => 'sky', 'emoji' => '🎭'],
        'imposter.player.'.$created['code'] => $created['hostPlayerId'],
    ]);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->call('imposterGuessed', $rooms)
        ->assertSet('status', ImposterRoomStatus::Results)
        ->assertSet('winner', 'imposter');
});

test('room: revealVotes transitions to results', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $rooms->joinRoom($created['code'], 'P1');
    $rooms->joinRoom($created['code'], 'P2');
    $rooms->startGame($created['code'], $created['hostPlayerId']);
    $rooms->startVoting($created['code'], $created['hostPlayerId']);
    $this->withSession([
        'games.identity' => ['name' => 'Player', 'color' => 'sky', 'emoji' => '🎭'],
        'imposter.player.'.$created['code'] => $created['hostPlayerId'],
    ]);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->call('revealVotes', $rooms)
        ->assertSet('status', ImposterRoomStatus::Results);
});

test('room: newRound resets room to lobby', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $rooms->joinRoom($created['code'], 'P1');
    $rooms->joinRoom($created['code'], 'P2');
    $rooms->startGame($created['code'], $created['hostPlayerId']);
    $rooms->startVoting($created['code'], $created['hostPlayerId']);
    $rooms->revealVotes($created['code'], $created['hostPlayerId']);
    $this->withSession([
        'games.identity' => ['name' => 'Player', 'color' => 'sky', 'emoji' => '🎭'],
        'imposter.player.'.$created['code'] => $created['hostPlayerId'],
    ]);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->call('newRound', $rooms)
        ->assertSet('status', ImposterRoomStatus::Lobby);
});

test('room: kickPlayer opens and closes confirmation', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $joined = $rooms->joinRoom($created['code'], 'Guest');
    $this->withSession([
        'games.identity' => ['name' => 'Player', 'color' => 'sky', 'emoji' => '🎭'],
        'imposter.player.'.$created['code'] => $created['hostPlayerId'],
    ]);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->call('openKickConfirm', $joined['playerId'], 'Guest')
        ->assertSet('confirmKickPlayerId', $joined['playerId'])
        ->assertSet('confirmKickPlayerName', 'Guest')
        ->call('closeKickConfirm')
        ->assertSet('confirmKickPlayerId', null)
        ->assertSet('confirmKickPlayerName', null);
});

test('room: openPlayers and closePlayers toggle', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $this->withSession([
        'games.identity' => ['name' => 'Player', 'color' => 'sky', 'emoji' => '🎭'],
        'imposter.player.'.$created['code'] => $created['hostPlayerId'],
    ]);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->assertSet('showPlayers', false)
        ->call('openPlayers')
        ->assertSet('showPlayers', true)
        ->call('closePlayers')
        ->assertSet('showPlayers', false);
});

test('room: toggleRole flips roleVisibility', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $this->withSession([
        'games.identity' => ['name' => 'Player', 'color' => 'sky', 'emoji' => '🎭'],
        'imposter.player.'.$created['code'] => $created['hostPlayerId'],
    ]);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->assertSet('roleVisible', true)
        ->call('toggleRole')
        ->assertSet('roleVisible', false)
        ->call('toggleRole')
        ->assertSet('roleVisible', true);
});

test('room: winner is imposter when imposter guessed word', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $rooms->joinRoom($created['code'], 'P1');
    $rooms->joinRoom($created['code'], 'P2');
    $rooms->startGame($created['code'], $created['hostPlayerId']);
    $rooms->imposterGuessedWord($created['code'], $created['hostPlayerId']);

    $room = $rooms->getRoom($created['code']);
    $playerId = array_key_first((array) $room['players']);
    $this->withSession([
        'games.identity' => ['name' => 'Player', 'color' => 'sky', 'emoji' => '🎭'],
        'imposter.player.'.$created['code'] => $playerId,
    ]);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->assertSet('status', ImposterRoomStatus::Results)
        ->assertSet('winner', 'imposter');
});

test('room: winner is crew when imposter gets unanimous vote', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $rooms->joinRoom($created['code'], 'P1');
    $rooms->joinRoom($created['code'], 'P2');
    $rooms->startGame($created['code'], $created['hostPlayerId']);
    $rooms->startVoting($created['code'], $created['hostPlayerId']);

    $room = $rooms->getRoom($created['code']);
    $playerIds = array_keys((array) $room['players']);
    $imposterId = collect($playerIds)
        ->first(fn ($id) => ($room['players'][$id]['role'] ?? null) === 'imposter');
    $crewTargetId = collect($playerIds)
        ->first(fn ($id) => $id !== $imposterId);

    while (($currentVoterId = $room['currentVoterId'] ?? null) !== null) {
        $targetId = $currentVoterId === $imposterId ? $crewTargetId : $imposterId;
        $rooms->castVote($created['code'], $currentVoterId, $targetId);
        $room = $rooms->getRoom($created['code']);
    }

    $rooms->revealVotes($created['code'], $created['hostPlayerId']);
    $this->withSession([
        'games.identity' => ['name' => 'Player', 'color' => 'sky', 'emoji' => '🎭'],
        'imposter.player.'.$created['code'] => $playerIds[0],
    ]);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->assertSet('status', ImposterRoomStatus::Results)
        ->assertSet('winner', 'crew');
});

test('room: winner is imposter on tie vote', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $rooms->joinRoom($created['code'], 'P1');
    $rooms->joinRoom($created['code'], 'P2');
    $rooms->startGame($created['code'], $created['hostPlayerId']);
    $rooms->startVoting($created['code'], $created['hostPlayerId']);

    $room = $rooms->getRoom($created['code']);
    $playerIds = array_keys((array) $room['players']);

    while (($currentVoterId = $room['currentVoterId'] ?? null) !== null) {
        $currentIndex = array_search($currentVoterId, $playerIds, true);
        $targetId = $playerIds[(($currentIndex === false ? 0 : $currentIndex) + 1) % count($playerIds)];
        $rooms->castVote($created['code'], $currentVoterId, $targetId);
        $room = $rooms->getRoom($created['code']);
    }

    $rooms->revealVotes($created['code'], $created['hostPlayerId']);
    $this->withSession([
        'games.identity' => ['name' => 'Player', 'color' => 'sky', 'emoji' => '🎭'],
        'imposter.player.'.$created['code'] => $playerIds[0],
    ]);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->assertSet('status', ImposterRoomStatus::Results)
        ->assertSet('winner', 'imposter');
});

test('room: winner is imposter when no votes cast', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $rooms->joinRoom($created['code'], 'P1');
    $rooms->joinRoom($created['code'], 'P2');
    $rooms->startGame($created['code'], $created['hostPlayerId']);
    $rooms->startVoting($created['code'], $created['hostPlayerId']);
    $rooms->revealVotes($created['code'], $created['hostPlayerId']);

    $room = $rooms->getRoom($created['code']);
    $playerId = array_key_first((array) $room['players']);
    $this->withSession([
        'games.identity' => ['name' => 'Player', 'color' => 'sky', 'emoji' => '🎭'],
        'imposter.player.'.$created['code'] => $playerId,
    ]);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->assertSet('winner', 'imposter');
});

test('room: shows role only to joined players during started game', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $rooms->joinRoom($created['code'], 'P1');
    $rooms->joinRoom($created['code'], 'P2');
    $rooms->startGame($created['code'], $created['hostPlayerId']);

    // Non-joined visitor
    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->assertSet('isJoined', false)
        ->assertSet('myRole', null);
});

test('room: shows word only to crew, not imposter, during started game', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $rooms->joinRoom($created['code'], 'P1');
    $rooms->joinRoom($created['code'], 'P2');
    $rooms->startGame($created['code'], $created['hostPlayerId']);

    $room = $rooms->getRoom($created['code']);
    $word = $room['word'];
    $crewId = collect((array) $room['players'])
        ->first(fn ($p) => ($p['role'] ?? null) === 'crew')['id'];
    $imposterId = collect((array) $room['players'])
        ->first(fn ($p) => ($p['role'] ?? null) === 'imposter')['id'];

    $this->withSession(['imposter.player.'.$created['code'] => $crewId]);
    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->assertSet('myRole', 'crew')
        ->assertSet('myWord', $word);

    $this->withSession(['imposter.player.'.$created['code'] => $imposterId]);
    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->assertSet('myRole', 'imposter')
        ->assertSet('myWord', null);
});

test('room: revealed word is hidden from non-joined visitor in results', function () {
    $rooms = app(ImposterRoomService::class);
    $created = $rooms->createRoom('Host');
    $rooms->joinRoom($created['code'], 'P1');
    $rooms->joinRoom($created['code'], 'P2');
    $rooms->startGame($created['code'], $created['hostPlayerId']);
    $rooms->startVoting($created['code'], $created['hostPlayerId']);
    $rooms->revealVotes($created['code'], $created['hostPlayerId']);

    Livewire::test('games.imposter.room', ['roomCode' => $created['code']])
        ->call('refreshRoom', $rooms)
        ->assertSet('isJoined', false)
        ->assertSet('revealedWord', null)
        ->assertSet('imposterName', null);
});
