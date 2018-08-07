<?php

namespace App\Rules;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Validation\Rule;

class FacebookEmailPermission implements Rule
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
        $response = null;
        try {
            $client = new Client([
                'base_uri' => 'https://graph.facebook.com',
                'timeout' => 2.0]);
            $response = \GuzzleHttp\json_decode($client->get('/me', ['query' => [
                'fields' => 'id,first_name,last_name,email',
                'access_token' => $value
            ]])->getBody());
        } catch (RequestException $e) {
            return true;
        }
        return $response->email != null;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.facebook_email_permission');
    }
}