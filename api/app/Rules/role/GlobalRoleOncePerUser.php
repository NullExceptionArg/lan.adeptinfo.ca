<?php

namespace App\Rules;


use App\Model\GlobalRoleUser;
use App\Model\User;
use Illuminate\Contracts\Validation\Rule;

class GlobalRoleOncePerUser implements Rule
{
    protected $email;

    /**
     * SeatOncePerLan constructor.
     * @param string $email
     */
    public function __construct(?string $email)
    {
        $this->email = $email;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (is_null($value) || is_null($this->email)) {
            return true;
        }

        $user = User::where('email', $this->email)->first();
        $globalRoleUser = GlobalRoleUser::where('role_id', $value)
            ->where('user_id', $user->id)->first();
        return $globalRoleUser == null || $globalRoleUser->count() == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.role_once_per_user');
    }
}