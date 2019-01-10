<?php

namespace App\Repositories;

use App\Model\Tournament;
use DateTime;
use Illuminate\Support\Collection;

interface TournamentRepository
{
    public function adminHasTournaments(int $userId, int $lanId): bool;

    public function associateOrganizerTournament(int $organizerId, int $tournamentId): void;

    public function create(
        int $lanId,
        string $name,
        DateTime $tournamentStart,
        DateTime $tournamentEnd,
        int $playersToReach,
        int $teamsToReach,
        string $rules,
        ?int $price
    ): int;

    public function delete(int $tournamentId): void;

    public function deleteTournamentOrganizer(int $tournamentId, int $userId): void;

    public function findById(int $id): ?Tournament;

    public function getAllTournaments(int $lanId): Collection;

    public function getOrganizerCount(int $tournamentId): int;

    public function getReachedTeams(int $tournamentId): int;

    public function getTournamentsForOrganizer(int $userId, int $lanId): Collection;

    public function getTournamentsLanId(int $tournamentId): ?int;

    public function update(
        int $tournamentId,
        ?string $name,
        ?string $state,
        ?DateTime $tournamentStart,
        ?DateTime $tournamentEnd,
        ?int $playersToReach,
        ?int $teamsToReach,
        ?string $rules,
        ?int $price
    ): void;
}
