<?php

namespace App\Repositories;


use App\Model\Lan;
use App\Model\Role;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

interface RoleRepository
{
    public function create(
        int $lanId,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): Role;

    public function linkPermissionIdRole(string $permissionId, Role $role): void;

    public function getAdminPermissions(Lan $lan, Authenticatable $user): Collection;
}