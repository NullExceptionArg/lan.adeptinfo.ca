<?php

namespace App\Http\Resources\Tournament;

use App\Http\Resources\Team\GetTournamentDetailsTeamResource;
use App\Model\TagTeam;
use App\Model\Team;
use Illuminate\Http\Resources\Json\Resource;

class TournamentDetailsResource extends Resource
{
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
        $teamsReached = 0;

        foreach ($teams as $team) {
            $playersReached = TagTeam::where('team_id', $team->id)->count();
            if ($playersReached >= $this->players_to_reach) {
                $teamsReached++;
                break;
            }
        }

        $teams->map(function ($team) {
            $team['lan_id'] = $this->lan_id;
            return $team;
        });

        return [
            'id' => intval($this->id),
            'name' => $this->name,
            'rules' => $this->rules,
            'price' => intval($this->price),
            'tournament_start' => date('Y-m-d H:i:s', strtotime($this->tournament_start)),
            'tournament_end' => date('Y-m-d H:i:s', strtotime($this->tournament_end)),
            'teams_to_reach' => intval($this->teams_to_reach),
            'teams_reached' => $teamsReached,
            'players_to_reach' => intval($this->players_to_reach),
            'state' => $this->getCurrentState(),
            'teams' => GetTournamentDetailsTeamResource::collection($teams)
        ];
    }
}
