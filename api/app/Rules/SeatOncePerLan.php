<?php

namespace App\Rules;


use App\Model\Reservation;
use Illuminate\Contracts\Validation\Rule;

class SeatOncePerLan implements Rule
{
    protected $lanId;

    /**
     * SeatOncePerLan constructor.
     * @param string $lanId
     */
    public function __construct(string $lanId)
    {
        $this->lanId = $lanId;
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
        $lanSeatReservation = Reservation::where('lan_id', $this->lanId)
            ->where('seat_id', $value)->first();
        if ($lanSeatReservation != null && $lanSeatReservation->count() > 0) {
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
        return 'This seat is already taken for this event.';
    }
}