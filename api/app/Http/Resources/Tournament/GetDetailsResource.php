<?php

namespace App\Http\Resources\Tournament;

use App\Http\Resources\Team\GetTournamentDetailsTeamResource;
use App\Model\Team;
use App\Model\Tournament;
use Illuminate\Http\Resources\Json\Resource;

class GetDetailsResource extends Resource
{
    protected $teamsReached;

    public function __construct(Tournament $resource, int $teamsReached)
    {
        $this->teamsReached = $teamsReached;
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $teams = Team::where('tournament_id', $this->id)
            ->get();
        $teams->map(function ($team) {
            $team['lan_id'] = $this->lan_id;
            return $team;
        });

        return [
            'id' => intval($this->id),
            'name' => $this->name,
            'rules' => $this->rules,
            'price' => $this->price,
            'tournament_start' => $this->tournament_start,
            'tournament_end' => $this->tournament_end,
            'teams_to_reach' => intval($this->teams_to_reach),
            'teams_reached' => intval($this->teamsReached),
            'players_to_reach' => $this->players_to_reach,
            'state' => $this->getCurrentState(),
            'teams' => GetTournamentDetailsTeamResource::collection($teams)
        ];
    }
}