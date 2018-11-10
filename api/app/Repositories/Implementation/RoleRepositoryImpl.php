<?php

namespace App\Repositories\Implementation;


use App\Model\GlobalRole;
use App\Model\Lan;
use App\Model\LanRole;
use App\Model\PermissionGlobalRole;
use App\Model\PermissionLanRole;
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

    public function editLanRole(
        LanRole $role,
        ?string $name,
        ?string $enDisplayName,
        ?string $enDescription,
        ?string $frDisplayName,
        ?string $frDescription
    ): LanRole
    {
        $role->name = $name != null ? $name : $role->name;
        $role->en_display_name = $enDisplayName != null ? $enDisplayName : $role->en_display_name;
        $role->en_description = $enDescription != null ? $enDescription : $role->en_description;
        $role->fr_display_name = $frDisplayName != null ? $frDisplayName : $role->fr_display_name;
        $role->fr_description = $frDescription != null ? $frDescription : $role->fr_description;
        $role->save();

        return $role;
    }

    public function createGlobalRole(
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): GlobalRole
    {
        $role = new GlobalRole();
        $role->name = $name;
        $role->en_display_name = $enDisplayName;
        $role->en_description = $enDescription;
        $role->fr_display_name = $frDisplayName;
        $role->fr_description = $frDescription;
        $role->save();

        return $role;
    }

    public function editGlobalRole(
        GlobalRole $role,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): GlobalRole
    {
        $role->name = $name != null ? $name : $role->name;
        $role->en_display_name = $enDisplayName != null ? $enDisplayName : $role->en_display_name;
        $role->en_description = $enDescription != null ? $enDescription : $role->en_description;
        $role->fr_display_name = $frDisplayName != null ? $frDisplayName : $role->fr_display_name;
        $role->fr_description = $frDescription != null ? $frDescription : $role->fr_description;
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

    public function linkLanRoleUser(LanRole $role, Authenticatable $user): void
    {
        DB::table('lan_role_user')
            ->insert([
                'user_id' => $user->id,
                'role_id' => $role->id
            ]);
    }

    public function unlinkPermissionsFromLanRole(LanRole $role): void
    {
        PermissionLanRole::where('role_id', $role->id)
            ->delete();
    }

    public function linkPermissionIdGlobalRole(string $permissionId, GlobalRole $role): void
    {
        DB::table('permission_global_role')
            ->insert([
                'permission_id' => $permissionId,
                'role_id' => $role->id
            ]);
    }

    public function linkGlobalRoleUser(GlobalRole $role, Authenticatable $user): void
    {
        DB::table('global_role_user')
            ->insert([
                'user_id' => $user->id,
                'role_id' => $role->id
            ]);
    }

    public function unlinkPermissionsFromGlobalRole(GlobalRole $role): void
    {
        PermissionGlobalRole::where('role_id', $role->id)
            ->delete();
    }

    public function findLanRoleById(int $id): ?LanRole
    {
        return LanRole::find($id);
    }

    public function findGlobalRoleById(int $id): ?GlobalRole
    {
        return GlobalRole::find($id);
    }

    public function getAdminPermissions(Lan $lan, Authenticatable $user): Collection
    {
        $lanPermissions = DB::table('permission')
            ->join('permission_lan_role', 'permission.id', '=', 'permission_lan_role.permission_id')
            ->join('lan_role', 'permission_lan_role.role_id', '=', 'lan_role.id')
            ->join('lan', 'lan_role.lan_id', '=', 'lan.id')
            ->join('lan_role_user', 'lan_role.id', '=', 'lan_role_user.role_id')
            ->where('lan_role.lan_id', $lan->id)
            ->where('lan_role_user.user_id', $user->id)
            ->select('permission.id', 'permission.name')
            ->get();

        $globalPermissions = DB::table('permission')
            ->join('permission_global_role', 'permission.id', '=', 'permission_global_role.permission_id')
            ->join('global_role', 'permission_global_role.role_id', '=', 'global_role.id')
            ->join('global_role_user', 'global_role.id', '=', 'global_role_user.role_id')
            ->where('global_role_user.user_id', $user->id)
            ->select('permission.id', 'permission.name')
            ->get();

        return $lanPermissions->merge($globalPermissions)->unique();
    }
}