<?php

namespace App\Services\Implementation;

use App\Http\Resources\Tournament\TournamentDetailsResource;
use App\Http\Resources\Tournament\TournamentResource;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\RoleRepositoryImpl;
use App\Repositories\Implementation\TournamentRepositoryImpl;
use App\Repositories\Implementation\UserRepositoryImpl;
use App\Rules\AfterOrEqualLanStartTime;
use App\Rules\BeforeOrEqualLanEndTime;
use App\Rules\HasPermissionInLan;
use App\Rules\HasPermissionInLanOrIsTournamentAdmin;
use App\Rules\PlayersToReachLock;
use App\Services\TournamentService;
use App\Tournament\Rules\UserIsTournamentAdmin;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TournamentServiceImpl implements TournamentService
{
    protected $lanRepository;
    protected $userRepository;
    protected $tournamentRepository;
    protected $roleRepository;

    /**
     * LanServiceImpl constructor.
     * @param LanRepositoryImpl $lanRepository
     * @param TournamentRepositoryImpl $tournamentRepository
     * @param UserRepositoryImpl $userRepository
     * @param RoleRepositoryImpl $roleRepository
     */
    public function __construct(
        LanRepositoryImpl $lanRepository,
        TournamentRepositoryImpl $tournamentRepository,
        UserRepositoryImpl $userRepository,
        RoleRepositoryImpl $roleRepository
    )
    {
        $this->lanRepository = $lanRepository;
        $this->tournamentRepository = $tournamentRepository;
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    public function create(Request $input): TournamentDetailsResource
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $tournamentValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'name' => $input->input('name'),
            'price' => $input->input('price'),
            'tournament_start' => $input->input('tournament_start'),
            'tournament_end' => $input->input('tournament_end'),
            'players_to_reach' => $input->input('players_to_reach'),
            'teams_to_reach' => $input->input('teams_to_reach'),
            'rules' => $input->input('rules'),
            'permission' => 'create-tournament'
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'name' => 'required|string|max:255',
            'price' => 'integer|min:0',
            'tournament_start' => ['required', new AfterOrEqualLanStartTime($input->input('lan_id'))],
            'tournament_end' => ['required', 'after:tournament_start', new BeforeOrEqualLanEndTime($input->input('lan_id'))],
            'players_to_reach' => 'required|min:1|integer',
            'teams_to_reach' => 'required|min:1|integer',
            'rules' => 'required|string',
            'permission' => new HasPermissionInLan($input->input('lan_id'), Auth::id())
        ]);

        if ($tournamentValidator->fails()) {
            throw new BadRequestHttpException($tournamentValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }

        $tournamentId = $this->tournamentRepository->create(
            $lan,
            $input->input('name'),
            new DateTime($input->input('tournament_start')),
            new DateTime($input->input('tournament_end')),
            $input->input('players_to_reach'),
            $input->input('teams_to_reach'),
            $input->input('rules'),
            intval($input->input('price'))
        );

        $this->tournamentRepository->associateOrganizerTournament(Auth::id(), $tournamentId);
        $tournament = $this->tournamentRepository->findById($tournamentId);

        return new TournamentDetailsResource($tournament);
    }

    public function getAllOrganizer(Request $input): AnonymousResourceCollection
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $tournamentValidator = Validator::make([
            'lan_id' => $input->input('lan_id')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL'
        ]);

        if ($tournamentValidator->fails()) {
            throw new BadRequestHttpException($tournamentValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }

        if (
            $this->roleRepository->userHasPermission('edit-tournament', Auth::id(), $lan->id) &&
            $this->roleRepository->userHasPermission('delete-tournamnet', Auth::id(), $lan->id) &&
            $this->roleRepository->userHasPermission('add-organizer', Auth::id(), $lan->id)
        ) {
            return TournamentResource::collection($this->tournamentRepository->getAllTournaments($lan->id));
        } else {
            return TournamentResource::collection($this->tournamentRepository->getTournamentsForOrganizer(Auth::user(), $lan));
        }
    }

    public function getAll(Request $input): AnonymousResourceCollection
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $tournamentValidator = Validator::make([
            'lan_id' => $input->input('lan_id')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL'
        ]);

        if ($tournamentValidator->fails()) {
            throw new BadRequestHttpException($tournamentValidator->errors());
        }

        $tournaments = $this->tournamentRepository->getAllTournaments($input->input('lan_id'));

        return TournamentResource::collection($tournaments);
    }

    public function edit(Request $input, string $tournamentId): TournamentDetailsResource
    {
        $lanId = $this->tournamentRepository->getTournamentsLanId(intval($tournamentId));
        $tournamentValidator = Validator::make([
            'tournament_id' => $tournamentId,
            'name' => $input->input('name'),
            'state' => $input->input('state'),
            'price' => $input->input('price'),
            'tournament_start' => $input->input('tournament_start'),
            'tournament_end' => $input->input('tournament_end'),
            'players_to_reach' => $input->input('players_to_reach'),
            'teams_to_reach' => $input->input('teams_to_reach'),
            'rules' => $input->input('rules'),
            'permission' => 'edit-tournament'
        ], [
            'tournament_id' => ['integer', 'exists:tournament,id,deleted_at,NULL'],
            'name' => 'string|max:255',
            'state' => ['nullable', Rule::in(['hidden', 'visible', 'started', 'finished'])],
            'price' => 'integer|min:0',
            'tournament_start' => [new AfterOrEqualLanStartTime($input->input('lan_id'))],
            'tournament_end' => ['after:tournament_start', new BeforeOrEqualLanEndTime($input->input('lan_id'))],
            'players_to_reach' => ['min:1', 'integer', new PlayersToReachLock($tournamentId)],
            'teams_to_reach' => 'min:1|integer',
            'rules' => 'string',
            'permission' => new HasPermissionInLanOrIsTournamentAdmin($lanId, Auth::id(), $tournamentId)
        ]);

        if ($tournamentValidator->fails()) {
            throw new BadRequestHttpException($tournamentValidator->errors());
        }

        $tournament = $this->tournamentRepository->findById($tournamentId);

        $tournament = $this->tournamentRepository->update(
            $tournament,
            $input->input('name'),
            $input->input('state'),
            new DateTime($input->input('tournament_start')),
            new DateTime($input->input('tournament_end')),
            $input->input('players_to_reach'),
            $input->input('teams_to_reach'),
            $input->input('rules'),
            intval($input->input('price'))
        );

        return new TournamentDetailsResource($tournament);
    }

    public function get(string $tournamentId): TournamentDetailsResource
    {
        $tournamentValidator = Validator::make([
            'tournament_id' => $tournamentId
        ], [
            'tournament_id' => 'integer|exists:tournament,id,deleted_at,NULL'
        ]);

        if ($tournamentValidator->fails()) {
            throw new BadRequestHttpException($tournamentValidator->errors());
        }

        $tournament = $this->tournamentRepository->findById($tournamentId);

        return new TournamentDetailsResource($tournament);
    }

    public function delete(string $tournamentId): TournamentResource
    {
        $lanId = $this->tournamentRepository->getTournamentsLanId(intval($tournamentId));
        $tournamentValidator = Validator::make([
            'tournament_id' => $tournamentId,
            'permission' => 'delete-tournament'
        ], [
            'tournament_id' => 'integer|exists:tournament,id,deleted_at,NULL',
            'permission' => new HasPermissionInLanOrIsTournamentAdmin($lanId, Auth::id(), $tournamentId)
        ]);

        if ($tournamentValidator->fails()) {
            throw new BadRequestHttpException($tournamentValidator->errors());
        }

        $tournament = $this->tournamentRepository->findById($tournamentId);
        $this->tournamentRepository->delete($tournament);

        return new TournamentResource($tournament);
    }

    public function quit(string $tournamentId): TournamentResource
    {
        $tournamentValidator = Validator::make([
            'tournament_id' => $tournamentId
        ], [
            'tournament_id' => ['integer', 'exists:tournament,id,deleted_at,NULL', new UserIsTournamentAdmin],
        ]);

        if ($tournamentValidator->fails()) {
            throw new BadRequestHttpException($tournamentValidator->errors());
        }

        $tournament = $this->tournamentRepository->findById($tournamentId);

        $organizerCount = $this->tournamentRepository->getOrganizerCount($tournament);
        $this->tournamentRepository->quit($tournament, Auth::user());

        if ($organizerCount <= 1) {
            $this->tournamentRepository->delete($tournament);
        }

        return new TournamentResource($tournament);
    }

    public function addOrganizer(Request $input, string $tournamentId): TournamentResource
    {
        $lanId = $this->tournamentRepository->getTournamentsLanId(intval($tournamentId));
        $tournamentValidator = Validator::make([
            'tournament_id' => $tournamentId,
            'email' => $input->input('email'),
            'permission' => 'add-organizer'
        ], [
            'tournament_id' => ['integer', 'exists:tournament,id,deleted_at,NULL'],
            'email' => 'string|exists:user,email',
            'permission' => new HasPermissionInLanOrIsTournamentAdmin($lanId, Auth::id(), $tournamentId)
        ]);

        if ($tournamentValidator->fails()) {
            throw new BadRequestHttpException($tournamentValidator->errors());
        }

        $tournament = $this->tournamentRepository->findById($tournamentId);
        $user = $this->userRepository->findByEmail($input->input('email'));

        $this->tournamentRepository->associateOrganizerTournament($user->id, $tournament->id);

        return new TournamentResource($tournament);
    }
}