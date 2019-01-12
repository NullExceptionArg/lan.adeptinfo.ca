<?php

namespace App\Rules\Role;

use App\Model\GlobalRole;
use App\Model\PermissionGlobalRole;
use Illuminate\Contracts\Validation\Rule;

class PermissionsDontBelongToGlobalRole implements Rule
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
        $globalRole = GlobalRole::find($this->roleId);

        if (is_null($value) || !is_array($value) || is_null($this->roleId) || is_null($globalRole)) {
            return true;
        }

        foreach ($value as $permissionId) {
            $permission = PermissionGlobalRole::where('permission_id', $permissionId)
                ->where('role_id', $globalRole->id)
                ->get()
                ->first();
            if (!is_null($permission)) {
                return false;
            }
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
        return trans('validation.permissions_dont_belong_to_user');
    }
}