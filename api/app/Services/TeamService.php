<?php

namespace App\Services;

use App\Http\Resources\Team\GetUsersTeamDetailsResource;
use App\Model\{Request as TeamRequest, Tag, Team};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Méthodes pour exécuter la logique d'affaire des équipes.
 *
 * Interface TeamService
 * @package App\Services
 */
interface TeamService
{
    public function acceptRequest(int $requestId): Tag;

    public function changeLeader(int $tagId, int $teamId): Tag;

    public function createRequest(int $teamId, int $tagId): TeamRequest;

    public function create(int $tournamentId, string $name, string $tag, int $userTagId): Team;

    public function deleteAdmin(int $teamId): Team;

    public function deleteLeader(int $teamId): Team;

    public function deleteRequestLeader(int $requestId): Tag;

    public function deleteRequestPlayer(int $requestId): Team;

    public function getRequests(int $lanId): AnonymousResourceCollection;

    public function getUsersTeamDetails(int $teamId): GetUsersTeamDetailsResource;

    public function getUserTeams(int $lanId): AnonymousResourceCollection;

    public function kick(int $teamId, int $tagId): Tag;

    public function leave(int $teamId): Team;
}
