<?php

namespace App\Repositories\Implementation;


use App\Model\Lan;
use App\Model\Role;
use App\Repositories\RoleRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
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

    public function getAdminPermissions(Lan $lan, Authenticatable $user): Collection
    {
        return DB::table('role')
            ->join('permission_role', 'role.id', '=', 'permission_role.role_id')
            ->join('permission', 'permission_role.permission_id', '=', 'permission.id')
            ->join('role_user', 'role.id', '=', 'role_user.role_id')
            ->where('role.lan_id', $lan->id)
            ->where('role_user.user_id', $user->id)
            ->select('permission.id', 'permission.name')
            ->get();

    }
}