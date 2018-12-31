<?php

namespace App\Services;


use App\Http\Resources\Role\GetRoleResource;
use App\Model\GlobalRole;
use App\Model\LanRole;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;

interface RoleService
{
    public function addPermissionsGlobalRole(int $roleId, array $permissions): GetRoleResource;

    public function addPermissionsLanRole(int $roleId, array $permissions): GetRoleResource;

    public function assignGlobalRole(int $roleId, string $email): GetRoleResource;

    public function assignLanRole(int $roleId, string $email): GetRoleResource;

    public function createGlobalRole(
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription,
        array $permissions
    ): GlobalRole;

    public function createLanRole(
        int $lanId,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription,
        array $permissions
    ): LanRole;

    public function deleteGlobalRole(int $roleId): GetRoleResource;

    public function deleteLanRole(int $roleId): GetRoleResource;

    public function deletePermissionsGlobalRole(int $roleId, array $permissions): GetRoleResource;

    public function deletePermissionsLanRole(int $roleId, array $permissions): GetRoleResource;

    public function getGlobalRolePermissions(int $roleId): AnonymousResourceCollection;

    public function getGlobalRoles(): AnonymousResourceCollection;

    public function getLanRolePermissions(int $roleId): AnonymousResourceCollection;

    public function getLanRoles(int $lanId): AnonymousResourceCollection;

    public function getLanUsers(int $roleId): Collection;

    public function getPermissions(): AnonymousResourceCollection;

    public function getRoleUsers(int $roleId): Collection;

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
