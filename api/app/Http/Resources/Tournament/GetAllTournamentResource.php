<?php

namespace App\Http\Resources\Tournament;


use App\Model\TagTeam;
use App\Model\Team;
use Illuminate\Http\Resources\Json\Resource;

class GetAllTournamentResource extends Resource
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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tournament_start' => date('F Y', strtotime($this->tournament_start)),
            'tournament_end' => date('F Y', strtotime($this->tournament_end)),
            'current_state' => $this->getCurrentState(),
            'teams_reached' => intval($teamsReached),
            'teams_to_reach' => intval($this->teams_to_reach)
        ];
    }
}