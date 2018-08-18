<?php

namespace App\Services\Implementation;

use App\Http\Resources\Team\GetUsersTeamDetailsResource;
use App\Http\Resources\Team\GetUserTeamsResource;
use App\Model\Tag;
use App\Model\Team;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\TagRepositoryImpl;
use App\Repositories\Implementation\TeamRepositoryImpl;
use App\Repositories\Implementation\TournamentRepositoryImpl;
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
            'tournament_id' => 'required|exists:tournament,id',
            'user_tag_id' => ['required', 'exists:tag,id', new UniqueUserPerTournament($input->input('tournament_id'), null)],
            'name' => ['required', 'string', 'max:255', new UniqueTeamNamePerTournament($input->input('tournament_id'))],
            'tag' => ['string', 'max:5', new UniqueTeamTagPerTournament($input->input('tournament_id'))]
        ]);

        if ($teamValidator->fails()) {
            throw new BadRequestHttpException($teamValidator->errors());
        }

        $tournament = $this->tournamentRepository->findTournamentById($input->input('tournament_id'));
        $team = $this->teamRepository->create(
            $tournament,
            $input->input('name'),
            $input->input('tag')
        );

        $tag = $this->tagRepository->findById($input->input('user_tag_id'));
        $this->teamRepository->linkTagTeam($tag, $team, true);

        return $team;
    }

    public function createRequest(Request $input): \App\Model\Request
    {
        $teamValidator = Validator::make([
            'team_id' => $input->input('team_id'),
            'tag_id' => $input->input('tag_id'),
        ], [
            'team_id' => ['required', 'exists:team,id', new UniqueUserPerRequest($input->input('tag_id'))],
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
            'lan_id' => 'integer|exists:lan,id',
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
            'team_id' => ['integer', 'exists:team,id', new UserBelongsInTeam],
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
            'team_id' => ['integer', 'exists:team,id', new UserIsTeamLeader],
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
        // TODO Ajouter request id quand on liste les requêtes
        // TODO La requête est pour l'équipe
        // TODO L'utilisateur est le chef de l'équipe
        $requestValidator = Validator::make([
            'request_id' => $input->input('request_id'),
            'team_id' => $input->input('team_id')
        ], [
            'request_id' => [
                'integer',
                'exists:request,id',
                new TagBelongsInTeam($input->input('team_id')),
                new TagNotBelongsLeader($input->input('team_id'))
            ],
            'team_id' => ['integer', 'exists:team,id', new UserIsTeamLeader],
        ]);

        if ($requestValidator->fails()) {
            throw new BadRequestHttpException($requestValidator->errors());
        }
    }
}