<?php

namespace App\Models;

use Database\Factories\GameFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    /** @use HasFactory<GameFactory> */
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'min_players',
        'max_players',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(GameSession::class);
    }
}
