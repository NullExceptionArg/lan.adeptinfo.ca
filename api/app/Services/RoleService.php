<?php

namespace App\Services;

use App\Http\Resources\Role\GetRoleResource;
use App\Model\{GlobalRole, LanRole};
use Illuminate\{Http\Resources\Json\AnonymousResourceCollection, Support\Collection};

/**
 * Méthodes pour exécuter la logique d'affaire des rôles.
 *
 * Interface RoleService
 * @package App\Services
 */
interface RoleService
{
    /**
     * Ajouter des permissions à un rôle global.
     *
     * @param int $roleId Id du rôle global
     * @param array $permissions Ids des permissions
     * @return GetRoleResource Rôle global des permissions ajoutées
     */
    public function addPermissionsGlobalRole(int $roleId, array $permissions): GetRoleResource;

    /**
     * Ajouter des permissions à un rôle de LAN.
     *
     * @param int $roleId Id du rôle de LAN
     * @param array $permissions Ids des permissions
     * @return GetRoleResource Rôle de LAN des permissions ajoutées
     */
    public function addPermissionsLanRole(int $roleId, array $permissions): GetRoleResource;

    /**
     * Assigner un rôle global à un utilisateur.
     *
     * @param int $roleId Id du rôle global
     * @param string $email Courriel de l'utilisateur
     * @return GetRoleResource Rôle global assigné
     */
    public function assignGlobalRole(int $roleId, string $email): GetRoleResource;

    /**
     * Assigner un rôle de LAN à un utilisateur.
     *
     * @param int $roleId Id du rôle de LAN
     * @param string $email Courriel de l'utilisateur
     * @return GetRoleResource Rôle de LAN assigné
     */
    public function assignLanRole(int $roleId, string $email): GetRoleResource;

    /**
     * Créer un rôle global.
     *
     * @param string $name Nom du rôle
     * @param string $enDisplayName Nom d'affichage du rôle, en anglais
     * @param string $enDescription Description du rôle, en anglais
     * @param string $frDisplayName Nom d'affichage du rôle, en français
     * @param string $frDescription Description du rôle, en français
     * @param array $permissions Permissions à ajouter au rôle
     * @return GlobalRole Rôle global créé
     */
    public function createGlobalRole(
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription,
        array $permissions
    ): GlobalRole;

    /**
     * Créer un rôle de LAN.
     *
     * @param int $lanId Id du LAN
     * @param string $name Nom du rôle
     * @param string $enDisplayName Nom d'affichage du rôle, en anglais
     * @param string $enDescription Description du rôle, en anglais
     * @param string $frDisplayName Nom d'affichage du rôle, en français
     * @param string $frDescription Description du rôle, en français
     * @param array $permissions Permissions à ajouter au rôle
     * @return LanRole Rôle de LAN créé
     */
    public function createLanRole(
        int $lanId,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription,
        array $permissions
    ): LanRole;

    /**
     * Supprimer un rôle global.
     *
     * @param int $roleId Id du rôle global
     * @return GetRoleResource Rôle global supprimé
     */
    public function deleteGlobalRole(int $roleId): GetRoleResource;

    /**
     * Supprimer un rôle de LAN.
     *
     * @param int $roleId Id du rôle de LAN
     * @return GetRoleResource Rôle de LAN supprimé
     */
    public function deleteLanRole(int $roleId): GetRoleResource;

    /**
     * Supprimer des permissions d'un rôle global.
     *
     * @param int $roleId Id du rôle global
     * @param array $permissions Ids des permissions à supprimer
     * @return GetRoleResource Rôle global des permissions supprimées
     */
    public function deletePermissionsGlobalRole(int $roleId, array $permissions): GetRoleResource;

    /**
     * Supprimer des permissions d'un rôle de LAN.
     *
     * @param int $roleId Id du rôle de LAN
     * @param array $permissions Ids des permissions à supprimer
     * @return GetRoleResource Rôle de LAN des permissions supprimées
     */
    public function deletePermissionsLanRole(int $roleId, array $permissions): GetRoleResource;

    /**
     * Obtenir les permissions d'un rôle global.
     *
     * @param int $roleId Id du rôle global
     * @return AnonymousResourceCollection Permissions du rôle global
     */
    public function getGlobalRolePermissions(int $roleId): AnonymousResourceCollection;

    /**
     * Obtenir les rôles globaux de l'API.
     *
     * @return AnonymousResourceCollection Rôles globaux de l'API
     */
    public function getGlobalRoles(): AnonymousResourceCollection;

    /**
     * Obtenir les permissions d'un rôle de LAN
     *
     * @param int $roleId Id du rôle de LAN
     * @return AnonymousResourceCollection Permissions du rôle de LAN
     */
    public function getLanRolePermissions(int $roleId): AnonymousResourceCollection;

    /**
     * Obtenir les rôles de LAN d'un LAN.
     *
     * @param int $lanId Id du LAN
     * @return AnonymousResourceCollection Rôles de LAN du LAN
     */
    public function getLanRoles(int $lanId): AnonymousResourceCollection;

    /**
     * Obtenir les utilisateurs d'un rôle de LAN
     *
     * @param int $roleId
     * @return Collection
     */
    public function getLanRoleUsers(int $roleId): Collection;

    /**
     * Obtenir les permissions de l'API.
     *
     * @return AnonymousResourceCollection Permissions de l'API
     */
    public function getPermissions(): AnonymousResourceCollection;

    public function getGlobalRoleUsers(int $roleId): Collection;

    public function updateGlobalRole(
        int $roleId,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): GlobalRole;

    public function updateLanRole(
        int $roleId,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): LanRole;
}
