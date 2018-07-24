<?php

namespace App\Repositories\Implementation;

use App\Model\Lan;
use App\Model\Tournament;
use App\Repositories\TournamentRepository;
use DateTime;

class TournamentRepositoryImpl implements TournamentRepository
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
    ): Tournament
    {
        $tournament = new Tournament();
        $tournament->lan_id = $lan->id;
        $tournament->name = $name;
        $tournament->tournament_start = $tournamentStart->format('Y-m-d H:i:s');
        $tournament->tournament_end = $tournamentEnd->format('Y-m-d H:i:s');
        $tournament->players_to_reach = $playersToReach;
        $tournament->teams_to_reach = $teamsToReach;
        $tournament->rules = $rules;
        $tournament->price = $price;
        $tournament->save();

        return $tournament;
    }

    // TODO Tests
    public function findTournamentById(int $id): ?Tournament
    {
        return Tournament::find($id);
    }
}