<?php

namespace App\Services;

use App\Http\Resources\Team\GetUsersTeamDetailsResource;
use App\Model\Request as TeamRequest;
use App\Model\Tag;
use App\Model\Team;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Méthodes pour exécuter la logique d'affaire des équipes.
 *
 * Interface TeamService
 */
interface TeamService
{
    /**
     * Accepter une demande pour joindre une équipe.
     *
     * @param int $requestId Id de la demande
     *
     * @return Tag Tag de joueur de l'utilisateur qui a été accepté
     */
    public function acceptRequest(int $requestId): Tag;

    /**
     * Changer de chef d'équipe.
     *
     * @param int $tagId  Id du tag de joueur du nouveau chef de l'équipe
     * @param int $teamId Id de l'équipe
     *
     * @return Tag Tag de joueur du nouveau chef de l'équipe
     */
    public function changeLeader(int $tagId, int $teamId): Tag;

    /**
     * Créer une requête pour joindre une équipe.
     *
     * @param int $teamId Id de l'équipe
     * @param int $tagId  Id du tag du joueur qui demande à joindre l'équipe
     *
     * @return TeamRequest Requête pour joindre l'équipe
     */
    public function createRequest(int $teamId, int $tagId): TeamRequest;

    /**
     * Créer une équipe pour participer à un tournoi.
     *
     * @param int    $tournamentId Id du tournoi
     * @param string $name         Nom de l'équipe
     * @param string $tag          Tag d'équipe de l'équipe
     * @param int    $userTagId    Id du tag de l'utilisateur qui créer l'équipe
     *
     * @return Team Équipe créée
     */
    public function create(int $tournamentId, string $name, string $tag, int $userTagId): Team;

    /**
     * Supprimer une équipe.
     *
     * @param int $teamId Id de l'équipe à supprimer
     *
     * @return Team Équipe supprimée
     */
    public function delete(int $teamId): Team;

    /**
     * Supprimer une requête pour entrer dans une équipe, en tant que chef de l'équipe.
     *
     * @param int $requestId Id de la requête
     *
     * @return Tag Tag du joueur dont la requête a été supprimée
     */
    public function deleteRequestLeader(int $requestId): Tag;

    /**
     * Supprimer une requête pour entrer dans une équipe, en tant que joueur qui a créé la requête.
     *
     * @param int $requestId Id de la requête
     *
     * @return Team Équipe pour laquelle la requête a été supprimée
     */
    public function deleteRequestPlayer(int $requestId): Team;

    /**
     * Obtenir les requêtes d'un utilisateur pour entrer dans des équipes, pour un LAN.
     *
     * @param int $userId Id de l'utilisateur
     * @param int $lanId  Id du LAN
     *
     * @return AnonymousResourceCollection Requêtes pour entrer dans des équipes
     */
    public function getRequests(int $userId, int $lanId): AnonymousResourceCollection;

    /**
     * Obtenir les détails de l'équipe dont fait parti un utilisateur.
     * Si l'utilisateur est chef de l'équipe, il vera aussi les requêtes pour entrer dans l'équipe.
     *
     * @param int $userId Id de l'utilisateur
     * @param int $teamId Id de l'équipe
     *
     * @return GetUsersTeamDetailsResource Détails de l'équipe d'un utilisateur
     */
    public function getUsersTeamDetails(int $userId, int $teamId): GetUsersTeamDetailsResource;

    /**
     * Obtenir les équipes d'un utilisateur pour un LAN.
     *
     * @param int $userId Id de l'utilisateur
     * @param int $lanId  Id du LAN
     *
     * @return AnonymousResourceCollection Équipes de l'utilisateur
     */
    public function getUserTeams(int $userId, int $lanId): AnonymousResourceCollection;

    /**
     * Retirer un joueur d'une équipe.
     *
     * @param int $teamId Id de l'équipe
     * @param int $tagId  Id du tag de joueur
     *
     * @return Tag Tag du joueur qui a été retiré de l'équipe.
     */
    public function kick(int $teamId, int $tagId): Tag;

    /**
     * Quitter une équipe.
     *
     * @param int $userId Id de l'utilisateur qui souhaite quitter l'équipe
     * @param int $teamId Id de l'équipe que l'utilisateur souhaite quitter
     *
     * @return Team Équipe que l'utilisateur a quitté
     */
    public function leave(int $userId, int $teamId): Team;
}
