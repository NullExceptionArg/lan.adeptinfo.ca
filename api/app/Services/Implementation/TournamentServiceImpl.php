<?php

namespace App\Services\Implementation;

use App\Http\Resources\{Tournament\TournamentDetailsResource, Tournament\TournamentResource};
use App\Repositories\Implementation\{LanRepositoryImpl,
    RoleRepositoryImpl,
    TournamentRepositoryImpl,
    UserRepositoryImpl};
use App\Services\TournamentService;
use DateTime;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

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

    public function getAllForOrganizer(int $lanId): AnonymousResourceCollection
    {
        // Si l'utilisateur possède les permissions pour modifier, supprimer, et ajouter un organisateur à un tournoi, tous les LANs s'affichent.
        // Sinon seulement ceux qu'il administre.
        if (
            $this->roleRepository->userHasPermission('edit-tournament', Auth::id(), $lanId) &&
            $this->roleRepository->userHasPermission('delete-tournament', Auth::id(), $lanId) &&
            $this->roleRepository->userHasPermission('add-organizer', Auth::id(), $lanId)
        ) {
            return TournamentResource::collection($this->tournamentRepository->getAllTournaments($lanId));
        } else {
            return TournamentResource::collection(
                $this->tournamentRepository->getTournamentsForOrganizer(
                    Auth::id(),
                    $lanId
                )
            );
        }
    }

    public function getAll(int $lanId): AnonymousResourceCollection
    {
        $tournaments = $this->tournamentRepository->getAllTournaments($lanId);

        return TournamentResource::collection($tournaments);
    }

    public function get(string $tournamentId): TournamentDetailsResource
    {
        $tournament = $this->tournamentRepository->findById($tournamentId);

        return new TournamentDetailsResource($tournament);
    }

    public function quit(string $tournamentId): TournamentResource
    {
        $organizerCount = $this->tournamentRepository->getOrganizerCount($tournamentId);
        $this->tournamentRepository->deleteTournamentOrganizer($tournamentId, Auth::id());
        $tournament = $this->tournamentRepository->findById($tournamentId);

        if ($organizerCount <= 1) {
            $this->tournamentRepository->delete($tournamentId);
        }

        return new TournamentResource($tournament);
    }
}
