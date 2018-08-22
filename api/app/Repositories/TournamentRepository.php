<?php

namespace App\Repositories;

use App\Model\Lan;
use App\Model\Tournament;
use DateTime;
use Illuminate\Contracts\Auth\Authenticatable;

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

    public function findById(int $id): ?Tournament;

    public function associateOrganizerTournament(Authenticatable $organizer, Tournament $tournament): void;

    public function getReachedTeams(Tournament $tournament): int;
}