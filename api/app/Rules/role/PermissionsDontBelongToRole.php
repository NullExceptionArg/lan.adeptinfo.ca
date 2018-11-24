<?php

namespace App\Rules;


use App\Model\GlobalRole;
use App\Model\LanRole;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

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

        $lanRole = LanRole::find($this->roleId);
        $globalRole = GlobalRole::find($this->roleId);

        if (!is_null($lanRole)) {
            foreach ($value as $permissionId) {
                $permission = DB::table('permission_lan_role')
                    ->where('permission_id', $permissionId)
                    ->where('role_id', $lanRole->id);
                if (!is_null($permission)) {
                    return false;
                }
            }
        } else if (!is_null($globalRole)) {
            foreach ($value as $permissionId) {
                $permission = DB::table('permission_global_role')
                    ->where('permission_id', $permissionId)
                    ->where('role_id', $lanRole->id);
                if (!is_null($permission)) {
                    return false;
                }
            }
        } else {
            return true;
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