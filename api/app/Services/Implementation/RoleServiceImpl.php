<?php

namespace App\Services\Implementation;

use App\Http\Resources\Role\GetPermissionsResource;
use App\Http\Resources\Role\GetRoleResource;
use App\Model\GlobalRole;
use App\Model\LanRole;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\RoleRepositoryImpl;
use App\Repositories\Implementation\UserRepositoryImpl;
use App\Services\RoleService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;

class RoleServiceImpl implements RoleService
{
    protected $roleRepository;
    protected $lanRepository;
    protected $userRepository;

    /**
     * RoleServiceImpl constructor.
     *
     * @param RoleRepositoryImpl $roleRepository
     * @param LanRepositoryImpl  $lanRepository
     * @param UserRepositoryImpl $userRepository
     */
    public function __construct(
        RoleRepositoryImpl $roleRepository,
        LanRepositoryImpl $lanRepository,
        UserRepositoryImpl $userRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->lanRepository = $lanRepository;
        $this->userRepository = $userRepository;
    }

    public function addPermissionsGlobalRole(int $roleId, array $permissions): GetRoleResource
    {
        // Pour chaque id de permission
        foreach ($permissions as $permissionId) {
            $this->roleRepository->linkPermissionIdGlobalRole($permissionId, $roleId);
        }

        // Retourner le rôle, dans la langue spécifiée
        return new GetRoleResource($this->roleRepository->findGlobalRoleById($roleId));
    }

    public function addPermissionsLanRole(int $roleId, array $permissions): GetRoleResource
    {
        // Pour chaque id de permission
        foreach ($permissions as $permissionId) {
            // Lier la permission au rôle de LAN
            $this->roleRepository->linkPermissionIdLanRole($permissionId, $roleId);
        }

        // Retourner le rôle, dans la langue spécifiée
        return new GetRoleResource($this->roleRepository->findLanRoleById($roleId));
    }

    public function assignGlobalRole(int $roleId, string $email): GetRoleResource
    {
        // Trouver l'utilisateur correspondant au courriel
        $user = $this->userRepository->findByEmail($email);

        // Lier l'utilisateur au rôle
        $this->roleRepository->linkGlobalRoleUser($roleId, $user->id);

        // Trouver le rôle
        $role = $this->roleRepository->findGlobalRoleById($roleId);

        // Retourner le rôle, dans la langue spécifiée
        return new GetRoleResource($role);
    }

    public function assignLanRole(int $roleId, string $email): GetRoleResource
    {
        // Trouver l'utilisateur correspondant au courriel
        $user = $this->userRepository->findByEmail($email);

        // Lier l'utilisateur au rôle
        $this->roleRepository->linkLanRoleUser($roleId, $user->id);

        // Trouver le rôle
        $role = $this->roleRepository->findLanRoleById($roleId);

        // Retourner le rôle, dans la langue spécifiée
        return new GetRoleResource($role);
    }

    public function createGlobalRole(
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription,
        array $permissions
    ): GlobalRole {
        // Créer le rôle global
        $roleId = $this->roleRepository->createGlobalRole(
            $name,
            $enDisplayName,
            $enDescription,
            $frDisplayName,
            $frDescription
        );

        // Pour chaque id de permission
        foreach ($permissions as $permissionId) {
            // Lier la permission au rôle créé
            $this->roleRepository->linkPermissionIdGlobalRole($permissionId, $roleId);
        }

        // Retourner le rôle global créé
        return $this->roleRepository->findGlobalRoleById($roleId);
    }

    public function createLanRole(
        int $lanId,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription,
        array $permissions
    ): LanRole {
        // Créer le rôle de LAN
        $roleId = $this->roleRepository->createLanRole(
            $lanId,
            $name,
            $enDisplayName,
            $enDescription,
            $frDisplayName,
            $frDescription
        );

        // Pour chaque id de permission
        foreach ($permissions as $permissionId) {
            // Lier la permission au rôle créé
            $this->roleRepository->linkPermissionIdLanRole($permissionId, $roleId);
        }

        // Retourner le rôle global créé
        return $this->roleRepository->findLanRoleById($roleId);
    }

