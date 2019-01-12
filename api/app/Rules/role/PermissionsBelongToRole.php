<?php

namespace App\Rules\Role;

use App\Model\GlobalRole;
use App\Model\LanRole;
use App\Model\PermissionGlobalRole;
use App\Model\PermissionLanRole;
use Illuminate\Contracts\Validation\Rule;

class PermissionsBelongToRole implements Rule
{
    protected $roleId;

    /**
     * PermissionsDontBelongToGlobalRole constructor.
     * @param $roleId
     */
    public function __construct($roleId)
    {
        $this->roleId = $roleId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (is_null($value) || !is_array($value) || is_null($this->roleId)) {
            return true;
        }

        $lanRole = LanRole::find($this->roleId);
        $globalRole = GlobalRole::find($this->roleId);

        if (!is_null($lanRole)) {
            foreach ($value as $permissionId) {
                $permission = PermissionLanRole::where('permission_id', $permissionId)
                    ->where('role_id', $lanRole->id)
                    ->get()
                    ->first();
                if (is_null($permission)) {
                    return false;
                }
            }
        } else if (!is_null($globalRole)) {
            foreach ($value as $permissionId) {
                $permission = PermissionGlobalRole::where('permission_id', $permissionId)
                    ->where('role_id', $globalRole->id)
                    ->get()
                    ->first();
                if (is_null($permission)) {
                    return false;
                }
            }
        } else {
            return true;
        }

        return true;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.permissions_belong_to_user');
    }
}