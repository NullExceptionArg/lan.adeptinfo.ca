<?php

namespace App\Repositories;

use App\Model\Lan;
use App\Model\Tournament;
use DateTime;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

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
    ): int;

    public function update(
        Tournament $tournament,
        ?string $name,
        ?string $state,
        ?DateTime $tournamentStart,
        ?DateTime $tournamentEnd,
        ?int $playersToReach,
        ?int $teamsToReach,
        ?string $rules,
        ?int $price
    ): Tournament;

    public function findById(int $id): ?Tournament;

    public function associateOrganizerTournament(int $organizerId, int $tournamentId): void;

    public function getAllTournaments(int $lanId): Collection;

    public function getReachedTeams(Tournament $tournament): int;

    public function delete(Tournament $tournament): void;

    public function quit(Tournament $tournament, Authenticatable $user): void;

    public function getOrganizerCount(Tournament $tournament): int;

    public function getTournamentsLanId(int $tournamentId): ?int;

    public function adminHasTournaments(int $userId, int $lanId): bool;
}