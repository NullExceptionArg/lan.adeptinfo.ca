<?php

namespace App\Rules;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Validation\Rule;

class ValidFacebookToken implements Rule
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
        try {
            $client = new Client([
                'base_uri' => 'https://graph.facebook.com',
                'timeout' => 2.0]);
            \GuzzleHttp\json_decode($client->get('/me', ['query' => [
                'fields' => 'id,first_name,last_name,email',
                'access_token' => $value
            ]])->getBody());
        } catch (RequestException $e) {
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
        return trans('validation.valid_facebook_token');
    }
}