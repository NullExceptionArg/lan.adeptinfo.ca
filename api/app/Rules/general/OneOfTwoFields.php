<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class OneOfTwoFields implements Rule
{
    protected $secondField;
    protected $secondFieldName;

    public function __construct(?string $secondField, string $secondFieldName)
    {
        $this->secondField = $secondField;
        $this->secondFieldName = $secondFieldName;
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
        if($value != null && $this->secondField != null){
            return false;
        } else {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.one_of_two_fields', ['value' => ':attribute', 'second_field' => $this->secondFieldName]);
    }
}
