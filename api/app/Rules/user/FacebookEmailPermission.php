<?php

namespace App\Rules;

use App\Utils\FacebookUtils;
use Facebook\Exceptions\FacebookSDKException;
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
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.facebook_email_permission');
    }
}
