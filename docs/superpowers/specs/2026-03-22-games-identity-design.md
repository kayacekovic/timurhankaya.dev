# Games Identity System — Design Spec

**Date:** 2026-03-22
**Scope:** Both games (Vampire Village & Word Imposter)
**Stack:** Laravel 12 + Livewire 4, Cache-first state, polling-based real-time

---

## Problem

Users currently enter a name on every game's create/join form. There is no persistent identity — someone can use a different name each time, and there is no visual identity (color, avatar) to distinguish players in the lobby. A user can also open two tabs and join the same room with two different names, since the name field is free-form on every form.

---

## Solution Overview

Introduce a shared, session-based player identity (`games.identity`) that persists across both games. A new shared Livewire component handles identity setup and display. Game index forms read from this identity instead of asking for a name. Player data stored in Cache is extended to include `color` and `emoji`.

Because Laravel sessions are cookie-based and shared across all tabs in the same browser, `games.identity` is unified across all tabs — the same identity is used everywhere. This prevents users from entering a different name in a second tab, since the session value is always the same. Note: if the user explicitly changes their identity via the "Değiştir" button, the new identity applies in all tabs.

---

## Session Identity Structure

Key: `games.identity`

```php
[
    'name'  => string,   // max 24 characters
    'color' => string,   // one of the 8 allowed color keys (see Color Allow-List below)
    'emoji' => string,   // one of the 16 allowed emojis (see Emoji Allow-List below)
]
```

### Color Allow-List (8 options)

```
red, orange, amber, green, teal, sky, purple, pink
```

### Emoji Allow-List (16 options)

```
🐺 🧛 🕵️ 🏹 🧙 👁️ 🗡️ 🛡️ 🌙 ☀️ 🔮 👑 🦇 🩸 ⚔️ 🎭
```

---

## Architecture

### 1. Shared Identity Component

**File:** `resources/views/components/games/⚡identity.blade.php`

A Livewire anonymous class component with two display states:

**State A — No identity set:**
- Name text input (max 24)
- Color picker: 8 preset color swatches (clickable)
- Emoji picker: 16 game-themed emojis (clickable)
- "Kaydet" (Save) button → validates inputs, writes to `session('games.identity')`

**State B — Identity exists:**
- Displays `{emoji} {name}` with a color-accented card matching the chosen color
- "Değiştir" (Change) button → switches back to State A (pre-filled with current values)

Both game index pages include this component **above** their create/join forms:
```blade
<livewire:games.identity />
```

