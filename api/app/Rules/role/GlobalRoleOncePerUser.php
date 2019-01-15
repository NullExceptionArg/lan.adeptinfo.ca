<?php

namespace App\Rules\Role;

use App\Model\{GlobalRoleUser, User};
use Illuminate\Contracts\Validation\Rule;

/**
 * Un rôle global n'est associé qu'une fois par utilisateur.
 *
 * Class GlobalRoleOncePerUser
 * @package App\Rules\Role
 */
class GlobalRoleOncePerUser implements Rule
{
    protected $email;

    /**
     * SeatOncePerLan constructor.
     * @param string $email Courriel de l'utilisateur
     */
    public function __construct(?string $email)
    {
        $this->email = $email;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed int Id du rôle global
     * @return bool
     */
    public function passes($attribute, $globalRoleId): bool
    {
        /*
         * Conditions de garde :
         * L'id du rôle global n'est pas nul
         * Un utilisateur existe pour le courriel
         */
        $user = User::where('email', $this->email)->first();
        if (is_null($globalRoleId) || is_null($user)) {
            return true; // Une autre validation devrait échouer
        }

        $globalRoleUser = GlobalRoleUser::where('role_id', $globalRoleId)
            ->where('user_id', $user->id)
            ->first();

        return is_null($globalRoleUser) || $globalRoleUser->count() == 0;
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