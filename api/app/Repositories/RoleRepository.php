<?php

namespace App\Repositories;

use App\Model\{GlobalRole, LanRole};
use Illuminate\Support\Collection;

/**
 * Méthodes pour accéder aux tables de base de donnée liées aux rôles.
 *
 * Interface RoleRepository
 * @package App\Repositories
 */
interface RoleRepository
{
    /**
     * Ajouter les rôles de LAN par défaut à un LAN.
     *
     * @param int $lanId Id du LAN pour lequel les rôles seront ajoutés.
     */
    public function addDefaultLanRoles(int $lanId): void;

    /**
     * Créer un rôle global.
     *
     * @param string $name Nom du rôle.
     * @param string $enDisplayName Nom d'affichage du rôle, en anglais.
     * @param string $enDescription Description du rôle, en anglais.
     * @param string $frDisplayName Nom d'affichage du rôle, en français.
     * @param string $frDescription Description du rôle, en français.
     * @return int Id du rôle global créé.
     */
    public function createGlobalRole(
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): int;

    /**
     * Créer un rôle de LAN.
     *
     * @param int $lanId Id du LAN du rôle.
     * @param string $name Nom du rôle.
     * @param string $enDisplayName Nom d'affichage du rôle, en anglais.
     * @param string $enDescription Description du rôle, en anglais.
     * @param string $frDisplayName Nom d'affichage du rôle, en français.
     * @param string $frDescription Description du rôle, en français.
     * @return int Id du rôle de LAN créé.
     */
    public function createLanRole(
        int $lanId,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): int;

    /**
     * Supprimer un rôle global.
     *
     * @param int $roleId Id du rôle global à supprimer.
     */
    public function deleteGlobalRole(int $roleId): void;

    /**
     * Supprimer un rôle de LAN.
     *
     * @param int $roleId Id du rôle de LAN à supprimer.
     */
    public function deleteLanRole(int $roleId): void;

    /**
     * Trouver un rôle global.
     *
     * @param int $id Id du rôle global.
     * @return GlobalRole|null Rôle global trouvé, ou null si rien n'a été trouvé.
     */
    public function findGlobalRoleById(int $id): ?GlobalRole;

    /**
     * Trouver un rôle de LAN.
     *
     * @param int $id Id du rôle de LAN.
     * @return LanRole|null Rôle de LAN trouvé, ou null si rien n'a été trouvé.
     */
    public function findLanRoleById(int $id): ?LanRole;

    /**
     * Obtenir les permissions d'un administrateur, pour un LAN.
     * Les permissions sont celles contenues dans les rôles globaux, et les rôles de LAN.
     *
     * @param int $lanId Id du LAN pour lequel l'utilisateur aurait un ou plusieurs rôles de LAN.
     * @param int $userId Id de l'utilisateur qui possède les permissions.
     * @return Collection Permissions trouvées.
     */
    public function getAdminPermissions(int $lanId, int $userId): Collection;

    /**
     * Obtenir les permissions d'un rôle global.
     *
     * @param int $roleId Id du rôle global.
     * @return Collection Permissions trouvées.
     */
    public function getGlobalRolePermissions(int $roleId): Collection;

    /**
     * Obtenir les rôles globaux de l'application.
     *
     * @return Collection Rôles trouvés.
     */
    public function getGlobalRoles(): Collection;

    /**
     * Obtenir les utilisateurs qui possèdent un rôle global.
     *
     * @param int $roleId Id du rôle global qui contiendrait des utilisateurs.
     * @return Collection Utilisateurs trouvés.
     */
    public function getGlobalUserRoles(int $roleId): Collection;

    /**
     * Obtenir les permissions d'un rôle de LAN.
     *
     * @param int $roleId Id du rôle de LAN.
     * @return Collection Permissions trouvées.
     */
    public function getLanRolePermissions(int $roleId): Collection;

    /**
     * Obtenir les rôles de LAN d'un LAN.
     * @param int $lanId Id du LAN pour lequel les permissions seront retournées.
     * @return Collection Rôles trouvés.
     */
    public function getLanRoles(int $lanId): Collection;

    /**
     * Obtenir les utilisateurs qui possèdent un rôle de LAN, pour un LAN.
     *
     * @param int $roleId Id du rôle de LAN qui contiendrait des utilisateurs.
     * @return Collection Utilisateurs trouvés.
     */
    public function getLanUserRoles(int $roleId): Collection;

