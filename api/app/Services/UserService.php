<?php

namespace App\Services;

use App\Http\Resources\User\GetAdminRolesResource;
use App\Http\Resources\User\GetAdminSummaryResource;
use App\Http\Resources\User\GetUserCollection;
use App\Http\Resources\User\GetUserDetailsResource;
use App\Http\Resources\User\GetUserSummaryResource;
use App\Model\Tag;
use App\Model\User;

/**
 * Méthodes pour exécuter la logique d'affaire des utilisateurs.
 *
 * Interface UserService
 */
interface UserService
{
    /**
     * Confirmer le compte d'un utilisateur.
     *
     * @param string $confirmationCode Code de confirmation de l'utilisateur
     */
    public function confirm(string $confirmationCode): void;

    /**
     * Créer un tag de joueur pour un utilisateur.
     *
     * @param int    $userId Id de l'utilisateur
     * @param string $name   Nom du tag de joueur
     *
     * @return Tag Tag de joueur créé
     */
    public function createTag(int $userId, string $name): Tag;

    /**
     * Supprimer un utilisateur.
     *
     * @param int $userId Id de l'utilisateur
     */
    public function deleteUser(int $userId): void;

    /**
     * Obtenir les rôles d'un utilisateur.
     *
     * @param string $email Courriel de l'utilisateur
     * @param int    $lanId Id du LAN pour les rôles de LAN
     *
     * @return GetAdminRolesResource Rôles trouvés
     */
    public function getAdminRoles(string $email, int $lanId): GetAdminRolesResource;

    /**
     * Obtenir le sommaire d'un administrateur. (Identité, s'il possède des tournoi, et ses permissions).
     *
     * @param int $userId Id de l'utilisateur
     * @param int $lanId  Id du LAN
     *
     * @return GetAdminSummaryResource Sommaire de l'utilisateur
     */
    public function getAdminSummary(int $userId, int $lanId): GetAdminSummaryResource;

    /**
     * Obtenir les détails d'un utilisateur. (Identité, siège courant, historique des places).
     *
     * @param int    $lanId Id du LAN, pour le siège courant
     * @param string $email Courriel de l'utilisateur
     *
     * @return GetUserDetailsResource Détails de l'utilisateur
     */
    public function getUserDetails(int $lanId, string $email): GetUserDetailsResource;

    /**
     * Obtenir les utilisateurs de l'API.
     *
     * @param string|null $queryString    Chaîne de caractère de recherche effective sur le nom, le prénom, et le courriel.
     *                                    Si nul, la chaîne est vide.
     * @param string|null $orderColumn    Colonne selon laquelle les résultats seront mis en ordre.
     *                                    Si nul, trier par nom de famille.
     * @param string|null $orderDirection Ordre de trie, ascendant (ASC), ou descendant (DESC)
     *                                    Si nul, ascendant.
     * @param int|null    $itemsPerPage   Nombre d'items à afficher par page.
     *                                    Si nul, 15.
     * @param int|null    $currentPage    Index de la page courante.
     *                                    Si nul, 1 (Première page)
     *
     * @return GetUserCollection Utilisateurs trouvés
     */
    public function getUsers(
        ?string $queryString,
        ?string $orderColumn,
        ?string $orderDirection,
        ?int $itemsPerPage,
        ?int $currentPage
    ): GetUserCollection;

    /**
     * Obtenir le sommaire d'un utilisateur. (Identité et nombre de requête pour joindre une équipe en attente).
     *
     * @param int $userId Id de l'utilisateur
     * @param int $lanId  Id du LAN pour les requêtes en attente
     *
     * @return GetUserSummaryResource Sommaire de l'utilisateur
     */
    public function getUserSummary(int $userId, int $lanId): GetUserSummaryResource;

    /**
     * Déconnecter l'utilisateur courant.
     */
    public function logOut(): void;

    /**
     * Se connecter avec Facebook.
     *
     * @param string $accessToken Jeton de Facebook
     *
     * @return array Jeton d'accessibilité de l'API, et si l'utilisateur est nouveau dans l'application
     */
    public function signInFacebook(string $accessToken): array;

    /**
     * Se connecter avec Google.
     *
     * @param string $accessToken Jeton de Google
     *
     * @return array Jeton d'accessibilité de l'API, et si l'utilisateur est nouveau dans l'application
     */
    public function signInGoogle(string $accessToken): array;

    /**
     * Créer un utilisateur de l'API.
     *
     * @param string $firstName Prénom de l'utilisateur
     * @param string $lastName  Nom de l'utilisateur
     * @param string $email     Courriel de l'utilisateur
     * @param string $password  Mot de passe
     *
     * @return User Utilisateur créé
     */
    public function signUpUser(string $firstName, string $lastName, string $email, string $password): User;
}
