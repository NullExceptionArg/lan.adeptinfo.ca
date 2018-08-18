<?php

namespace App\Repositories;

use App\Model\Lan;
use App\Model\Request;
use App\Model\Tag;
use App\Model\Team;
use App\Model\Tournament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

interface TeamRepository
{
    public function create(
        Tournament $tournament,
        string $name,
        string $tag
    ): Team;

    public function linkTagTeam(Tag $tag, Team $team, bool $isLeader): void;

    public function createRequest(int $teamId, $userTagId): Request;

    public function getUserTeams(Authenticatable $user, Lan $lan): Collection;

    public function findById(int $id): ?Team;

    public function getUsersTeamTags(Team $team): Collection;

    public function userIsLeader(Team $team, Authenticatable $user): bool;

    public function getRequests(Team $team): Collection;

    public function switchLeader(Tag $tag, Team $team): void;

    public function findRequestById(int $id): ?Request;

    public function deleteRequest(Request $request): void;

    public function getRequestsForUser(Authenticatable $user, Lan $lan): Collection;
}