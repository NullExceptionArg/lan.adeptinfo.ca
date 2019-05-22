<?php

namespace App\Http\Controllers;

use App\Rules\Team\HasPermissionInLan;
use App\Rules\Team\RequestBelongsToUser;
use App\Rules\Team\TagBelongsInTeam;
use App\Rules\Team\TagBelongsToUser;
use App\Rules\Team\TagNotBelongsLeader;
use App\Rules\Team\UniqueTeamNamePerTournament;
use App\Rules\Team\UniqueTeamTagPerTournament;
use App\Rules\Team\UniqueUserPerRequest;
use App\Rules\Team\UniqueUserPerTournament as UniqueUserPerTournamentTeam;
use App\Rules\Team\UserBelongsInTeam;
use App\Rules\Team\UserIsTeamLeaderRequest;
use App\Rules\Team\UserIsTeamLeaderTeam;
use App\Rules\Team\UserIsTournamentAdmin;
use App\Rules\Tournament\UniqueUserPerTournament;
use App\Services\Implementation\TeamServiceImpl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Validation et application de la logique applicative sur les équipes.
 *
 * Class TeamController
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
     *
     * @param TeamServiceImpl $teamServiceImpl
     */
    public function __construct(TeamServiceImpl $teamServiceImpl)
    {
        $this->teamServiceImpl = $teamServiceImpl;
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#accepter-une-requete
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptRequest(Request $request)
    {
        $validator = Validator::make([
            'request_id' => $request->input('request_id'),
        ], [
            'request_id' => [
                'integer',
                'exists:request,id',
                new UserIsTeamLeaderRequest(Auth::id()),
            ],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->acceptRequest(
            $request->input('request_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#changer-de-chef
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeLeader(Request $request)
    {
        $validator = Validator::make([
            'tag_id'  => $request->input('tag_id'),
            'team_id' => $request->input('team_id'),
        ], [
            'tag_id' => [
                'integer',
                'exists:tag,id',
                new TagBelongsInTeam($request->input('team_id')),
                new TagNotBelongsLeader($request->input('team_id')),
            ],
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTeamLeaderTeam(Auth::id())],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->changeLeader(
            $request->input('tag_id'),
            $request->input('team_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#creer-une-demande-pour-joindre-une-equipe
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createRequest(Request $request)
    {
        $validator = Validator::make([
            'team_id' => $request->input('team_id'),
            'tag_id'  => $request->input('tag_id'),
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
                new TagBelongsToUser(Auth::id()),
            ],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->createRequest(
            $request->input('team_id'),
            $request->input('tag_id')
        ), 201);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#creer-une-equipe
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator = Validator::make([
            'tournament_id' => $request->input('tournament_id'),
            'user_tag_id'   => $request->input('user_tag_id'),
            'name'          => $request->input('name'),
            'tag'           => $request->input('tag'),
        ], [
            'tournament_id' => [
                'required',
                'exists:tournament,id,deleted_at,NULL',
                new UniqueUserPerTournament(Auth::id()),
            ],
            'user_tag_id' => [
                'required',
                'exists:tag,id',
                new TagBelongsToUser(Auth::id()),
            ],
            'name' => ['required', 'string', 'max:255', new UniqueTeamNamePerTournament($request->input('tournament_id'))],
            'tag'  => ['string', 'max:5', new UniqueTeamTagPerTournament($request->input('tournament_id'))],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->create(
            $request->input('tournament_id'),
            $request->input('name'),
            $request->input('tag'),
            $request->input('user_tag_id')
        ), 201);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#supprimer-une-equipe-administrateur
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAdmin(Request $request)
    {
        $validator = Validator::make([
            'team_id'    => $request->input('team_id'),
            'permission' => 'delete-team',
        ], [
            'team_id'    => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTournamentAdmin(Auth::id())],
            'permission' => new HasPermissionInLan($request->input('team_id'), Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->delete(
            $request->input('team_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#supprimer-une-equipe-chef
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteLeader(Request $request)
    {
        $validator = Validator::make([
            'team_id' => $request->input('team_id'),
        ], [
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTeamLeaderTeam(Auth::id())],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->delete(
            $request->input('team_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#supprimer-une-requete-chef
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteRequestLeader(Request $request)
    {
        $validator = Validator::make([
            'request_id' => $request->input('request_id'),
        ], [
            'request_id' => [
                'integer',
                'exists:request,id',
                new UserIsTeamLeaderRequest(Auth::id()),
            ],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->deleteRequestLeader(
            $request->input('request_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#annuler-une-requete
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteRequestPlayer(Request $request)
    {
        $validator = Validator::make([
            'request_id' => $request->input('request_id'),
        ], [
            'request_id' => [
                'integer',
                'exists:request,id',
                new RequestBelongsToUser(Auth::id()),
            ],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->deleteRequestPlayer(
            $request->input('request_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#lister-les-requetes-de-l-39-utilisateur
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRequests(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->getRequests(
            Auth::id(),
            $request->input('lan_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#obtenir-les-details-d-39-une-equipe-de-l-39-utilisateur
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersTeamDetails(Request $request)
    {
        $validator = Validator::make([
            'team_id' => $request->input('team_id'),
        ], [
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserBelongsInTeam(Auth::id())],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->getUsersTeamDetails(
            Auth::id(),
            $request->input('team_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#obtenir-les-equipes-de-l-39-utilisateur
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserTeams(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->getUserTeams(
            Auth::id(),
            $request->input('lan_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#supprimer-un-joueur-de-son-equipe
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function kick(Request $request)
    {
        $validator = Validator::make([
            'team_id' => $request->input('team_id'),
            'tag_id'  => $request->input('tag_id'),
        ], [
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserIsTeamLeaderTeam(Auth::id())],
            'tag_id'  => [
                'integer',
                'exists:tag,id',
                new TagBelongsInTeam($request->input('team_id')),
                new TagNotBelongsLeader($request->input('team_id')),
            ],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->kick(
            $request->input('team_id'),
            $request->input('tag_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#quitter-une-equipe
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function leave(Request $request)
    {
        $validator = Validator::make([
            'team_id' => $request->input('team_id'),
        ], [
            'team_id' => ['integer', 'exists:team,id,deleted_at,NULL', new UserBelongsInTeam(Auth::id())],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->teamServiceImpl->leave(
            Auth::id(),
            $request->input('team_id')
        ), 200);
    }
}
