<?php

namespace App\Repositories;

use App\Model\Request;
use App\Model\Tag;
use App\Model\Team;
use Illuminate\Support\Collection;

interface TeamRepository
{
    public function createRequest(int $teamId, int $userTagId): int;

    public function create(
        int $tournamentId,
        string $name,
        string $tag
    ): int;

    public function deleteRequest(int $requestId): void;

    public function deleteTagTeam(int $tagId, int $teamId): void;

    public function delete(int $teamId): void;

    public function findById(int $id): ?Team;

    public function findRequestById(int $id): ?Request;

    public function findTagById(int $id): ?Tag;

    public function getLeadersRequestTotalCount(int $userId, int $lanId): int;

    public function getRequestsForUser(int $userId, int $lanId): Collection;

    public function getRequests(int $teamId): Collection;

    public function getTagWithMostSeniorityNotLeader(int $teamId): ?Tag;

    public function getTeamsLanId(int $teamId): ?int;

    public function getUsersTeamTags(int $teamId): Collection;

    public function getUserTeams(int $userId, int $lanId): Collection;

    public function linkTagTeam(int $tagId, int $teamId, bool $isLeader): void;

    public function removeUserFromTeam(int $userId, int $teamId): void;

    public function switchLeader(int $tagId, int $teamId): void;

    public function userIsLeader(int $teamId, int $userId): bool;
}
