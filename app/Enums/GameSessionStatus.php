<?php

namespace App\Enums;

enum GameSessionStatus: string
{
    case WAITING = 'waiting';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::WAITING => 'Bekliyor',
            self::ACTIVE => 'Aktif',
            self::COMPLETED => 'Tamamlandı',
            self::CANCELLED => 'İptal Edildi',
        };
    }
}
