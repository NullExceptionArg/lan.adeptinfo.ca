<?php

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class HasPermission implements Rule
{

    protected $lanId;
    protected $userId;

    public function __construct(string $lanId, string $userId)
    {
        $this->lanId = $lanId;
        $this->userId = $userId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value permissions name
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (is_null($value) || is_null($this->lanId) || is_null($this->userId)) {
            return true;
        }

        return DB::table('permission')
                ->join('permission_lan_role', 'permission.id', '=', 'permission_lan_role.permission_id')
                ->join('lan_role', 'permission_lan_role.role_id', '=', 'role.id')
                ->join('lan_role_user', 'lan_role.id', '=', 'lan_role_user.role_id')
                ->where('permission.name', $value)
                ->where('role.lan_id', $this->lanId)
                ->where('lan_role_user.user_id', $this->userId)
                ->count() > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.has_permission');
    }
}