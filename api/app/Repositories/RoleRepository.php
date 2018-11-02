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

    public function createGlobalRole(
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): GlobalRole;

    public function linkPermissionIdLanRole(string $permissionId, LanRole $role): void;

    public function linkPermissionIdGlobalRole(string $permissionId, GlobalRole $role): void;

    public function getAdminPermissions(Lan $lan, Authenticatable $user): Collection;
}