<?php

namespace App\Rules;

use App\Model\Reservation;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserOncePerLan implements Rule
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
        $user = Auth::user();
        $lanUserReservation = Reservation::where('user_id', $user->id)
            ->where('lan_id', $value)->first();

        if ($lanUserReservation != null && $lanUserReservation->count() > 0) {
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
        return trans('validation.user_once_per_lan');
    }
}