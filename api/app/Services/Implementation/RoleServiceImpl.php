<?php

namespace App\Services\Implementation;

use App\Http\Resources\{Role\GetPermissionsResource, Role\GetRoleResource};
use App\Model\{GlobalRole, LanRole};
use App\Repositories\Implementation\{LanRepositoryImpl, RoleRepositoryImpl, UserRepositoryImpl};
use App\Services\RoleService;
use Illuminate\{Http\Resources\Json\AnonymousResourceCollection, Support\Collection};

class RoleServiceImpl implements RoleService
{
    protected $roleRepository;
    protected $lanRepository;
    protected $userRepository;

    /**
     * RoleServiceImpl constructor.
     * @param RoleRepositoryImpl $roleRepository
     * @param LanRepositoryImpl $lanRepository
     * @param UserRepositoryImpl $userRepository
     */
    public function __construct(
        RoleRepositoryImpl $roleRepository,
        LanRepositoryImpl $lanRepository,
        UserRepositoryImpl $userRepository
    )
    {
        $this->roleRepository = $roleRepository;
        $this->lanRepository = $lanRepository;
        $this->userRepository = $userRepository;
    }

    public function addPermissionsGlobalRole(int $roleId, array $permissions): GetRoleResource
    {
        foreach ($permissions as $permissionId) {
            $this->roleRepository->linkPermissionIdGlobalRole($permissionId, $roleId);
        }
        return new GetRoleResource($this->roleRepository->findGlobalRoleById($roleId));
    }

    public function addPermissionsLanRole(int $roleId, array $permissions): GetRoleResource
    {
        foreach ($permissions as $permissionId) {
            $this->roleRepository->linkPermissionIdLanRole($permissionId, $roleId);
        }
        return new GetRoleResource($this->roleRepository->findLanRoleById($roleId));
    }

    public function assignGlobalRole(int $roleId, string $email): GetRoleResource
    {
        $user = $this->userRepository->findByEmail($email);

        $this->roleRepository->linkGlobalRoleUser($roleId, $user->id);
        $role = $this->roleRepository->findGlobalRoleById($roleId);

        return new GetRoleResource($role);
    }

    public function assignLanRole(int $roleId, string $email): GetRoleResource
    {
        $user = $this->userRepository->findByEmail($email);

        $this->roleRepository->linkLanRoleUser($roleId, $user->id);
        $role = $this->roleRepository->findLanRoleById($roleId);

        return new GetRoleResource($role);
    }

    public function createGlobalRole(
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription,
        array $permissions
    ): GlobalRole
    {
        $roleId = $this->roleRepository->createGlobalRole(
            $name,
            $enDisplayName,
            $enDescription,
            $frDisplayName,
            $frDescription
        );

        foreach ($permissions as $permissionId) {
            $this->roleRepository->linkPermissionIdGlobalRole($permissionId, $roleId);
        }

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
    ): LanRole
    {
        $roleId = $this->roleRepository->createLanRole(
            $lanId,
            $name,
            $enDisplayName,
            $enDescription,
            $frDisplayName,
            $frDescription
        );

        foreach ($permissions as $permissionId) {
            $this->roleRepository->linkPermissionIdLanRole($permissionId, $roleId);
        }

        return $this->roleRepository->findLanRoleById($roleId);
    }

    public function deleteGlobalRole(int $roleId): GetRoleResource
    {
        $role = $this->roleRepository->findGlobalRoleById($roleId);
        $this->roleRepository->deleteGlobalRole($roleId);

        return new GetRoleResource($role);
    }

    public function deleteLanRole(int $roleId): GetRoleResource
    {
        $role = $this->roleRepository->findLanRoleById($roleId);
        $this->roleRepository->deleteLanRole($roleId);

        return new GetRoleResource($role);
    }

    public function deletePermissionsGlobalRole(int $roleId, array $permissions): GetRoleResource
    {
        foreach ($permissions as $permissionId) {
            $this->roleRepository->unlinkPermissionIdGlobalRole($permissionId, $roleId);
        }

        $role = $this->roleRepository->findGlobalRoleById($roleId);

        return new GetRoleResource($role);
    }

    public function deletePermissionsLanRole(int $roleId, array $permissions): GetRoleResource
    {
        foreach ($permissions as $permissionId) {
            $this->roleRepository->unlinkPermissionIdLanRole($permissionId, $roleId);
        }

        $role = $this->roleRepository->findLanRoleById($roleId);

        return new GetRoleResource($role);
    }

    public function getGlobalRolePermissions(int $roleId): AnonymousResourceCollection
    {
        return GetPermissionsResource::collection($this->roleRepository->getGlobalRolePermissions($roleId));
    }

    public function getGlobalRoles(): AnonymousResourceCollection
    {
        return GetRoleResource::Collection($this->roleRepository->getGlobalRoles());
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
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): GlobalRole
    {
        $this->roleRepository->updateGlobalRole(
            $roleId,
            $name,
            $enDisplayName,
            $enDescription,
            $frDisplayName,
            $frDescription
        );

        return $this->roleRepository->findGlobalRoleById($roleId);
    }

    public function updateLanRole(
        int $roleId,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): LanRole
    {
        $this->roleRepository->updateLanRole(
            $roleId,
            $name,
            $enDisplayName,
            $enDescription,
            $frDisplayName,
            $frDescription
        );

        return $this->roleRepository->findLanRoleById($roleId);
    }
}
