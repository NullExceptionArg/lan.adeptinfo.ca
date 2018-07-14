<?php

namespace App\Rules;

use App\Model\Reservation;
use App\Model\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Validation\Rule;

class UserOncePerLan implements Rule
{
    protected $user;
    protected $email;

    /**
     * SeatOncePerLan constructor.
     * @param Authenticatable $user
     * @param string $email
     */
    public function __construct(?Authenticatable $user, ?string $email)
    {
        $this->user = $user;
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
        if ($this->user == null) {
            if ($this->email == null) {
                return true;
            }
            $this->user = User::where('email', $this->email)->first();
            if ($this->user == null) {
                return true;
            }
        }

        $lanUserReservation = Reservation::where('user_id', $this->user->id)
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