    public function deleteGlobalRole(int $roleId): GetRoleResource
    {
        // Trouver le rôle à supprimer
        $role = $this->roleRepository->findGlobalRoleById($roleId);

        // Supprimer le rôle global
        $this->roleRepository->deleteGlobalRole($roleId);

        // Retourner le rôle supprimé
        return new GetRoleResource($role);
    }

    public function deleteLanRole(int $roleId): GetRoleResource
    {
        // Trouver le rôle à supprimer
        $role = $this->roleRepository->findLanRoleById($roleId);

        // Supprimer le rôle de LAN
        $this->roleRepository->deleteLanRole($roleId);

        // Retourner le rôle supprimé
        return new GetRoleResource($role);
    }

    public function deletePermissionsGlobalRole(int $roleId, array $permissions): GetRoleResource
    {
        // Pour chaque id de permission
        foreach ($permissions as $permissionId) {
            // Supprimer le lien entre la permission et le rôle global
            $this->roleRepository->unlinkPermissionIdGlobalRole($permissionId, $roleId);
        }

        // Trouver le rôle global
        $role = $this->roleRepository->findGlobalRoleById($roleId);

        // Retourner le rôle global, selon la langue courante
        return new GetRoleResource($role);
    }

    public function deletePermissionsLanRole(int $roleId, array $permissions): GetRoleResource
    {
        // Pour chaque id de permission
        foreach ($permissions as $permissionId) {
            // Supprimer le lien entre la permission et le rôle de LAN
            $this->roleRepository->unlinkPermissionIdLanRole($permissionId, $roleId);
        }

        // Trouver le rôle de LAN
        $role = $this->roleRepository->findLanRoleById($roleId);

        // Retourner le rôle global, selon la langue courante
        return new GetRoleResource($role);
    }

    public function getGlobalRolePermissions(int $roleId): AnonymousResourceCollection
    {
        return GetPermissionsResource::collection(
            $this->roleRepository->getGlobalRolePermissions($roleId)
        );
    }

    public function getGlobalRoles(): AnonymousResourceCollection
    {
        return GetRoleResource::Collection(
            $this->roleRepository->getGlobalRoles()
        );
    }

    public function getGlobalRoleUsers(int $roleId): Collection
    {
        return $this->roleRepository->getGlobalUserRoles($roleId);
    }

    public function getLanRolePermissions(int $roleId): AnonymousResourceCollection
    {
        return GetPermissionsResource::collection($this->roleRepository->getLanRolePermissions($roleId));
    }

    public function getLanRoles(int $lanId): AnonymousResourceCollection
    {
        return GetRoleResource::Collection($this->roleRepository->getLanRoles($lanId));
    }

    public function getLanRoleUsers(int $roleId): Collection
    {
        return $this->roleRepository->getLanUserRoles($roleId);
    }

    public function getPermissions(): AnonymousResourceCollection
    {
        return GetPermissionsResource::collection($this->roleRepository->getPermissions());
    }

    public function updateGlobalRole(
        int $roleId,
        ?string $name,
        ?string $enDisplayName,
        ?string $enDescription,
        ?string $frDisplayName,
        ?string $frDescription
    ): GlobalRole {
        // Mettre à jour le rôle global
        $this->roleRepository->updateGlobalRole(
            $roleId,
            $name,
            $enDisplayName,
            $enDescription,
            $frDisplayName,
            $frDescription
        );

        // Trouver et retourner le rôle global mis à jour
        return $this->roleRepository->findGlobalRoleById($roleId);
    }

    public function updateLanRole(
        int $roleId,
        ?string $name,
        ?string $enDisplayName,
        ?string $enDescription,
        ?string $frDisplayName,
        ?string $frDescription
    ): LanRole {
        // Mettre à jour le rôle de LAN
        $this->roleRepository->updateLanRole(
            $roleId,
            $name,
            $enDisplayName,
            $enDescription,
            $frDisplayName,
            $frDescription
        );

        // Trouver et retourner le rôle de LAN mis à jour
        return $this->roleRepository->findLanRoleById($roleId);
    }
}
