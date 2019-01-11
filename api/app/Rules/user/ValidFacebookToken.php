<?php

namespace App\Rules\User;

use App\Utils\FacebookUtils;
use Facebook\Exceptions\FacebookSDKException;
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
            FacebookUtils::getFacebook()->get(
                '/me?fields=id,first_name,last_name,email',
                $value
            );
        } catch (FacebookSDKException $e) {
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
