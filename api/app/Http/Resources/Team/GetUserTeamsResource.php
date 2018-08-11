<?php

namespace App\Http\Resources\Lan;


use App\Model\Request;
use App\Model\TagTeam;
use App\Model\Tournament;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GetUserTeamsResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $playersReached = TagTeam::where('team_id', $this->id)
            ->count();
        $tournament = Tournament::find($this->tournament_id);
        $requests = Request::where('team_id', $this->id)->count();
        $tagIds = DB::table('tag')
            ->select('id')
            ->where('user_id', Auth::id())
            ->pluck('id')
            ->toArray();
        $tagTeam = TagTeam::whereIn('tag_id', $tagIds)
            ->where('team_id', $this->id)
            ->first();
        $playersState = null;
        if ($tagTeam != null) {
            $playersState = $tagTeam->is_leader ? 'leader' : 'confirmed';
        } else {
            $playersState = 'not-confirmed';
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'tag' => $this->tag,
            'players_reached' => $playersReached,
            'players_to_reach' => $tournament->players_to_reach,
            'tournament_name' => $tournament->name,
            'requests' => $requests,
            'player_state' => $playersState
        ];
    }
}