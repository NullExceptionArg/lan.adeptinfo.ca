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
    Team\UniqueUserPerTournament,
    Team\UserBelongsInTeam,
    Team\UserIsTeamLeaderRequest,
    Team\UserIsTeamLeaderTeam,
    Team\UserIsTournamentAdmin};
use App\Services\Implementation\TeamServiceImpl;
use Illuminate\{Http\Request, Support\Facades\Auth, Support\Facades\Validator};

class TeamController extends Controller
{
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
                new UserIsTeamLeaderRequest
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
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTeamLeaderTeam],
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
            'team_id' => ['required', 'exists:team,id,deleted_at,NULL', new UniqueUserPerRequest($request->input('tag_id'))],
            'tag_id' => [
                'required',
                'exists:tag,id',
                new UniqueUserPerTournament(null, $request->input('team_id')),
                new TagBelongsToUser
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
            'tournament_id' => 'required|exists:tournament,id,deleted_at,NULL',
            'user_tag_id' => [
                'required',
                'exists:tag,id',
                new UniqueUserPerTournament($request->input('tournament_id'), null),
                new TagBelongsToUser
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
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTournamentAdmin],
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
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTeamLeaderTeam],
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
                new UserIsTeamLeaderRequest,
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
                new RequestBelongsToUser
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
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserBelongsInTeam],
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
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTeamLeaderTeam],
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
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserBelongsInTeam],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->leave(
            $request->input('team_id')
        ), 200);
    }
}
