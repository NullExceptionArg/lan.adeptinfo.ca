<?php

namespace App\Repositories\Implementation;

use App\Model\Lan;
use App\Model\Request;
use App\Model\Tag;
use App\Model\TagTeam;
use App\Model\Team;
use App\Model\Tournament;
use App\Repositories\TeamRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;

class TeamRepositoryImpl implements TeamRepository
{
    public function create(
        Tournament $tournament,
        string $name,
        string $tag
    ): Team
    {
        $team = new Team();
        $team->tournament_id = $tournament->id;
        $team->name = $name;
        $team->tag = $tag;
        $team->save();

        return $team;
    }

    public function linkTagTeam(Tag $tag, Team $team, bool $isLeader): void
    {
        $tagTeam = new TagTeam();
        $tagTeam->tag_id = $tag->id;
        $tagTeam->team_id = $team->id;
        $tagTeam->is_leader = $isLeader;
        $tagTeam->save();
    }

    public function createRequest(int $teamId, $userTagId): Request
    {
        $request = new Request();
        $request->team_id = $teamId;
        $request->tag_id = $userTagId;
        $request->save();

        return $request;
    }

    public function getUserTeam(Authenticatable $user, Lan $lan)
    {
        $tagIds = DB::table('tag')
            ->select('id')
            ->where('user_id', $user->id)
            ->pluck('id')
            ->toArray();

        $teamsIds = DB::table('tag_team')
            ->select('team_id')
            ->whereIn('tag_id', $tagIds)
            ->pluck('team_id')
            ->toArray();

        $teamsIdsRequest = DB::table('request')
            ->select('team_id')
            ->whereIn('tag_id', $tagIds)
            ->pluck('team_id')
            ->toArray();

        $tournamentIds = DB::table('tournament')
            ->select('id')
            ->where('lan_id', $lan->id)
            ->pluck('id')
            ->toArray();

        return Team::whereIn('id', $teamsIds)
            ->orWhereIn('id', $teamsIdsRequest)
            ->whereIn('tournament_id', $tournamentIds)
            ->get();
    }
}