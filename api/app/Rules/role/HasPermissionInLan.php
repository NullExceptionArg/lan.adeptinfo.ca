<?php

namespace App\Rules\Role;

use App\Model\LanRole;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class HasPermissionInLan implements Rule
{

    protected $roleId;
    protected $userId;

    public function __construct(?string $roleId, string $userId)
    {
        $this->roleId = $roleId;
        $this->userId = $userId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $value permissions name
     * @return bool
     * @throws AuthorizationException
     */
    public function passes($attribute, $value): bool
    {
        if (is_null($value) || is_null(LanRole::find($this->roleId)) || is_null($this->userId)) {
            return true;
        }

        $lanRole = LanRole::find($this->roleId);
        $lanPermissions = DB::table('permission')
            ->join('permission_lan_role', 'permission.id', '=', 'permission_lan_role.permission_id')
            ->join('lan_role', 'permission_lan_role.role_id', '=', 'lan_role.id')
            ->join('lan', 'lan_role.lan_id', '=', 'lan.id')
            ->join('lan_role_user', 'lan_role.id', '=', 'lan_role_user.role_id')
            ->where('lan_role.lan_id', $lanRole->lan_id)
            ->where('lan_role_user.user_id', $this->userId)
            ->where('permission.name', $value)
            ->get();

        $globalPermissions = DB::table('permission')
            ->join('permission_global_role', 'permission.id', '=', 'permission_global_role.permission_id')
            ->join('global_role', 'permission_global_role.role_id', '=', 'global_role.id')
            ->join('global_role_user', 'global_role.id', '=', 'global_role_user.role_id')
            ->where('global_role_user.user_id', $this->userId)
            ->where('permission.name', $value)
            ->get();

        $hasPermission = $lanPermissions->merge($globalPermissions)->unique()->count() > 0;
        if (!$hasPermission) {
            throw new AuthorizationException(trans('validation.forbidden'));
        }

        return $hasPermission;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.has_permission');
    }
}