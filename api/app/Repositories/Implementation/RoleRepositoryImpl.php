<?php

namespace App\Repositories\Implementation;


use App\Model\Role;
use App\Repositories\RoleRepository;
use Illuminate\Support\Facades\DB;

class RoleRepositoryImpl implements RoleRepository
{
    public function create(
        int $lanId,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): Role
    {
        $role = new Role();
        $role->lan_id = $lanId;
        $role->name = $name;
        $role->en_display_name = $enDisplayName;
        $role->en_description = $enDescription;
        $role->fr_display_name = $frDisplayName;
        $role->fr_description = $frDescription;
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