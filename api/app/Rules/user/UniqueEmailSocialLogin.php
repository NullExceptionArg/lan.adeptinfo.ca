<?php

namespace App\Rules\User;

use App\Model\User;
use Illuminate\Contracts\Validation\Rule;

/**
 * Valider que si le courriel est nouveau.
 * S'il est déjà utilisé, valider qu'il utilise une connexion sociale (Facebook ou Google),
 * et qu'il n'est pas en attente de confirmation.
 *
 * Class UniqueEmailSocialLogin
 * @package App\Rules\User
 */
class UniqueEmailSocialLogin implements Rule
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
        $user = User::where('email', $value)->first();

        if (is_null($user)) {
            return true; // Une autre validation devrait échouer
        } else {
            $hasSocialLogin = !is_null($user->facebook_id) || !is_null($user->google_id);
            $hasConfirmationCode = !is_null($user->confirmation_code);

            return $hasSocialLogin && !$hasConfirmationCode;
        }
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.unique_email_social_login');
    }
}