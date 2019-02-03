<?php

namespace App\Services\Implementation;

use App\Http\Resources\{Team\GetRequestsResource, Team\GetUsersTeamDetailsResource, Team\GetUserTeamsResource};
use App\Model\{Request as TeamRequest, Tag, Team};
use App\Repositories\Implementation\{LanRepositoryImpl, TeamRepositoryImpl, TournamentRepositoryImpl};
use App\Services\TeamService;
use Illuminate\{Http\Resources\Json\AnonymousResourceCollection};

class TeamServiceImpl implements TeamService
{
    protected $teamRepository;
    protected $tournamentRepository;
    protected $lanRepository;

    /**
     * TeamServiceImpl constructor.
     * @param TeamRepositoryImpl $teamRepositoryImpl
     * @param TournamentRepositoryImpl $tournamentRepositoryImpl
     * @param LanRepositoryImpl $lanRepositoryImpl
     */
    public function __construct(
        TeamRepositoryImpl $teamRepositoryImpl,
        TournamentRepositoryImpl $tournamentRepositoryImpl,
        LanRepositoryImpl $lanRepositoryImpl
    )
    {
        $this->teamRepository = $teamRepositoryImpl;
        $this->tournamentRepository = $tournamentRepositoryImpl;
        $this->lanRepository = $lanRepositoryImpl;
    }

    public function acceptRequest(int $requestId): Tag
    {
        $request = $this->teamRepository->findRequestById($requestId);
        $tag = $this->teamRepository->findTagById($request->tag_id);
        $team = $this->teamRepository->findById($request->team_id);

        $this->teamRepository->linkTagTeam($tag->id, $team->id, false);
        $this->teamRepository->deleteRequest($request->id);

        return $tag;
    }

    public function changeLeader(int $tagId, int $teamId): Tag
    {
        $tag = $this->teamRepository->findTagById($tagId);

        $this->teamRepository->switchLeader($tag->id, $teamId);

        return $tag;
    }

    public function createRequest(int $teamId, int $tagId): TeamRequest
    {
        $requestId = $this->teamRepository->createRequest($teamId, $tagId);

        return $this->teamRepository->findRequestById($requestId);
    }

    public function create(int $tournamentId, string $name, string $tag, int $userTagId): Team
    {
        $teamId = $this->teamRepository->create(
            $tournamentId,
            $name,
            $tag
        );

        $this->teamRepository->linkTagTeam($userTagId, $teamId, true);
        return $this->teamRepository->findById($teamId);
    }

    public function delete(int $teamId): Team
    {
        $team = $this->teamRepository->findById($teamId);

        $this->teamRepository->delete($teamId);
        return $team;
    }

    public function deleteRequestLeader(int $requestId): Tag
    {
        $request = $this->teamRepository->findRequestById($requestId);
        $tag = $this->teamRepository->findTagById($request->tag_id);

        $this->teamRepository->deleteRequest($requestId);
        return $tag;
    }

    public function deleteRequestPlayer(int $requestId): Team
    {
        $request = $this->teamRepository->findRequestById($requestId);
        $team = $this->teamRepository->findById($request->team_id);

        $this->teamRepository->deleteRequest($requestId);

        return $team;
    }

    public function getRequests(int $userId, int $lanId): AnonymousResourceCollection
    {
        return GetRequestsResource::collection($this->teamRepository->getRequestsForUser($userId, $lanId));
    }

    public function getUsersTeamDetails(int $userId, int $teamId): GetUsersTeamDetailsResource
    {
        $team = $this->teamRepository->findById($teamId);
        $isLeader = $this->teamRepository->userIsLeader($teamId, $userId);
        $tags = $this->teamRepository->getUsersTeamTags($teamId);
        $requests = null;

        if ($isLeader) {
            $requests = $this->teamRepository->getRequests($teamId);
        }

        return new GetUsersTeamDetailsResource($team, $tags, $requests);
    }

    public function getUserTeams(int $userId, int $lanId): AnonymousResourceCollection
    {
        $teams = $this->teamRepository->getUserTeams($userId, $lanId);

        return GetUserTeamsResource::collection($teams);
    }

    public function kick(int $teamId, int $tagId): Tag
    {
        $this->teamRepository->deleteTagTeam($tagId, $teamId);

        $tag = $this->teamRepository->findTagById($tagId);
        return $tag;
    }

    public function leave(int $userId, int $teamId): Team
    {
        $team = $this->teamRepository->findById($teamId);

        if ($this->teamRepository->userIsLeader($teamId, $userId)) {
            $tag = $this->teamRepository->getTagWithMostSeniorityNotLeader($teamId);

            if (is_null($tag)) {
                $this->teamRepository->delete($teamId);
            } else {
                $this->teamRepository->switchLeader($tag->id, $teamId);
            }
        }
        $this->teamRepository->removeUserFromTeam($userId, $teamId);

        return $team;
    }
}
