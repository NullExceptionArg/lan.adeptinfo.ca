<?php

namespace App\Rules;


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
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (is_null($value) || !is_array($value) || is_null($this->roleId)) {
            return true;
        }

        $globalRole = GlobalRole::find($this->roleId);

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
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.permissions_dont_belong_to_user');
    }
}