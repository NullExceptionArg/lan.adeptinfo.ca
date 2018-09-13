<?php

namespace App\Repositories\Implementation;


use App\Model\Role;
use App\Repositories\RoleRepository;
use Illuminate\Support\Facades\DB;

class RoleRepositoryImpl implements RoleRepository
{
    public function create(string $name,
                           string $enDisplayName,
                           string $enDescription,
                           string $frDisplayName,
                           string $frDescription
    ): Role
    {
        $role = new Role();
        $role->name = $name;
        $role->enDisplayName = $enDisplayName;
        $role->enDescription = $enDescription;
        $role->frDisplayName = $frDisplayName;
        $role->frDescription = $frDescription;
        $role->save();

        return $role;
    }

    public function linkPermissionIdRole(string $permissionId, Role $role): void
    {
        DB::table('permission_role')
            ->insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id
            ]);
    }
}