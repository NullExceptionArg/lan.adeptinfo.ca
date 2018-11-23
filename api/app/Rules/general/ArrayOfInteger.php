<?php

namespace App\Rules;


use Illuminate\Contracts\Validation\Rule;

class ArrayOfInteger implements Rule
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

        foreach ($value as $v) {
            if (!is_int($v)) return false;
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
        return trans('validation.array_of_integer');
    }
}