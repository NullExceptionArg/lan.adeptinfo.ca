<?php

namespace App\Rules;


use Exception;
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
        $client = new Google_Client(['client_id' => env('GOOGLE_TEST_CLIENT_ID')]);
        try {
            $client->verifyIdToken($value);
        } catch (Exception $e) {
            return false;
        }
        return true;
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
