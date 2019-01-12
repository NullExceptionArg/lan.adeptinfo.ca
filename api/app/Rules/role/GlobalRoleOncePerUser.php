<?php

namespace App\Rules\Role;

use App\Model\GlobalRoleUser;
use App\Model\User;
use Illuminate\Contracts\Validation\Rule;

class GlobalRoleOncePerUser implements Rule
{
    protected $email;

    /**
     * SeatOncePerLan constructor.
     * @param string $email
     */
    public function __construct(?string $email)
    {
        $this->email = $email;
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
        $user = User::where('email', $this->email)->first();
        if (is_null($value) || is_null($user)) {
            return true;
        }

        $globalRoleUser = GlobalRoleUser::where('role_id', $value)
            ->where('user_id', $user->id)->first();
        return $globalRoleUser == null || $globalRoleUser->count() == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.role_once_per_user');
    }
}