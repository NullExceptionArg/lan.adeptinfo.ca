<?php

namespace App\Rules;


use Google_Client;
use Illuminate\Contracts\Validation\Rule;

class ValidGoogleToken implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $client = new Google_Client();
        $client->setApplicationName('LAN de l\'ADEPT');
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $google_result = $client->verifyIdToken($value);
        if (!$google_result) {
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
        return trans('validation.valid_google_token');
    }
}