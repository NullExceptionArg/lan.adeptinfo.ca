<?php

namespace App\Services\Implementation;

use App\Http\Resources\Team\GetRequestsResource;
use App\Http\Resources\Team\GetUsersTeamDetailsResource;
use App\Http\Resources\Team\GetUserTeamsResource;
use App\Model\Request as TeamRequest;
use App\Model\Tag;
use App\Model\Team;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\TeamRepositoryImpl;
use App\Repositories\Implementation\TournamentRepositoryImpl;
use App\Services\TeamService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class TeamServiceImpl implements TeamService
{
    protected $teamRepository;
    protected $tournamentRepository;
    protected $lanRepository;

    /**
     * LanServiceImpl constructor.
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
        $team = $this->teamRepository->findById($teamId);

        $this->teamRepository->switchLeader($tag->id, $team->id);

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

    public function deleteAdmin(int $teamId): Team
    {
        $team = $this->teamRepository->findById($teamId);

        $this->teamRepository->delete($teamId);
        return $team;
    }

    public function deleteLeader(int $teamId): Team
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

    public function getRequests(int $lanId): AnonymousResourceCollection
    {
        return GetRequestsResource::collection($this->teamRepository->getRequestsForUser(Auth::id(), $lanId));
    }

    public function getUsersTeamDetails(int $teamId): GetUsersTeamDetailsResource
    {
        $team = $this->teamRepository->findById($teamId);
        $isLeader = $this->teamRepository->userIsLeader($teamId, Auth::id());
        $tags = $this->teamRepository->getUsersTeamTags($teamId);
        $requests = null;

        if ($isLeader) {
            $requests = $this->teamRepository->getRequests($teamId);
        }

        return new GetUsersTeamDetailsResource($team, $tags, $requests);
    }

    public function getUserTeams(int $lanId): AnonymousResourceCollection
    {
        $teams = $this->teamRepository->getUserTeams(Auth::id(), $lanId);

        return GetUserTeamsResource::collection($teams);
    }

    public function kick(int $teamId, int $tagId): Tag
    {
        $this->teamRepository->deleteTagTeam($tagId, $teamId);

        $tag = $this->teamRepository->findTagById($tagId);
        return $tag;
    }

    public function leave(int $teamId): Team
    {
        $team = $this->teamRepository->findById($teamId);

        if ($this->teamRepository->userIsLeader($teamId, Auth::id())) {
            $tag = $this->teamRepository->getTagWithMostSeniorityNotLeader($teamId);

            if (is_null($tag)) {
                $this->teamRepository->delete($teamId);
            } else {
                $this->teamRepository->switchLeader($tag->id, $teamId);
            }
        }
        $this->teamRepository->removeUserFromTeam(Auth::id(), $teamId);

        return $team;
    }
}
