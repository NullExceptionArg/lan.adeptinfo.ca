<?php

namespace App\Rules\User;

use App\Utils\FacebookUtils;
use Facebook\Exceptions\FacebookSDKException;
use Illuminate\Contracts\Validation\Rule;

class FacebookEmailPermission implements Rule
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
        $response = null;
        try {
            $response = FacebookUtils::getFacebook()->get(
                '/me?fields=id,first_name,last_name,email',
                $value
            );
        } catch (FacebookSDKException $e) {
            return true;
        }
        return array_key_exists('email', $response->getDecodedBody());
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.facebook_email_permission');
    }
}
