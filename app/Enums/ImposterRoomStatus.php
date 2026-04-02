<?php

namespace App\Enums;

enum ImposterRoomStatus: string
{
    case Loading = 'loading';

    case Missing = 'missing';

    case Lobby = 'lobby';

    case Started = 'started';

    case Voting = 'voting';

    case Results = 'results';
}
