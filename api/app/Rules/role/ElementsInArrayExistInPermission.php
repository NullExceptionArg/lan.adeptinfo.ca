<?php

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ElementsInArrayExistInPermission implements Rule
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
            if (is_null(DB::table('permission')->find($permissionId))) return false;
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
        return trans('validation.elements_in_array_exist_in_permission');
    }
}