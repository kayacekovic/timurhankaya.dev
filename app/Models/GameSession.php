<?php

namespace App\Models;

use App\Enums\GameSessionStatus;
use Database\Factories\GameSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameSession extends Model
{
    /** @use HasFactory<GameSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'game_id',
        'status',
        'room_code',
        'metadata',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'status' => GameSessionStatus::class,
        'metadata' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(GamePlayer::class);
    }
}
