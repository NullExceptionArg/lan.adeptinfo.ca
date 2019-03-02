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
        // Trouver le tournoi
        $tournament = $this->tournamentRepository->findById($tournamentId);

        // Trouver l'utilisateur qui correspond au courriel
        $user = $this->userRepository->findByEmail($email);

        // Lier le l'utilisateur et le tournoi
        $this->tournamentRepository->associateOrganizerTournament($user->id, $tournament->id);

        // Retourner le tournoi dont l'organisation a été assigné à l'utilisateur
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
        // Créer le tournoi
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

        // Associer l'utilisateur au tournoi (l'utilisateur est organisateur du tournoi)
        $this->tournamentRepository->associateOrganizerTournament($userId, $tournamentId);

        // Trouver le tournoi créé
        $tournament = $this->tournamentRepository->findById($tournamentId);

        // Retourner le tournoi créé
        return new TournamentDetailsResource($tournament);
    }

    public function delete(string $tournamentId): TournamentResource
    {
        // Trouver le tournoi
        $tournament = $this->tournamentRepository->findById($tournamentId);

        // Supprimer le tournoi
        $this->tournamentRepository->delete($tournament->id);

        // Retourner le tournoi supprimé
        return new TournamentResource($tournament);
    }

    public function getAllForOrganizer(int $userId, int $lanId): AnonymousResourceCollection
    {
        // Si l'utilisateur possède les permissions pour modifier, supprimer, et ajouter un organisateur à un tournoi
        if (
            $this->roleRepository->userHasPermission('edit-tournament', $userId, $lanId) &&
            $this->roleRepository->userHasPermission('delete-tournament', $userId, $lanId) &&
            $this->roleRepository->userHasPermission('add-organizer', $userId, $lanId)
        ) {
            // Tous les LANs s'affichent
            return TournamentResource::collection($this->tournamentRepository->getAllTournaments($lanId));
        } else {
            // Seulement ceux que l'utilisateur administre
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
        // Trouver les tournoi du LAN
        $tournaments = $this->tournamentRepository->getAllTournaments($lanId);

        // Retourner les tournois
        return TournamentResource::collection($tournaments);
    }

    public function get(string $tournamentId): TournamentDetailsResource
    {
        // Trouver le tournoi
        $tournament = $this->tournamentRepository->findById($tournamentId);

        // Retourner les détails du tournoi
        return new TournamentDetailsResource($tournament);
    }

    public function quit(int $userId, string $tournamentId): TournamentResource
    {
        // Trouver le nombre d'organisateurs du tournoi
        $organizerCount = $this->tournamentRepository->getOrganizerCount($tournamentId);

        // Supprimer le lien entre l'organisateur et le tournoi
        $this->tournamentRepository->dissociateOrganizerTournament($tournamentId, $userId);

        // Trouver le tournoi
        $tournament = $this->tournamentRepository->findById($tournamentId);

        // Si le nombre d'organisateur avant la suppression du lien est plus petit ou égal à 1
        if ($organizerCount <= 1) {
            // Supprimer le tournoi
            $this->tournamentRepository->delete($tournamentId);
        }

        // Retourner le tournoi que l'organisateur a quitté
        return new TournamentResource($tournament);
    }

    public function removeOrganizer(string $email, string $tournamentId): TournamentResource
    {
        // Trouver le nombre d'organisateurs du tournoi
        $organizerCount = $this->tournamentRepository->getOrganizerCount($tournamentId);

        // Trouver le tournoi
        $tournament = $this->tournamentRepository->findById($tournamentId);

        // Trouver l'utilisateur qui correspond au courriel
        $user = $this->userRepository->findByEmail($email);

        // Supprimer le lien entre l'utilisateur et le tournoi
        $this->tournamentRepository->dissociateOrganizerTournament($user->id, $tournament->id);

        // Si le nombre d'organisateur avant la suppression du lien est plus petit ou égal à 1
        if ($organizerCount <= 1) {
            // Supprimer le tournoi
            $this->tournamentRepository->delete($tournamentId);
        }

        // Retourner le tournoi dont l'organisation a été assigné à l'utilisateur
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
        // Mettre à jour le tournoi
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

        // Retourner le tournoi mis à jour
        return new TournamentDetailsResource($this->tournamentRepository->findById($tournamentId));
    }
}
