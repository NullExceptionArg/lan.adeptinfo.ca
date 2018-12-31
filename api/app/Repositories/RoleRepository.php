<?php

namespace App\Repositories;

use App\Model\GlobalRole;
use App\Model\Lan;
use App\Model\LanRole;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

interface RoleRepository
{
    public function createDefaultLanRoles(int $lanId): void;

    public function createGlobalRole(
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): int;

    public function createLanRole(
        int $lanId,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): int;

    public function deleteGlobalRole(int $roleId): void;

    public function deleteLanRole(int $roleId): void;

    public function findGlobalRoleById(int $id): ?GlobalRole;

    public function findLanRoleById(int $id): ?LanRole;

    public function getAdminPermissions(Lan $lan, Authenticatable $user): Collection;

    public function getGlobalRolePermissions(int $roleId): Collection;

    public function getGlobalRoles(): Collection;

    public function getGlobalUserRoles(int $roleId): Collection;

    public function getLanRolePermissions(int $roleId): Collection;

    public function getLanRoles(int $lanId): Collection;

    public function getLanUserRoles(int $roleId): Collection;

    public function getPermissions(): Collection;

    public function getUsersGlobalRoles(string $email): Collection;

    public function getUsersLanRoles(string $email, int $lanId): Collection;

    public function linkGlobalRoleUser(int $roleId, int $userId): void;

    public function linkLanRoleUser(int $roleId, int $userId): void;

    public function linkPermissionIdGlobalRole(string $permissionId, int $roleId): void;

    public function linkPermissionIdLanRole(string $permissionId, int $roleId): void;

    public function unlinkPermissionIdGlobalRole(int $permissionId, int $roleId): void;

    public function unlinkPermissionIdLanRole(int $permissionId, int $roleId): void;

    public function updateGlobalRole(
        int $roleId,
        ?string $name,
        ?string $enDisplayName,
        ?string $enDescription,
        ?string $frDisplayName,
        ?string $frDescription
    ): void;

    public function updateLanRole(
        int $roleId,
        ?string $name,
        ?string $enDisplayName,
        ?string $enDescription,
        ?string $frDisplayName,
        ?string $frDescription
    ): void;

    public function userHasPermission(string $permission, int $userId, int $lanId): bool;
}