<?php

namespace App\Rules\General;

use Illuminate\Contracts\Validation\Rule;

class ArrayOfInteger implements Rule
{

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
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
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.array_of_integer');
    }
}
