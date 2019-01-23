<?php

namespace App\Http\Controllers;

use App\Rules\{Team\HasPermissionInLan,
    Team\RequestBelongsToUser,
    Team\TagBelongsInTeam,
    Team\TagBelongsToUser,
    Team\TagNotBelongsLeader,
    Team\UniqueTeamNamePerTournament,
    Team\UniqueTeamTagPerTournament,
    Team\UniqueUserPerRequest,
    Team\UniqueUserPerTournament as UniqueUserPerTournamentTeam,
    Team\UserBelongsInTeam,
    Team\UserIsTeamLeaderRequest,
    Team\UserIsTeamLeaderTeam,
    Team\UserIsTournamentAdmin,
    Tournament\UniqueUserPerTournament};
use App\Services\Implementation\TeamServiceImpl;
use Illuminate\{Http\Request, Support\Facades\Auth, Support\Facades\Validator};

/**
 * Validation et application de la logique applicative sur les équipes.
 *
 * Class TeamController
 * @package App\Http\Controllers
 */
class TeamController extends Controller
{
    /**
     * Service d'équipe.
     *
     * @var TeamServiceImpl
     */
    protected $teamServiceImpl;

    /**
     * TeamController constructor.
     * @param TeamServiceImpl $teamServiceImpl
     */
    public function __construct(TeamServiceImpl $teamServiceImpl)
    {
        $this->teamServiceImpl = $teamServiceImpl;
    }

    public function acceptRequest(Request $request)
    {
        $validator = Validator::make([
            'request_id' => $request->input('request_id')
        ], [
            'request_id' => [
                'integer',
                'exists:request,id',
                new UserIsTeamLeaderRequest(Auth::id())
            ]
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->acceptRequest(
            $request->input('request_id')
        ), 200);
    }

    public function changeLeader(Request $request)
    {
        $validator = Validator::make([
            'tag_id' => $request->input('tag_id'),
            'team_id' => $request->input('team_id')
        ], [
            'tag_id' => [
                'integer',
                'exists:tag,id',
                new TagBelongsInTeam($request->input('team_id')),
                new TagNotBelongsLeader($request->input('team_id'))
            ],
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTeamLeaderTeam(Auth::id())],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->changeLeader(
            $request->input('tag_id'),
            $request->input('team_id')
        ), 200);
    }

    public function createRequest(Request $request)
    {
        $validator = Validator::make([
            'team_id' => $request->input('team_id'),
            'tag_id' => $request->input('tag_id'),
        ], [
            'team_id' => [
                'required',
                'exists:team,id,deleted_at,NULL',
                new UniqueUserPerRequest($request->input('tag_id'), Auth::id()),
                new UniqueUserPerTournamentTeam(Auth::id()),
            ],
            'tag_id' => [
                'required',
                'exists:tag,id',
                new TagBelongsToUser(Auth::id())
            ],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->createRequest(
            $request->input('team_id'),
            $request->input('tag_id')
        ), 201);
    }

    public function create(Request $request)
    {
        $validator = Validator::make([
            'tournament_id' => $request->input('tournament_id'),
            'user_tag_id' => $request->input('user_tag_id'),
            'name' => $request->input('name'),
            'tag' => $request->input('tag')
        ], [
            'tournament_id' => [
                'required',
                'exists:tournament,id,deleted_at,NULL',
                new UniqueUserPerTournament(Auth::id())
            ],
            'user_tag_id' => [
                'required',
                'exists:tag,id',
                new TagBelongsToUser(Auth::id())
            ],
            'name' => ['required', 'string', 'max:255', new UniqueTeamNamePerTournament($request->input('tournament_id'))],
            'tag' => ['string', 'max:5', new UniqueTeamTagPerTournament($request->input('tournament_id'))]
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->create(
            $request->input('tournament_id'),
            $request->input('name'),
            $request->input('tag'),
            $request->input('user_tag_id')
        ), 201);
    }

    public function deleteAdmin(Request $request)
    {
        $validator = Validator::make([
            'team_id' => $request->input('team_id'),
            'permission' => 'delete-team'
        ], [
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTournamentAdmin(Auth::id())],
            'permission' => new HasPermissionInLan($request->input('team_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->deleteAdmin(
            $request->input('team_id')
        ), 200);
    }

    public function deleteLeader(Request $request)
    {
        $validator = Validator::make([
            'team_id' => $request->input('team_id')
        ], [
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTeamLeaderTeam(Auth::id())],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->deleteLeader(
            $request->input('team_id')
        ), 200);
    }

    public function deleteRequestLeader(Request $request)
    {
        $validator = Validator::make([
            'request_id' => $request->input('request_id')
        ], [
            'request_id' => [
                'integer',
                'exists:request,id',
                new UserIsTeamLeaderRequest(Auth::id()),
            ]
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->deleteRequestLeader(
            $request->input('request_id')
        ), 200);
    }

    public function deleteRequestPlayer(Request $request)
    {
        $validator = Validator::make([
            'request_id' => $request->input('request_id')
        ], [
            'request_id' => [
                'integer',
                'exists:request,id',
                new RequestBelongsToUser(Auth::id())
            ]
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->deleteRequestPlayer(
            $request->input('request_id')
        ), 200);
    }

    public function getRequests(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->getRequests(
            $request->input('lan_id')
        ), 200);
    }

    public function getUsersTeamDetails(Request $request)
    {
        $validator = Validator::make([
            'team_id' => $request->input('team_id')
        ], [
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserBelongsInTeam(Auth::id())],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->getUsersTeamDetails(
            $request->input('team_id')
        ), 200);
    }

    public function getUserTeams(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->getUserTeams(
            $request->input('lan_id')
        ), 200);
    }

    public function kick(Request $request)
    {
        $validator = Validator::make([
            'team_id' => $request->input('team_id'),
            'tag_id' => $request->input('tag_id')
        ], [
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTeamLeaderTeam(Auth::id())],
            'tag_id' => [
                'integer',
                'exists:tag,id',
                new TagBelongsInTeam($request->input('team_id')),
                new TagNotBelongsLeader($request->input('team_id'))
            ],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->kick(
            $request->input('team_id'),
            $request->input('tag_id')
        ), 200);
    }

    public function leave(Request $request)
    {
        $validator = Validator::make([
            'team_id' => $request->input('team_id')
        ], [
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserBelongsInTeam(Auth::id())],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->leave(
            $request->input('team_id')
        ), 200);
    }
}
