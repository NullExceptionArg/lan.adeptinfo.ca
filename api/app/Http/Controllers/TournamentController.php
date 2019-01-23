<?php

namespace App\Http\Controllers;

use App\Rules\{Role\HasPermissionInLanOrIsTournamentAdmin,
    Tournament\AfterOrEqualLanStartTime,
    Tournament\BeforeOrEqualLanEndTime,
    Tournament\PlayersToReachLock,
    Tournament\UserIsTournamentAdmin,
    User\HasPermissionInLan};
use App\Services\Implementation\TournamentServiceImpl;
use Carbon\Carbon;
use Illuminate\{Http\Request, Support\Facades\Auth, Support\Facades\Validator, Validation\Rule};

/**
 * Validation et application de la logique applicative sur les tournois.
 *
 * Class TournamentController
 * @package App\Http\Controllers
 */
class TournamentController extends Controller
{
    /**
     * Service de tournoi.
     *
     * @var TournamentServiceImpl
     */
    protected $tournamentService;

    /**
     * TournamentController constructor.
     * @param TournamentServiceImpl $tournamentService
     */
    public function __construct(TournamentServiceImpl $tournamentService)
    {
        $this->tournamentService = $tournamentService;
    }

    public function addOrganizer(Request $request, string $tournamentId)
    {
        $validator = Validator::make([
            'tournament_id' => $tournamentId,
            'email' => $request->input('email'),
            'permission' => 'add-organizer'
        ], [
            'tournament_id' => ['integer', 'exists:tournament,id,deleted_at,NULL'],
            'email' => 'string|exists:user,email',
            'permission' => new HasPermissionInLanOrIsTournamentAdmin(Auth::id(), $tournamentId)
        ]);

        $this->checkValidation($validator);

        return response()->json($this->tournamentService->addOrganizer(
            $request->input('email'),
            $tournamentId
        ), 200);
    }

    public function create(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'tournament_start' => $request->input('tournament_start'),
            'tournament_end' => $request->input('tournament_end'),
            'players_to_reach' => $request->input('players_to_reach'),
            'teams_to_reach' => $request->input('teams_to_reach'),
            'rules' => $request->input('rules'),
            'permission' => 'create-tournament'
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'name' => 'required|string|max:255',
            'price' => 'integer|min:0',
            'tournament_start' => ['required', new AfterOrEqualLanStartTime($request->input('lan_id'))],
            'tournament_end' => ['required', 'after:tournament_start', new BeforeOrEqualLanEndTime($request->input('lan_id'))],
            'players_to_reach' => 'required|min:1|integer',
            'teams_to_reach' => 'required|min:1|integer',
            'rules' => 'required|string',
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->tournamentService->create(
            intval($request->input('lan_id')),
            $request->input('name'),
            Carbon::parse($request->input('tournament_start')),
            Carbon::parse($request->input('tournament_end')),
            intval($request->input('players_to_reach')),
            intval($request->input('teams_to_reach')),
            $request->input('rules'),
            intval($request->input('price'))
        ), 201);
    }

    public function delete(Request $request, string $tournamentId)
    {
        $validator = Validator::make([
            'tournament_id' => $tournamentId,
            'permission' => 'delete-tournament'
        ], [
            'tournament_id' => 'integer|exists:tournament,id,deleted_at,NULL',
            'permission' => new HasPermissionInLanOrIsTournamentAdmin(Auth::id(), $tournamentId)
        ]);

        $this->checkValidation($validator);

        return response()->json($this->tournamentService->delete($tournamentId), 200);
    }

    public function getAllForOrganizer(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL'
        ]);

        $this->checkValidation($validator);

        return response()->json($this->tournamentService->getAllForOrganizer(
            $request->input('lan_id')
        ), 200);
    }

    public function getAll(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL'
        ]);

        $this->checkValidation($validator);

        return response()->json($this->tournamentService->getAll($request->input('lan_id')), 200);
    }

    public function get(Request $request, string $tournamentId)
    {
        $validator = Validator::make([
            'tournament_id' => $tournamentId
        ], [
            'tournament_id' => 'integer|exists:tournament,id,deleted_at,NULL'
        ]);

        $this->checkValidation($validator);

        return response()->json($this->tournamentService->get($tournamentId), 200);
    }

    public function quit(Request $request, string $tournamentId)
    {
        $validator = Validator::make([
            'tournament_id' => $tournamentId
        ], [
            'tournament_id' => ['integer', 'exists:tournament,id,deleted_at,NULL', new UserIsTournamentAdmin],
        ]);

        $this->checkValidation($validator);

        return response()->json($this->tournamentService->quit($tournamentId), 200);
    }

    public function update(Request $request, string $tournamentId)
    {
        $validator = Validator::make([
            'tournament_id' => $tournamentId,
            'name' => $request->input('name'),
            'state' => $request->input('state'),
            'price' => $request->input('price'),
            'tournament_start' => $request->input('tournament_start'),
            'tournament_end' => $request->input('tournament_end'),
            'players_to_reach' => $request->input('players_to_reach'),
            'teams_to_reach' => $request->input('teams_to_reach'),
            'rules' => $request->input('rules'),
            'permission' => 'edit-tournament'
        ], [
            'tournament_id' => ['integer', 'exists:tournament,id,deleted_at,NULL', new PlayersToReachLock],
            'name' => 'string|max:255',
            'state' => ['nullable', Rule::in(['hidden', 'visible', 'started', 'finished'])],
            'price' => 'integer|min:0',
            'tournament_start' => [new AfterOrEqualLanStartTime($request->input('lan_id'))],
            'tournament_end' => ['after:tournament_start', new BeforeOrEqualLanEndTime($request->input('lan_id'))],
            'players_to_reach' => ['min:1', 'integer'],
            'teams_to_reach' => 'min:1|integer',
            'rules' => 'string',
            'permission' => new HasPermissionInLanOrIsTournamentAdmin(Auth::id(), $tournamentId)
        ]);

        $this->checkValidation($validator);

        return response()->json($this->tournamentService->update(
            $tournamentId,
            $request->input('name'),
            Carbon::parse($request->input('tournament_start')),
            Carbon::Parse($request->input('tournament_end')),
            intval($request->input('players_to_reach')),
            intval($request->input('teams_to_reach')),
            $request->input('state'),
            $request->input('rules'),
            intval($request->input('price'))
        ), 200);
    }
}
