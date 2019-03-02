<?php

namespace App\Services;

use App\Http\Resources\{Tournament\TournamentDetailsResource, Tournament\TournamentResource};
use DateTime;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Méthodes pour exécuter la logique d'affaire des tournois.
 *
 * Interface TournamentService
 * @package App\Services
 */
interface TournamentService
{
    /**
     * Ajouter un organisateur à un tournoi
     *
     * @param string $email Courriel de l'organisateur à en devenir
     * @param string $tournamentId Id du tournoi
     * @return TournamentResource Tournoi du nouvel organisateur
     */
    public function addOrganizer(string $email, string $tournamentId): TournamentResource;

    /**
     * Créer un tournoi.
     *
     * @param int $lanId LAN du tournoi
     * @param int $userId Id de l'utilisateur qui créer le tournoi
     * @param string $name Nom du tournoi
     * @param DateTime $tournamentStart Date et heure de début du tournoi
     * @param DateTime $tournamentEnd Date et heure de fin du tournoi
     * @param int $playersToReach Nombre de joueurs à atteindre par équipe pour que le tournoi ait lieu
     * @param int $teamsToReach Nombre d'équipe à atteindre pour que le tournoi ait lieu
     * @param string $rules Règlements du tournoi
     * @param int|null $price Prix d'entrée du tournoi. Si nul, le prix est fixé a 0.
     * @return TournamentDetailsResource Tournoi créé
     */
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
    ): TournamentDetailsResource;

    /**
     * Supprimer un tournoi.
     *
     * @param string $tournamentId Id du tournoi
     * @return TournamentResource Tournoi supprimé
     */
    public function delete(string $tournamentId): TournamentResource;

    /**
     * Obtenir tout les tournoi, pour un organisateur.
     *
     * @param int $userId Id de l'organisateur
     * @param int $lanId Id du LAN des tournoi
     * @return AnonymousResourceCollection Tournois de l'organisateur
     */
    public function getAllForOrganizer(int $userId, int $lanId): AnonymousResourceCollection;

    /**
     * Obtenir tout les tournois, pour un joueur.
     *
     * @param int $lanId Id du LAN des tournois
     * @return AnonymousResourceCollection Tournois du LAN
     */
    public function getAll(int $lanId): AnonymousResourceCollection;

    /**
     * Obtenir les détails d'un LAN.
     *
     * @param string $tournamentId Id du tournoi
     * @return TournamentDetailsResource Détails du tournoi
     */
    public function get(string $tournamentId): TournamentDetailsResource;

    /**
     * Quitter l'organisation d'un tournoi.
     * Si l'organisateur qui quitte est le dernier, le tournoi est supprimé.
     *
     * @param int $userId
     * @param string $tournamentId
     * @return TournamentResource
     */
    public function quit(int $userId, string $tournamentId): TournamentResource;

    /**
     * Mettre à jour les informations d'un tournoi.
     * Si un champ est laissé nul, la valeur initiale du champ sera gardée.
     *
     * @param int $tournamentId Id du tournoi à mettre à jour
     * @param string|null $name Nouveau nom du tournoi
     * @param DateTime|null $tournamentStart Nouvelle date et heure de début du tournoi
     * @param DateTime|null $tournamentEnd Nouvelle date et heure de fin du tournoi
     * @param int|null $playersToReach Nouveau nombre de joueurs à atteindre par équipe pour que le tournoi ait lieu
     * @param int|null $teamsToReach Nouveau nombre d'équipe à atteindre pour que le tournoi ait lieu
     * @param string|null $state Nouvel état du tournoi
     * @param string|null $rules Nouveaux règlements du tournoi
     * @param int|null $price Nouveau prix pour faire parti du tournoi
     * @return TournamentDetailsResource Tournoi mis à jour
     */
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
    ): TournamentDetailsResource;
}
