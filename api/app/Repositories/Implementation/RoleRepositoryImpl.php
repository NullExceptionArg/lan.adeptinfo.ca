<?php

namespace App\Repositories\Implementation;

use App\Model\{GlobalRole, LanRole, Permission};
use App\Repositories\RoleRepository;
use Illuminate\{Support\Collection, Support\Facades\DB};

class RoleRepositoryImpl implements RoleRepository
{
    public function addDefaultLanRoles(int $lanId): void
    {
        $lanRoles = (include(base_path() . '/resources/roles.php'))['lan_roles'];
        foreach ($lanRoles as $role) {
            $roleId = DB::table('lan_role')->insertGetId([
                'name' => $role['name'],
                'en_display_name' => $role['en_display_name'],
                'en_description' => $role['en_description'],
                'fr_display_name' => $role['fr_display_name'],
                'fr_description' => $role['fr_description'],
                'lan_id' => $lanId
            ]);
            foreach ($role['permissions'] as $permission) {
                DB::table('permission_lan_role')->insert([
                    'permission_id' => Permission::where('name', $permission['name'])->first()->id,
                    'role_id' => $roleId
                ]);
            }
        }
    }

    public function createGlobalRole(
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): int
    {
        return DB::table('global_role')
            ->insertGetId([
                'name' => $name,
                'en_display_name' => $enDisplayName,
                'en_description' => $enDescription,
                'fr_display_name' => $frDisplayName,
                'fr_description' => $frDescription
            ]);
    }

    public function createLanRole(
        int $lanId,
        string $name,
        string $enDisplayName,
        string $enDescription,
        string $frDisplayName,
        string $frDescription
    ): int
    {
        return DB::table('lan_role')
            ->insertGetId([
                'lan_id' => $lanId,
                'name' => $name,
                'en_display_name' => $enDisplayName,
                'en_description' => $enDescription,
                'fr_display_name' => $frDisplayName,
                'fr_description' => $frDescription
            ]);
    }

    public function deleteGlobalRole(int $roleId): void
    {
        GlobalRole::destroy($roleId);
    }

    public function deleteLanRole(int $roleId): void
    {
        LanRole::destroy($roleId);
    }

    public function findGlobalRoleById(int $id): ?GlobalRole
    {
        return GlobalRole::find($id);
    }

    public function findLanRoleById(int $id): ?LanRole
    {
        return LanRole::find($id);
    }

    public function getAdminPermissions(int $lanId, int $userId): Collection
    {
        $lanPermissions = DB::table('permission')
            ->join('permission_lan_role', 'permission.id', '=', 'permission_lan_role.permission_id')
            ->join('lan_role', 'permission_lan_role.role_id', '=', 'lan_role.id')
            ->join('lan', 'lan_role.lan_id', '=', 'lan.id')
            ->join('lan_role_user', 'lan_role.id', '=', 'lan_role_user.role_id')
            ->where('lan_role.lan_id', $lanId)
            ->where('lan_role_user.user_id', $userId)
            ->select('permission.id', 'permission.name', 'permission.can_be_per_lan')
            ->get();

        $globalPermissions = DB::table('permission')
            ->join('permission_global_role', 'permission.id', '=', 'permission_global_role.permission_id')
            ->join('global_role', 'permission_global_role.role_id', '=', 'global_role.id')
            ->join('global_role_user', 'global_role.id', '=', 'global_role_user.role_id')
            ->where('global_role_user.user_id', $userId)
            ->select('permission.id', 'permission.name', 'permission.can_be_per_lan')
            ->get();

        return $lanPermissions->merge($globalPermissions)->unique();
    }

    public function getGlobalRolePermissions(int $roleId): Collection
    {
        return DB::table('permission_global_role')
            ->join('permission', 'permission_global_role.permission_id', '=', 'permission.id')
            ->where('permission_global_role.role_id', $roleId)
            ->select([
                'permission.id',
                'permission.name',
                'permission.can_be_per_lan'
            ])
            ->get();
    }

    public function getGlobalRoles(): Collection
    {
        return GlobalRole::get();
    }

    public function getGlobalUserRoles(int $roleId): Collection
    {
        return DB::table('global_role_user')
            ->join('user', 'global_role_user.user_id', '=', 'user.id')
            ->where('global_role_user.role_id', $roleId)
            ->select('user.email', 'user.first_name', 'user.last_name')
            ->get();
    }

    public function getLanRolePermissions(int $roleId): Collection
    {
        return DB::table('permission_lan_role')
            ->join('permission', 'permission_lan_role.permission_id', '=', 'permission.id')
            ->where('permission_lan_role.role_id', $roleId)
            ->select([
                'permission.id',
                'permission.name',
                'permission.can_be_per_lan',
            ])
            ->get();
    }

    public function getLanRoles(int $lanId): Collection
    {
        return LanRole::where('lan_id', $lanId)
            ->get();
    }

    public function getLanUserRoles(int $roleId): Collection
    {
        return DB::table('lan_role_user')
            ->join('user', 'lan_role_user.user_id', '=', 'user.id')
            ->where('lan_role_user.role_id', $roleId)
            ->select('user.email', 'user.first_name', 'user.last_name')
            ->get();
    }