    /**
     * Obtenir les permissions de l'application
     *
     * @return Collection Permissions trouvées.
     */
    public function getPermissions(): Collection;

    /**
     * Obtenir les rôles globaux d'un utilisateur
     *
     * @param string $email Courriel de l'utilisateur
     * @return Collection Rôles globaux trouvés.
     */
    public function getUsersGlobalRoles(string $email): Collection;

    /**
     * Obtenir les rôles de LAN d'un utilisateur, pour un LAN.
     *
     * @param string $email Courriel de l'utilisateur.
     * @param int $lanId Id du LAN.
     * @return Collection Rôles de LAN trouvés.
     */
    public function getUsersLanRoles(string $email, int $lanId): Collection;

    /**
     * Lier un utilisateur et un rôle global.
     *
     * @param int $roleId Id du rôle global.
     * @param int $userId Id de l'utilisateur.
     */
    public function linkGlobalRoleUser(int $roleId, int $userId): void;

    /**
     * Lier un utilisateur et un rôle de LAN.
     *
     * @param int $roleId Id du rôle de LAN.
     * @param int $userId Id de l'utilisateur.
     */
    public function linkLanRoleUser(int $roleId, int $userId): void;

    /**
     * Lier une permission et un rôle global.
     *
     * @param string $permissionId Id de la permission.
     * @param int $roleId Id du rôle global.
     */
    public function linkPermissionIdGlobalRole(string $permissionId, int $roleId): void;

    /**
     * Lier une permission et un rôle de LAN.
     *
     * @param string $permissionId Id de la permission.
     * @param int $roleId Id du rôle de LAN.
     */
    public function linkPermissionIdLanRole(string $permissionId, int $roleId): void;

    /**
     * Supprimer le lien entre une permission et un rôle global.
     *
     * @param int $permissionId Id de la permission.
     * @param int $roleId Id du rôle global.
     */
    public function unlinkPermissionIdGlobalRole(int $permissionId, int $roleId): void;

    /**
     * Supprimer le lien entre une permission et un rôle de LAN.
     *
     * @param int $permissionId Id de la permission.
     * @param int $roleId Id du rôle de LAN.
     */
    public function unlinkPermissionIdLanRole(int $permissionId, int $roleId): void;

    /**
     * Mettre à jour un rôle global.
     *
     * @param int $roleId Id du rôle global à modifier.
     * @param string|null $name Nom du rôle global. (Optionnel)
     * @param string|null $enDisplayName Nom d'affichage du rôle global, en anglais. (Optionnel)
     * @param string|null $enDescription Description du rôle global, en anglais. (Optionnel)
     * @param string|null $frDisplayName Nom d'affichage du rôle global, en français. (Optionnel)
     * @param string|null $frDescription Description du rôle global, en français. (Optionnel)
     */
    public function updateGlobalRole(
        int $roleId,
        ?string $name,
        ?string $enDisplayName,
        ?string $enDescription,
        ?string $frDisplayName,
        ?string $frDescription
    ): void;

    /**
     * Mettre à jour un rôle de LAN.
     *
     * @param int $roleId Id du rôle de LAN à modifier.
     * @param string|null $name Nom du rôle de LAN. (Optionnel)
     * @param string|null $enDisplayName Nom d'affichage du rôle de LAN, en anglais. (Optionnel)
     * @param string|null $enDescription Description du rôle de LAN, en anglais. (Optionnel)
     * @param string|null $frDisplayName Nom d'affichage du rôle de LAN, en français. (Optionnel)
     * @param string|null $frDescription Description du rôle de LAN, en français. (Optionnel)
     */
    public function updateLanRole(
        int $roleId,
        ?string $name,
        ?string $enDisplayName,
        ?string $enDescription,
        ?string $frDisplayName,
        ?string $frDescription
    ): void;

    /**
     * Vérifier si un utilisateur possède une permission, dans un rôle de LAN, ou un rôle global.
     *
     * @param string $permission Nom de la permission.
     * @param int $userId Id de l'utilisateur.
     * @param int $lanId Id du LAN pour le rôle de LAN.
     * @return bool Si l'utilisateur possède la permission.
     */
    public function userHasPermission(string $permission, int $userId, int $lanId): bool;
}
