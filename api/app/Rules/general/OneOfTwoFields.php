<?php

namespace App\Rules\General;

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
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if($value != null && $this->secondField != null){
            return false;
        } else {
            return true;
        }
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.one_of_two_fields', ['value' => ':attribute', 'second_field' => $this->secondFieldName]);
    }
}
