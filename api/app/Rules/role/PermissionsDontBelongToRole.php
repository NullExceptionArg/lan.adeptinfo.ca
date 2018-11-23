<?php

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;

class PermissionsDontBelongToRole implements Rule
{
    protected $roleId;

    /**
     * PermissionsDontBelongToRole constructor.
     * @param int $roleId
     */
    public function __construct(int $roleId)
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
        if ($value == null || !is_array($value)) {
            return true;
        }


//        foreach ($value as $permissionId) {
//            if (is_nullDB::table('permission')->find($permissionId))) return true;
//            if ($this->isGlobal) {
//                if (DB::table('permission_global_role')
//                        ->where('role_id', $this->roleId)
//                        ->permission_id == $value) return false;
//            } else {
//                if (DB::table('permission_lan_role')
//                        ->where('role_id', $this->roleId)
//                        ->permission_id == $value) return false;
//            }

//        }
//        return true;
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