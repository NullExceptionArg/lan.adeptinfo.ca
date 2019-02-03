<?php

namespace App\Services\Implementation;

use App\Http\Resources\{Tournament\TournamentDetailsResource, Tournament\TournamentResource};
use App\Repositories\Implementation\{LanRepositoryImpl,
    RoleRepositoryImpl,
    TournamentRepositoryImpl,
    UserRepositoryImpl};
use App\Services\TournamentService;
use DateTime;
use Illuminate\{Http\Resources\Json\AnonymousResourceCollection};

class TournamentServiceImpl implements TournamentService
{
    protected $lanRepository;
    protected $userRepository;
    protected $tournamentRepository;
    protected $roleRepository;

    /**
     * TournamentServiceImpl constructor.
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
        int $userId,
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

        $this->tournamentRepository->associateOrganizerTournament($userId, $tournamentId);
        $tournament = $this->tournamentRepository->findById($tournamentId);

        return new TournamentDetailsResource($tournament);
    }

    public function delete(string $tournamentId): TournamentResource
    {
        $tournament = $this->tournamentRepository->findById($tournamentId);
        $this->tournamentRepository->delete($tournament->id);

        return new TournamentResource($tournament);
    }

    public function getAllForOrganizer(int $userId, int $lanId): AnonymousResourceCollection
    {
        // Si l'utilisateur possède les permissions pour modifier, supprimer, et ajouter un organisateur à un tournoi, tous les LANs s'affichent.
        // Sinon seulement ceux qu'il administre.
        if (
            $this->roleRepository->userHasPermission('edit-tournament', $userId, $lanId) &&
            $this->roleRepository->userHasPermission('delete-tournament', $userId, $lanId) &&
            $this->roleRepository->userHasPermission('add-organizer', $userId, $lanId)
        ) {
            return TournamentResource::collection($this->tournamentRepository->getAllTournaments($lanId));
        } else {
            return TournamentResource::collection(
                $this->tournamentRepository->getTournamentsForOrganizer(
                    $userId,
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

    public function quit(int $userId, string $tournamentId): TournamentResource
    {
        $organizerCount = $this->tournamentRepository->getOrganizerCount($tournamentId);
        $this->tournamentRepository->deleteTournamentOrganizer($tournamentId, $userId);
        $tournament = $this->tournamentRepository->findById($tournamentId);

        if ($organizerCount <= 1) {
            $this->tournamentRepository->delete($tournamentId);
        }

        return new TournamentResource($tournament);
    }

    public function update(
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
}