**Validation** (in the Livewire component's `save` method):
```php
$this->validate([
    'name'  => ['required', 'string', 'max:24'],
    'color' => ['required', 'string', Rule::in(['red','orange','amber','green','teal','sky','purple','pink'])],
    'emoji' => ['required', 'string', Rule::in(['🐺','🧛','🕵️','🏹','🧙','👁️','🗡️','🛡️','🌙','☀️','🔮','👑','🦇','🩸','⚔️','🎭'])],
]);
```

Both `color` and `emoji` are validated against their allow-lists **before** being written to the session or passed to any service. This prevents arbitrary strings from reaching the cache or the view layer.

### 2. Modified Index Components

**Files:** `resources/views/components/games/vampire/⚡index.blade.php`
and `resources/views/components/games/imposter/⚡index.blade.php`

Changes:
- Remove `name` public property and all `name` form fields
- `createRoom` and `joinRoom` read identity from session:
  ```php
  $identity = session('games.identity');
  // $identity['name'], $identity['color'], $identity['emoji']
  ```
- If `session('games.identity')` is null, submit buttons are disabled and a hint is shown: "Önce kimliğini ayarla"
- `color` and `emoji` are passed to service methods alongside `name`

### 3. Service Layer

**Files:** `app/Services/Vampire/VampireRoomService.php`
and `app/Services/Imposter/ImposterRoomService.php`

Updated method signatures:
```php
createRoom(string $name, ?string $password, string $color, string $emoji): array
joinRoom(string $code, string $name, ?string $password, string $color, string $emoji): ?array
```

Player data structure in Cache extended:
```php
// Before
['id' => ..., 'name' => ..., 'alive' => ..., ...]

// After
['id' => ..., 'name' => ..., 'color' => ..., 'emoji' => ..., 'alive' => ..., ...]
```

**Backward compatibility:** Cache entries written before this deploy will not have `color` or `emoji` keys. All read paths must use null-safe fallbacks: `$player['color'] ?? 'sky'` and `$player['emoji'] ?? '🎭'`.

### 4. Room Components & Views

**Files:** `resources/views/components/games/vampire/⚡room.blade.php`
and `resources/views/components/games/imposter/⚡room.blade.php`

**Player mapping** updated to expose `color` and `emoji` (with fallback defaults):
```php
[
    'id'     => ...,
    'name'   => ...,
    'color'  => (string) ($player['color'] ?? 'sky'),
    'emoji'  => (string) ($player['emoji'] ?? '🎭'),
    'isHost' => ...,
    'isMe'   => ...,
    'alive'  => ...,
]
```

**Lobby player list** rendered as:
```
[🧛]  Timurhan    (Sen) 👑
[🕵️]  Ahmet
[🐺]  Zeynep
```

Each emoji is displayed inside a small circle colored with the player's chosen color.

**Tailwind JIT — Static Color Class Map**

Tailwind's JIT compiler only includes classes detected at build time. Dynamically constructing strings like `'bg-'.$color.'-500'` would cause those classes to be purged in production. Instead, a static lookup map is used directly in the template:

```blade
@php
$colorClasses = [
    'red'    => 'bg-red-500',
    'orange' => 'bg-orange-500',
    'amber'  => 'bg-amber-500',
    'green'  => 'bg-green-500',
    'teal'   => 'bg-teal-500',
    'sky'    => 'bg-sky-500',
    'purple' => 'bg-purple-500',
    'pink'   => 'bg-pink-500',
];
@endphp
```

All 8 color class strings are present as literal strings in the template, making them visible to the JIT compiler.

**In-room join form** (for users who navigate directly to a room URL without being a member): the name input in this form is removed; identity is read from `session('games.identity')`. If the session identity is null, the join button is disabled with a hint directing the user to the game index to set their identity first.

**Mid-game session behavior:** Once a player has joined a room, their `name`, `color`, and `emoji` are stored in the Cache under their player ID. The session identity is only read at join time — after joining, the room's Cache entry is the source of truth for display. Clearing `games.identity` from the session after joining does not affect in-room display for other players.

---

## Tests

**Files:**
- `tests/Feature/VampireRoomTest.php` — all 12 existing tests updated
- `tests/Feature/ImposterRoomTest.php` — all existing tests updated

All calls to `createRoom` and `joinRoom` updated to include fixture values:
```php
$rooms->createRoom('Host', null, 'red', '🧛');
$rooms->joinRoom($code, 'Guest', null, 'sky', '🐺');
```

No new test cases are required — existing coverage is sufficient for the service layer changes.

---

## What Is NOT Changing

- Session key structure for room membership (`vampire.player.{CODE}`, `imposter.player.{CODE}`) — unchanged
- Room service cache keys and state machine — unchanged
- Password protection flow — unchanged
- Polling / real-time mechanism — unchanged

---

## File Change Summary

| File | Change |
|------|--------|
| `resources/views/components/games/⚡identity.blade.php` | **New** — shared identity Livewire component |
| `resources/views/components/games/vampire/⚡index.blade.php` | Remove name field, read from session, add identity component |
| `resources/views/components/games/imposter/⚡index.blade.php` | Same as above |
| `resources/views/components/games/vampire/⚡room.blade.php` | Add color/emoji to player mapping; update player list UI; update in-room join form |
| `resources/views/components/games/imposter/⚡room.blade.php` | Same as above |
| `app/Services/Vampire/VampireRoomService.php` | Add color/emoji params, extend player data |
| `app/Services/Imposter/ImposterRoomService.php` | Same as above |
| `tests/Feature/VampireRoomTest.php` | Update existing tests for new params |
| `tests/Feature/ImposterRoomTest.php` | Update existing tests for new params |
