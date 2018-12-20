<?php

namespace App\Rules;

use App\Model\User;
use Illuminate\Contracts\Validation\Rule;

class UniqueEmailSocialLogin implements Rule
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
        $user = User::where('email', $value)->first();
        $hasConfirmationCode = $user != null ? $user->confirmation_code != null : false;
        return ($user == null || ($user->facebook_id != null || $user->google_id != null)) && !$hasConfirmationCode;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.unique_email_social_login');
    }
}