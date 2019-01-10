<?php

namespace App\Services;

use App\Http\Resources\Tournament\TournamentDetailsResource;
use App\Http\Resources\Tournament\TournamentResource;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface TournamentService
{
    public function addOrganizer(string $email, string $tournamentId): TournamentResource;

    public function create(
        int $lanId,
        string $name,
        DateTime $tournamentStart,
        DateTime $tournamentEnd,
        int $playersToReach,
        int $teamsToReach,
        string $rules,
        ?int $price
    ): TournamentDetailsResource;

    public function delete(string $tournamentId): TournamentResource;

    public function edit(
        int $tournamentId,
        ?string $name,
        ?DateTime $tournamentStart,
        ?DateTime $tournamentEnd,
        ?int $playersToReach,
        ?int $teamsToReach,
        ?string $state,
        ?string $rules,
        ?int $price
    ): TournamentDetailsResource;

    public function getAllOrganizer(int $lanId): AnonymousResourceCollection;

    public function getAll(Request $input): AnonymousResourceCollection;

    public function get(string $tournamentId): TournamentDetailsResource;

    public function quit(string $tournamentId): TournamentResource;
}
