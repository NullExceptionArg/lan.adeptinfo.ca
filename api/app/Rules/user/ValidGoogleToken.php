<?php

namespace App\Rules\User;

use Exception;
use Google_Client;
use Illuminate\Contracts\Validation\Rule;

class ValidGoogleToken implements Rule
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
        $client = new Google_Client(['client_id' => env('GOOGLE_TEST_CLIENT_ID')]);
        try {
            $client->verifyIdToken($value);
        } catch (Exception $e) {
            return false;
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
        return trans('validation.valid_google_token');
    }
}
