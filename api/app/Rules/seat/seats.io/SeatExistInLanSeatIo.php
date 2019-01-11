<?php

namespace App\Rules\Seat;


use App\Model\Lan;
use Illuminate\Contracts\Validation\Rule;
use Seatsio\SeatsioClient;
use Seatsio\SeatsioException;

class SeatExistInLanSeatIo implements Rule
{
    protected $lanId;

    /**
     * SeatOncePerLan constructor.
     * @param string|null $lanId
     */
    public function __construct(?string $lanId)
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
        $lan = Lan::find($this->lanId);
        if ($lan == null) {
            return true;
        }
        $seatsClient = new SeatsioClient($lan->secret_key);
        try {
            $seatsClient->events->retrieveObjectStatus($lan->event_key, $value);
        } catch (SeatsioException $exception) {
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
        return trans('validation.seat_exist_in_lan_seat_io');
    }
}