<?php

namespace App\Services\Implementation;

use App\Http\Resources\Tournament\TournamentDetailsResource;
use App\Http\Resources\Tournament\TournamentResource;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\RoleRepositoryImpl;
use App\Repositories\Implementation\TournamentRepositoryImpl;
use App\Repositories\Implementation\UserRepositoryImpl;
use App\Services\TournamentService;
use App\Tournament\Rules\UserIsTournamentAdmin;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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

    public function addOrganizer(string $email, string $tournamentId): TournamentResource
    {
        $tournament = $this->tournamentRepository->findById($tournamentId);
        $user = $this->userRepository->findByEmail($email);

        $this->tournamentRepository->associateOrganizerTournament($user->id, $tournament->id);

        return new TournamentResource($tournament);
    }

    public function create(
        int $lanId,
        string $name,
        DateTime $tournamentStart,
        DateTime $tournamentEnd,
        int $playersToReach,
        int $teamsToReach,
        string $rules,
        ?int $price
    ): TournamentDetailsResource
    {
        $tournamentId = $this->tournamentRepository->create(
            $lanId,
            $name,
            $tournamentStart,
            $tournamentEnd,
            $playersToReach,
            $teamsToReach,
            $rules,
            $price
        );

        $this->tournamentRepository->associateOrganizerTournament(Auth::id(), $tournamentId);
        $tournament = $this->tournamentRepository->findById($tournamentId);

        return new TournamentDetailsResource($tournament);
    }

    public function delete(string $tournamentId): TournamentResource
    {
        $tournament = $this->tournamentRepository->findById($tournamentId);
        $this->tournamentRepository->delete($tournament->id);

        return new TournamentResource($tournament);
    }

    public function edit(
        int $tournamentId,
        ?string $name,
        ?DateTime $tournamentStart,
        ?DateTime $tournamentEnd,
        ?int $playersToReach,
        ?int $teamsToReach,
        ?string $state,
        ?string $rules,
        ?int $price
    ): TournamentDetailsResource
    {
        $this->tournamentRepository->update(
            $tournamentId,
            $name,
            $state,
            $tournamentStart,
            $tournamentEnd,
            $playersToReach,
            $teamsToReach,
            $rules,
            $price
        );
        return new TournamentDetailsResource($this->tournamentRepository->findById($tournamentId));
    }

    public function getAllOrganizer(int $lanId): AnonymousResourceCollection
    {
        // TODO Revérifier logique
        if (
            $this->roleRepository->userHasPermission('edit-tournament', Auth::id(), $lanId) &&
            $this->roleRepository->userHasPermission('delete-tournament', Auth::id(), $lanId) &&
            $this->roleRepository->userHasPermission('add-organizer', Auth::id(), $lanId)
        ) {
            return TournamentResource::collection($this->tournamentRepository->getAllTournaments($lanId));
        } else {
            return TournamentResource::collection($this->tournamentRepository->getTournamentsForOrganizer(Auth::id(), $lanId));
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
        $this->tournamentRepository->deleteTournamentOrganizer($tournament, Auth::user());

        if ($organizerCount <= 1) {
            $this->tournamentRepository->delete($tournament);
        }

        return new TournamentResource($tournament);
    }
}
