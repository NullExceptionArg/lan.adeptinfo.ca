<?php

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PermissionsCanBePerLan implements Rule
{

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

        foreach ($value as $permissionId) {
            if (!DB::table('permission')->find($permissionId)->can_be_per_lan) return false;
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
        return trans('validation.permissions_can_be_per_lan');
    }
}