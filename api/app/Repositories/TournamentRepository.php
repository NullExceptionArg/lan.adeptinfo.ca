<?php

namespace App\Repositories;

use App\Model\Tournament;
use DateTime;
use Illuminate\Support\Collection;

/**
 * Méthodes pour accéder aux tables de base de donnée liées aux tournois.
 *
 * Interface TournamentRepository
 * @package App\Repositories
 */
interface TournamentRepository
{
    /**
     * Déterminer si un utilisateur administre des tournois.
     *
     * @param int $userId Id de l'utilisateur.
     * @param int $lanId Id du LAN des tournois.
     * @return bool Si un utilisateur administre des tournois.
     */
    public function adminHasTournaments(int $userId, int $lanId): bool;

    /**
     * Associer un utilisateur à un tournoi.
     *
     * @param int $organizerId Id de l'utilisateur qui sera l'organisateur.
     * @param int $tournamentId Id du tournoi.
     */
    public function associateOrganizerTournament(int $organizerId, int $tournamentId): void;

    /**
     * Créer un tournoi.
     *
     * @param int $lanId Id du LAN du tournoi.
     * @param string $name Nom du tournoi.
     * @param DateTime $tournamentStart Date et heure de début du tournoi.
     * @param DateTime $tournamentEnd Date et heure de fin du tournoi.
     * @param int $playersToReach Nombre de joueurs à atteindre par équipe.
     * @param int $teamsToReach Nombre d'équipes à atteindre pour que le tournoi ait lieu.
     * @param string $rules Règlements du tournoi.
     * @param int|null $price Prix d'entrée du tournoi.
     * @return int Id du tournoi créé.
     */
    public function create(
        int $lanId,
        string $name,
        DateTime $tournamentStart,
        DateTime $tournamentEnd,
        int $playersToReach,
        int $teamsToReach,
        string $rules,
        ?int $price
    ): int;

    public function delete(int $tournamentId): void;

    /**
     * Supprimer le lien entre un organisateur de tournoi et le tournoi.
     *
     * @param int $tournamentId Id du tournoi.
     * @param int $userId Id de l'utilisateur.
     */
    public function deleteTournamentOrganizer(int $tournamentId, int $userId): void;

    /**
     * Trouver un tournoi.
     *
     * @param int $id Id du tournoi à trouver.
     * @return Tournament|null Tournoi trouvé, null si rien n'a été trouvé.
     */
    public function findById(int $id): ?Tournament;

    /**
     * Obtenir les tournois d'un LAN.
     *
     * @param int $lanId Id du LAN.
     * @return Collection Tournois trouvés.
     */
    public function getAllTournaments(int $lanId): Collection;

    /**
     * Obtenir le nombre d'organisateurs d'un tournois.
     *
     * @param int $tournamentId Id du tournoi.
     * @return int Nombre d'organisateurs du tournoi.
     */
    public function getOrganizerCount(int $tournamentId): int;

    /**
     * Obtenir le nombre d'équipes complètes pour un tournoi.
     *
     * @param int $tournamentId Id du tournoi.
     * @return int Nombre d'équipes complètes.
     */
    public function getReachedTeams(int $tournamentId): int;

    /**
     * Obtenir les tournois qu'organise un utilisateur.
     *
     * @param int $userId Id de l'utilisateur.
     * @param int $lanId Id du LAN du tournoi.
     * @return Collection Tournois trouvés, null si rien n'a été trouvé.
     */
    public function getTournamentsForOrganizer(int $userId, int $lanId): Collection;

    /**
     * Obtenir l'id du LAN d'un tournoi.
     *
     * @param int $tournamentId Id du tournoi.
     * @return int|null Id du LAN, null si rien n'a été trouvé.
     */
    public function getTournamentsLanId(int $tournamentId): ?int;

    /**
     * Mettre à jour les informations d'un tournoi.
     *
     * @param int $tournamentId Id du tournoi à mettre à jour.
     * @param string|null $name Nom du tournoi. (Optionnel)
     * @param string|null $state État courant du tournoi. (Optionnel)
     * @param DateTime|null $tournamentStart Date et heure de début du tournoi. (Optionnel)
     * @param DateTime|null $tournamentEnd Date et heure de fin du tournoi. (Optionnel)
     * @param int|null $playersToReach Nombre de joueurs à atteindre par équipe du tournoi. (Optionnel)
     * @param int|null $teamsToReach Nombre d'équipes à atteindre pour que le tournoi ait lieu. (Optionnel)
     * @param string|null $rules Règles du tournoi. (Optionnel)
     * @param int|null $price Prix du tournoi. (Optionnel)
     */
    public function update(
        int $tournamentId,
        ?string $name,
        ?string $state,
        ?DateTime $tournamentStart,
        ?DateTime $tournamentEnd,
        ?int $playersToReach,
        ?int $teamsToReach,
        ?string $rules,
        ?int $price
    ): void;
}
