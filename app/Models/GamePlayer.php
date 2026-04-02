<?php

namespace App\Models;

use Database\Factories\GamePlayerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GamePlayer extends Model
{
    /** @use HasFactory<GamePlayerFactory> */
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'user_id',
        'score',
        'rank',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(GameSession::class, 'game_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
