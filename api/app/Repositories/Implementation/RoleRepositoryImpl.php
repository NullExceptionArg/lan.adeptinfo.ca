<?php

namespace App\Repositories\Implementation;


use App\Model\Lan;
use App\Model\LanRole;
use App\Repositories\RoleRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RoleRepositoryImpl implements RoleRepository
{
    public function createLanRole(
        int $lanId,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): LanRole
    {
        $role = new LanRole();
        $role->lan_id = $lanId;
        $role->name = $name;
        $role->en_display_name = $enDisplayName;
        $role->en_description = $enDescription;
        $role->fr_display_name = $frDisplayName;
        $role->fr_description = $frDescription;
        $role->save();

        return $role;
    }

    public function linkPermissionIdLanRole(string $permissionId, LanRole $role): void
    {
        DB::table('permission_lan_role')
            ->insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id
            ]);
    }

    public function getAdminPermissions(Lan $lan, Authenticatable $user): Collection
    {
        return DB::table('role')
            ->join('permission_lan_role', 'role.id', '=', 'permission_lan_role.role_id')
            ->join('permission', 'permission_lan_role.permission_id', '=', 'permission.id')
            ->join('lan_role_user', 'role.id', '=', 'lan_role_user.role_id')
            ->where('role.lan_id', $lan->id)
            ->where('lan_role_user.user_id', $user->id)
            ->select('permission.id', 'permission.name')
            ->get();

    }
}