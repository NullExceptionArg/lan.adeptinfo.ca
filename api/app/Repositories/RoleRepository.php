<?php

namespace App\Repositories;


use App\Model\GlobalRole;
use App\Model\Lan;
use App\Model\LanRole;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

interface RoleRepository
{
    public function createLanRole(
        int $lanId,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): LanRole;

    public function editLanRole(
        LanRole $role,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): LanRole;

    public function createGlobalRole(
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): GlobalRole;

    public function editGlobalRole(
        GlobalRole $role,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): GlobalRole;

    public function linkPermissionIdLanRole(string $permissionId, LanRole $role): void;

    public function linkLanRoleUser(LanRole $role, Authenticatable $user): void;

    public function linkPermissionIdGlobalRole(string $permissionId, GlobalRole $role): void;

    public function linkGlobalRoleUser(GlobalRole $role, Authenticatable $user): void;

    public function getAdminPermissions(Lan $lan, Authenticatable $user): Collection;

    public function findLanRoleById(int $id): ?LanRole;

    public function findGlobalRoleById(int $id): ?GlobalRole;

    public function getLanRoles(int $lanId): Collection;

    public function getGlobalRoles(): Collection;

    public function getGlobalRolePermissions(int $roleId): Collection;

    public function getLanRolePermissions(int $roleId): Collection;

    public function getPermissions(): Collection;

    public function getLanUserRoles(int $roleId): Collection;

    public function getGlobalUserRoles(int $roleId): Collection;

    public function deleteLanRole(int $roleId): void;

    public function deleteGlobalRole(int $roleId): void;

    public function createDefaultLanRoles(int $lanId): void;

    public function userHasPermission(string $permission, int $userId, int $lanId): bool;
}