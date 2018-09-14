<?php

namespace App\Repositories;


use App\Model\Role;

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
}