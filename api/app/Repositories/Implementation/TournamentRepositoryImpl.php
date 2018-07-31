<?php

namespace App\Repositories\Implementation;

use App\Model\Lan;
use App\Model\OrganizerTournament;
use App\Model\Tournament;
use App\Repositories\TournamentRepository;
use DateTime;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

    public function findTournamentById(int $id): ?Tournament
    {
        return Tournament::find($id);
    }

    public function associateOrganizerTournament(Authenticatable $organizer, Tournament $tournament): void
    {
        $organizerTournament = new OrganizerTournament();
        $organizerTournament->organizer_id = $organizer->id;
        $organizerTournament->tournament_id = $tournament->id;
        $organizerTournament->save();
    }

    public function getTournamentForOrganizer(Authenticatable $user, Lan $lan): Collection
    {
        $tournamentIds = DB::table('organizer_tournament')
            ->select('tournament_id')
            ->where('organizer_id', $user->id)
            ->pluck('tournament_id')
            ->toArray();

        return Tournament::where('lan_id', $lan->id)
            ->whereIn('id', $tournamentIds)
            ->get();
    }
}