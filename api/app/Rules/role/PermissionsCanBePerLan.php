<?php

namespace App\Rules\Role;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class PermissionsCanBePerLan implements Rule
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

        foreach ($value as $permissionId) {
            if (is_null(DB::table('permission')->find($permissionId))) return true;
            if (!DB::table('permission')->find($permissionId)->can_be_per_lan) return false;
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
        return trans('validation.permissions_can_be_per_lan');
    }
}