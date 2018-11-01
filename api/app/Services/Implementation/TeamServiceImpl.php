<?php

namespace App\Services\Implementation;

use App\Http\Resources\Team\GetRequestsResource;
use App\Http\Resources\Team\GetUsersTeamDetailsResource;
use App\Http\Resources\Team\GetUserTeamsResource;
use App\Model\Request as TeamRequest;
use App\Model\Tag;
use App\Model\Team;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\TagRepositoryImpl;
use App\Repositories\Implementation\TeamRepositoryImpl;
use App\Repositories\Implementation\TournamentRepositoryImpl;
use App\Rules\HasPermission;
use App\Rules\Team\RequestBelongsInTeam;
use App\Rules\Team\TagBelongsInTeam;
use App\Rules\Team\TagBelongsToUser;
use App\Rules\Team\TagNotBelongsLeader;
use App\Rules\Team\UniqueTeamNamePerTournament;
use App\Rules\Team\UniqueTeamTagPerTournament;
use App\Rules\Team\UniqueUserPerRequest;
use App\Rules\Team\UniqueUserPerTournament;
use App\Rules\Team\UserBelongsInTeam;
use App\Rules\Team\UserIsTeamLeader;
use App\Services\TeamService;
use App\Team\Rules\UserIsTournamentAdmin;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TeamServiceImpl implements TeamService
{
    protected $teamRepository;
    protected $tournamentRepository;
    protected $tagRepository;
    protected $lanRepository;

    /**
     * LanServiceImpl constructor.
     * @param TeamRepositoryImpl $teamRepositoryImpl
     * @param TournamentRepositoryImpl $tournamentRepositoryImpl
     * @param TagRepositoryImpl $tagRepositoryImpl
     * @param LanRepositoryImpl $lanRepositoryImpl
     */
    public function __construct(
        TeamRepositoryImpl $teamRepositoryImpl,
        TournamentRepositoryImpl $tournamentRepositoryImpl,
        TagRepositoryImpl $tagRepositoryImpl,
        LanRepositoryImpl $lanRepositoryImpl
    )
    {
        $this->teamRepository = $teamRepositoryImpl;
        $this->tournamentRepository = $tournamentRepositoryImpl;
        $this->tagRepository = $tagRepositoryImpl;
        $this->lanRepository = $lanRepositoryImpl;
    }

    public function create(Request $input): Team
    {
        $teamValidator = Validator::make([
            'tournament_id' => $input->input('tournament_id'),
            'user_tag_id' => $input->input('user_tag_id'),
            'name' => $input->input('name'),
            'tag' => $input->input('tag')
        ], [
            'tournament_id' => 'required|exists:tournament,id,deleted_at,NULL',
            'user_tag_id' => ['required', 'exists:tag,id', new UniqueUserPerTournament($input->input('tournament_id'), null)],
            'name' => ['required', 'string', 'max:255', new UniqueTeamNamePerTournament($input->input('tournament_id'))],
            'tag' => ['string', 'max:5', new UniqueTeamTagPerTournament($input->input('tournament_id'))]
        ]);

        if ($teamValidator->fails()) {
            throw new BadRequestHttpException($teamValidator->errors());
        }

        $tournament = $this->tournamentRepository->findById($input->input('tournament_id'));
        $team = $this->teamRepository->create(
            $tournament,
            $input->input('name'),
            $input->input('tag')
        );

        $tag = $this->tagRepository->findById($input->input('user_tag_id'));
        $this->teamRepository->linkTagTeam($tag, $team, true);

        return $team;
    }

    public function createRequest(Request $input): TeamRequest
    {
        $teamValidator = Validator::make([
            'team_id' => $input->input('team_id'),
            'tag_id' => $input->input('tag_id'),
        ], [
            'team_id' => ['required', 'exists:team,id,deleted_at,NULL', new UniqueUserPerRequest($input->input('tag_id'))],
            'tag_id' => [
                'required',
                'exists:tag,id',
                new UniqueUserPerTournament(null, $input->input('team_id')),
                new TagBelongsToUser
            ],
        ]);

        if ($teamValidator->fails()) {
            throw new BadRequestHttpException($teamValidator->errors());
        }

        return $this->teamRepository->createRequest($input->input('team_id'), $input->input('tag_id'));
    }

    public function getUserTeams(Request $input): AnonymousResourceCollection
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $teamValidator = Validator::make([
            'lan_id' => $input->input('lan_id')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
        ]);

        if ($teamValidator->fails()) {
            throw new BadRequestHttpException($teamValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }

        $user = Auth::user();

        $teams = $this->teamRepository->getUserTeams($user, $lan);

        return GetUserTeamsResource::collection($teams);
    }

    public function getUsersTeamDetails(Request $input)
    {
        $teamValidator = Validator::make([
            'team_id' => $input->input('team_id')
        ], [
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserBelongsInTeam],
        ]);

        if ($teamValidator->fails()) {
            throw new BadRequestHttpException($teamValidator->errors());
        }

        $team = $this->teamRepository->findById($input->input('team_id'));
        $isLeader = $this->teamRepository->userIsLeader($team, Auth::user());
        $tags = $this->teamRepository->getUsersTeamTags($team);
        $requests = null;

        if ($isLeader) {
            $requests = $this->teamRepository->getRequests($team);
        }

        return new GetUsersTeamDetailsResource($team, $tags, $requests);
    }

    public function changeLeader(Request $input): Tag
    {
        $teamValidator = Validator::make([
            'tag_id' => $input->input('tag_id'),
            'team_id' => $input->input('team_id')
        ], [
            'tag_id' => [
                'integer',
                'exists:tag,id',
                new TagBelongsInTeam($input->input('team_id')),
                new TagNotBelongsLeader($input->input('team_id'))
            ],
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTeamLeader],
        ]);

        if ($teamValidator->fails()) {
            throw new BadRequestHttpException($teamValidator->errors());
        }

        $tag = $this->tagRepository->findById($input->input('tag_id'));
        $team = $this->teamRepository->findById($input->input('team_id'));

        $this->teamRepository->switchLeader($tag, $team);

        return $tag;
    }

    public function acceptRequest(Request $input): Tag
    {
        $requestValidator = Validator::make([
            'request_id' => $input->input('request_id'),
            'team_id' => $input->input('team_id')
        ], [
            'request_id' => [
                'integer',
                'exists:request,id',
                new RequestBelongsInTeam($input->input('team_id')),
            ],
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTeamLeader],
        ]);

        if ($requestValidator->fails()) {
            throw new BadRequestHttpException($requestValidator->errors());
        }

        $request = $this->teamRepository->findRequestById($input->input('request_id'));
        $tag = $this->tagRepository->findById($request->tag_id);
        $team = $this->teamRepository->findById($request->team_id);

        $this->teamRepository->linkTagTeam($tag, $team, false);
        $this->teamRepository->deleteRequest($request);

        return $tag;
    }

    public function getRequests(Request $input): AnonymousResourceCollection
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $requestValidator = Validator::make([
            'lan_id' => $input->input('lan_id')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
        ]);

        if ($requestValidator->fails()) {
            throw new BadRequestHttpException($requestValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }

        return GetRequestsResource::collection($this->teamRepository->getRequestsForUser(Auth::user(), $lan));
    }

    public function leave(Request $input): Team
    {
        $teamValidator = Validator::make([
            'team_id' => $input->input('team_id')
        ], [
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserBelongsInTeam],
        ]);

        if ($teamValidator->fails()) {
            throw new BadRequestHttpException($teamValidator->errors());
        }

        $team = $this->teamRepository->findById($input->input('team_id'));

        if ($this->teamRepository->userIsLeader($team, Auth::user())) {
            $tag = $this->teamRepository->getTagWithMostSeniorityNotLeader($team);
            if ($tag == null) {
                $this->teamRepository->delete($team);
            } else {
                $this->teamRepository->switchLeader($tag, $team);
            }
        }
        $this->teamRepository->removeUserFromTeam(Auth::user(), $team);

        return $team;
    }

    public function deleteRequestPlayer(Request $input): Team
    {
        $requestValidator = Validator::make([
            'request_id' => $input->input('request_id'),
            'team_id' => $input->input('team_id')
        ], [
            'request_id' => [
                'integer',
                'exists:request,id',
                new RequestBelongsInTeam($input->input('team_id')),
            ],
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserBelongsInTeam],
        ]);

        if ($requestValidator->fails()) {
            throw new BadRequestHttpException($requestValidator->errors());
        }

        $request = $this->teamRepository->findRequestById($input->input('request_id'));
        $team = $this->teamRepository->findById($request->team_id);
        $this->teamRepository->deleteRequest($request);

        return $team;
    }

    public function deleteAdmin(Request $input): Team
    {
        $lanId = $this->teamRepository->getTeamsLanId(intval($input->input('team_id')));
        $teamValidator = Validator::make([
            'team_id' => $input->input('team_id'),
            'permission' => 'delete-team'
        ], [
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTournamentAdmin],
            'permission' => new HasPermission($lanId, Auth::id())
        ]);

        if ($teamValidator->fails()) {
            throw new BadRequestHttpException($teamValidator->errors());
        }

        $team = $this->teamRepository->findById($input->input('team_id'));
        $this->teamRepository->delete($team);

        return $team;
    }

    public function deleteLeader(Request $input): Team
    {
        $teamValidator = Validator::make([
            'team_id' => $input->input('team_id')
        ], [
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTeamLeader],
        ]);

        if ($teamValidator->fails()) {
            throw new BadRequestHttpException($teamValidator->errors());
        }

        $team = $this->teamRepository->findById($input->input('team_id'));
        $this->teamRepository->delete($team);

        return $team;
    }

    public function kick(Request $input): Tag
    {
        $teamValidator = Validator::make([
            'team_id' => $input->input('team_id'),
            'tag_id' => $input->input('tag_id')
        ], [
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTeamLeader()],
            'tag_id' => [
                'integer',
                'exists:tag,id',
                new TagBelongsInTeam($input->input('team_id')),
                new TagNotBelongsLeader($input->input('team_id'))
            ],
        ]);

        if ($teamValidator->fails()) {
            throw new BadRequestHttpException($teamValidator->errors());
        }

        $team = $this->teamRepository->findById($input->input('team_id'));
        $tag = $this->teamRepository->findTagById($input->input('team_id'));
        $this->teamRepository->deleteTagTeam($tag, $team);

        return $tag;
    }

    public function deleteRequestLeader(Request $input): Tag
    {
        $requestValidator = Validator::make([
            'request_id' => $input->input('request_id'),
            'team_id' => $input->input('team_id')
        ], [
            'request_id' => [
                'integer',
                'exists:request,id',
                new RequestBelongsInTeam($input->input('team_id')),
            ],
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTeamLeader],
        ]);

        if ($requestValidator->fails()) {
            throw new BadRequestHttpException($requestValidator->errors());
        }

        $request = $this->teamRepository->findRequestById($input->input('request_id'));
        $tag = $this->teamRepository->findTagById($request->tag_id);

        $this->teamRepository->deleteRequest($request);

        return $tag;
    }
}