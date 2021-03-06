<?php

namespace App\Repositories\Implementation;

use App\Model\Request;
use App\Model\Tag;
use App\Model\TagTeam;
use App\Model\Team;
use App\Repositories\TeamRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TeamRepositoryImpl implements TeamRepository
{
    public function createRequest(int $teamId, int $userTagId): int
    {
        return DB::table('request')
            ->insertGetId([
                'team_id' => $teamId,
                'tag_id'  => $userTagId,
            ]);
    }

    public function create(
        int $tournamentId,
        string $name,
        string $tag
    ): int {
        return DB::table('team')
            ->insertGetId([
                'tournament_id' => $tournamentId,
                'name'          => $name,
                'tag'           => $tag,
            ]);
    }

    public function deleteRequest(int $requestId): void
    {
        DB::table('request')
            ->where('id', '=', $requestId)
            ->delete();
    }

    public function deleteTagTeam(int $tagId, int $teamId): void
    {
        TagTeam::where('tag_id', $tagId)
            ->where('team_id', $teamId)
            ->delete();
    }

    public function delete(int $teamId): void
    {
        $team = Team::find($teamId);
        $team->delete();
    }

    public function findById(int $id): ?Team
    {
        return Team::find($id);
    }

    public function findRequestById(int $id): ?Request
    {
        return Request::find($id);
    }

    public function findTagById(int $id): ?Tag
    {
        return Tag::find($id);
    }

    public function getLeadersRequestTotalCount(int $userId, int $lanId): int
    {
        // Obtenir les équipes dirigées par l'utilisateur
        $teamIds = DB::table('tag')
            ->join('tag_team', 'tag.id', '=', 'tag_team.tag_id')
            ->join('team', 'tag_team.team_id', '=', 'team.id')
            ->join('tournament', 'team.tournament_id', '=', 'tournament.id')
            ->select('team.id')
            ->where('user_id', $userId)
            ->where('tag_team.is_leader', true)
            ->where('tournament.lan_id', $lanId)
            ->pluck('team.id')
            ->toArray();

        // Obtenir les requêtes qui sont faites dans ces équipes, et les compter
        return DB::table('request')
            ->whereIn('team_id', $teamIds)
            ->count();
    }

    public function getRequestsForUser(int $userId, int $lanId): Collection
    {
        return DB::table('tag')
            ->join('request', 'tag.id', '=', 'request.tag_id')
            ->join('team', 'request.team_id', '=', 'team.id')
            ->join('tournament', 'team.tournament_id', '=', 'tournament.id')
            ->where('tag.user_id', $userId)
            ->where('tournament.lan_id', $lanId)
            ->select(
                'request.id',
                'tag.id as tag_id',
                'tag.name as tag_name',
                'team.id as team_id',
                'team.name as team_name',
                'team.tag as team_tag',
                'tournament.id as tournament_id',
                'tournament.name as tournament_name'
            )
            ->get();
    }

    public function getRequests(int $teamId): Collection
    {
        return DB::table('request')
            ->join('tag', 'request.tag_id', '=', 'tag.id')
            ->join('user', 'tag.user_id', '=', 'user.id')
            ->where('request.team_id', $teamId)
            ->select(
                'request.id',
                'tag.id as tag_id',
                'tag.name as tag_name',
                'user.first_name',
                'user.last_name'
            )
            ->get();
    }

    public function getTagWithMostSeniorityNotLeader(int $teamId): ?Tag
    {
        // Obtenir le plus vieux lien entre les tag et l'équipes où le tag n'est pas le chef.
        $tagTeam = TagTeam::where('team_id', $teamId)
            ->where('is_leader', false)
            ->oldest()
            ->first();

        // Vérifier si aucun tag de joueur n'a été trouvé
        if (is_null($tagTeam)) {
            return null;
        }

        // Trouver et retourner le tag de joueur.
        return Tag::find($tagTeam->tag_id);
    }

    public function getTeamsLanId(int $teamId): ?int
    {
        $lanId = DB::table('tournament')
            ->join('team', 'tournament.id', '=', 'team.tournament_id')
            ->where('team.id', $teamId)
            ->select('tournament.lan_id')
            ->first();

        return !is_null($lanId) ? $lanId->lan_id : null;
    }

    public function getUsersTeamTags(int $teamId): Collection
    {
        return DB::table('tag')
            ->join('tag_team', 'tag.id', '=', 'tag_team.tag_id')
            ->join('user', 'tag.user_id', '=', 'user.id')
            ->where('tag_team.team_id', $teamId)
            ->select(
                'tag_team.id',
                'tag.id as tag_id',
                'tag.name as tag_name',
                'user.first_name',
                'user.last_name',
                'tag_team.is_leader'
            )
            ->get();
    }

    public function getUserTeams(int $userId, int $lanId): Collection
    {
        // Obtenir les tags de l'utilisateur
        $tagIds = DB::table('tag')
            ->select('id')
            ->where('user_id', $userId)
            ->pluck('id')
            ->toArray();

        // Obtenir les équipes de l'utilisateur
        $teamsIds = DB::table('tag_team')
            ->select('team_id')
            ->whereIn('tag_id', $tagIds)
            ->pluck('team_id')
            ->toArray();

        // Obtenir les requêtes de l'utilisateur
        $teamsIdsRequest = DB::table('request')
            ->select('team_id')
            ->whereIn('tag_id', $tagIds)
            ->pluck('team_id')
            ->toArray();

        // Obtenir les tournois du LAN
        $tournamentIds = DB::table('tournament')
            ->select('id')
            ->where('lan_id', $lanId)
            ->pluck('id')
            ->toArray();

        // Obtenir les équipes dont l'utilisateur fait parti et celle où l'utilisateur à fait des requêtes, dans les
        // tournois du LAN
        return Team::whereIn('id', $teamsIds)
            ->orWhereIn('id', $teamsIdsRequest)
            ->whereIn('tournament_id', $tournamentIds)
            ->get();
    }

    public function linkTagTeam(int $tagId, int $teamId, bool $isLeader): void
    {
        DB::table('tag_team')
            ->insert([
                'tag_id'    => $tagId,
                'team_id'   => $teamId,
                'is_leader' => $isLeader,
            ]);
    }

    public function removeUserFromTeam(int $userId, int $teamId): void
    {
        // Obtenir les liens entre l'équipe et les tags du joueur
        $tagTeamId = DB::table('tag')
            ->join('tag_team', 'tag.id', '=', 'tag_team.tag_id')
            ->where('tag.user_id', $userId)
            ->where('tag_team.team_id', $teamId)
            ->select('tag_team.id')
            ->pluck('id')
            ->first();

        // Supprimer les liens trouvés
        TagTeam::destroy($tagTeamId);
    }

    public function switchLeader(int $tagId, int $teamId): void
    {
        // Mettre à jour l'équipe pour qu'elle n'ait plus de leader
        TagTeam::where('team_id', $teamId)
            ->update([
                'is_leader' => false,
            ]);

        // Mettre à jour l'équipe pour que le chef soit celui spécifié
        TagTeam::where('team_id', $teamId)
            ->where('tag_id', $tagId)
            ->update([
                'is_leader' => true,
            ]);
    }

    public function userIsLeader(int $teamId, int $userId): bool
    {
        return DB::table('tag_team')
                ->join('tag', 'tag_team.tag_id', '=', 'tag.id')
                ->where('tag_team.team_id', $teamId)
                ->where('tag_team.is_leader', true)
                ->where('tag.user_id', $userId)
                ->count() > 0;
    }
}