    public function getPermissions(): Collection
    {
        return Permission::all();
    }

    public function getUsersGlobalRoles(string $email): Collection
    {
        $globalRoles = DB::table('user')
            ->join('global_role_user', 'user.id', '=', 'global_role_user.user_id')
            ->join('global_role', 'global_role_user.role_id', '=', 'global_role.id')
            ->where('user.email', $email)
            ->select(
                'global_role.id',
                'global_role.name',
                'global_role.en_display_name',
                'global_role.fr_display_name',
                'global_role.en_description',
                'global_role.fr_description'
            )
            ->get();

        return $globalRoles;
    }

    public function getUsersLanRoles(string $email, int $lanId): Collection
    {
        $lanRoles = DB::table('user')
            ->join('lan_role_user', 'user.id', '=', 'lan_role_user.user_id')
            ->join('lan_role', 'lan_role_user.role_id', '=', 'lan_role.id')
            ->where('user.email', $email)
            ->where('lan_role.lan_id', $lanId)
            ->select(
                'lan_role.id',
                'lan_role.name',
                'lan_role.en_display_name',
                'lan_role.fr_display_name',
                'lan_role.en_description',
                'lan_role.fr_description'
            )
            ->get();

        return $lanRoles;
    }

    public function linkGlobalRoleUser(int $roleId, int $userId): void
    {
        DB::table('global_role_user')
            ->insert([
                'user_id' => $userId,
                'role_id' => $roleId
            ]);
    }

    public function linkLanRoleUser(int $roleId, int $userId): void
    {
        DB::table('lan_role_user')
            ->insert([
                'user_id' => $userId,
                'role_id' => $roleId
            ]);
    }

    public function linkPermissionIdGlobalRole(string $permissionId, int $roleId): void
    {
        DB::table('permission_global_role')
            ->insert([
                'permission_id' => $permissionId,
                'role_id' => $roleId
            ]);
    }

    public function linkPermissionIdLanRole(string $permissionId, int $roleId): void
    {
        DB::table('permission_lan_role')
            ->insert([
                'permission_id' => $permissionId,
                'role_id' => $roleId
            ]);
    }

    public function unlinkPermissionIdGlobalRole(int $permissionId, int $roleId): void
    {
        DB::table('permission_global_role')
            ->where('permission_id', $permissionId)
            ->where('role_id', $roleId)
            ->delete();
    }

    public function unlinkPermissionIdLanRole(int $permissionId, int $roleId): void
    {
        DB::table('permission_lan_role')
            ->where('permission_id', $permissionId)
            ->where('role_id', $roleId)
            ->delete();
    }

    public function updateGlobalRole(
        int $roleId,
        ?string $name,
        ?string $enDisplayName,
        ?string $enDescription,
        ?string $frDisplayName,
        ?string $frDescription
    ): void
    {
        $role = $this->findGlobalRoleById($roleId);
        DB::table('global_role')
            ->where('id', $roleId)
            ->update([
                'name' => $name != null ? $name : $role->name,
                'en_display_name' => $enDisplayName != null ? $enDisplayName : $role->en_display_name,
                'en_description' => $enDescription != null ? $enDescription : $role->en_description,
                'fr_display_name' => $frDisplayName != null ? $frDisplayName : $role->fr_display_name,
                'fr_description' => $frDescription != null ? $frDescription : $role->fr_description
            ]);
    }

    public function updateLanRole(
        int $roleId,
        ?string $name,
        ?string $enDisplayName,
        ?string $enDescription,
        ?string $frDisplayName,
        ?string $frDescription
    ): void
    {
        $role = $this->findGlobalRoleById($roleId);

        DB::table('lan_role')
            ->where('id', $roleId)
            ->update([
                'name' => $name != null ? $name : $role->name,
                'en_display_name' => $enDisplayName != null ? $enDisplayName : $role->en_display_name,
                'en_description' => $enDescription != null ? $enDescription : $role->en_description,
                'fr_display_name' => $frDisplayName != null ? $frDisplayName : $role->fr_display_name,
                'fr_description' => $frDescription != null ? $frDescription : $role->fr_description
            ]);
    }

    public function userHasPermission(string $permission, int $userId, int $lanId): bool
    {
        $lanPermissions = DB::table('permission')
            ->join('permission_lan_role', 'permission.id', '=', 'permission_lan_role.permission_id')
            ->join('lan_role', 'permission_lan_role.role_id', '=', 'lan_role.id')
            ->join('lan', 'lan_role.lan_id', '=', 'lan.id')
            ->join('lan_role_user', 'lan_role.id', '=', 'lan_role_user.role_id')
            ->where('lan_role.lan_id', $lanId)
            ->where('lan_role_user.user_id', $userId)
            ->where('permission.name', $permission)
            ->get();

        $globalPermissions = DB::table('permission')
            ->join('permission_global_role', 'permission.id', '=', 'permission_global_role.permission_id')
            ->join('global_role', 'permission_global_role.role_id', '=', 'global_role.id')
            ->join('global_role_user', 'global_role.id', '=', 'global_role_user.role_id')
            ->where('global_role_user.user_id', $userId)
            ->where('permission.name', $permission)
            ->get();

        return $lanPermissions->merge($globalPermissions)->unique()->count() > 0;
    }
}
