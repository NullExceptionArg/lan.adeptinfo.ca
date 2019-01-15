<?php

namespace App\Rules;

use App\Model\{LanRoleUser, User};
use Illuminate\Contracts\Validation\Rule;

/**
 * Un rôle de LAN n'est attribué qu'une seul fois à un utilisateur.
 *
 * Class LanRoleOncePerUser
 * @package App\Rules
 */
class LanRoleOncePerUser implements Rule
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
     * @param  mixed $roleId Id du rôle
     * @return bool
     */
    public function passes($attribute, $roleId): bool
    {
        $user = User::where('email', $this->email)->first();

        /*
         * Condition de garde :
         * L'id du rôle n'est pas nul
         * Un utilisateur existe pour le courriel
         */
        if (is_null($roleId) || is_null($user)) {
            return true; // Une autre validation devrait échouer
        }

        $lanRoleUser = LanRoleUser::where('role_id', $roleId)
            ->where('user_id', $user->id)->first();

        // Si aucun lien entre le rôle de LAN et l'utilisateur n'a été trouvé
        // Si le nombre de lien entre le rôle de LAN est l'utilisateur est 0
        return is_null($lanRoleUser) || $lanRoleUser->count() == 0;
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