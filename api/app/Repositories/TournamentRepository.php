<?php

namespace App\Repositories;

use App\Model\Lan;
use App\Model\Tournament;
use DateTime;

interface TournamentRepository
{
    public function create(
        Lan $lan,
        string $name,
        DateTime $tournamentStart,
        DateTime $tournamentEnd,
        int $playersToReach,
        int $teamsToReach,
        string $rules,
        ?int $price
    ): Tournament;
}