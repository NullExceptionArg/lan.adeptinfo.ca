<?php

namespace App\Rules\Role;

use App\Model\LanRole;
use App\Model\PermissionLanRole;
use Illuminate\Contracts\Validation\Rule;

class PermissionsDontBelongToLanRole implements Rule
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
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $lanRole = LanRole::find($this->roleId);

        if (is_null($value) || !is_array($value) || is_null($lanRole)) {
            return true;
        }

        foreach ($value as $permissionId) {
            $permission = PermissionLanRole::where('permission_id', $permissionId)
                ->where('role_id', $lanRole->id)
                ->get()
                ->first();
            if (!is_null($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.permissions_dont_belong_to_user');
    }
}
