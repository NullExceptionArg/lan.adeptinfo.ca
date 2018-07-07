<?php

namespace App\Rules;


use App\Model\Lan;
use Illuminate\Contracts\Validation\Rule;
use Seatsio\SeatsioClient;
use Seatsio\SeatsioException;

class SeatOncePerLanSeatIo implements Rule
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
        $lan = Lan::find($this->lanId);
        if ($lan == null) {
            return true;
        }
        $seatsClient = new SeatsioClient($lan->secret_key_id);
        try {
            $status = $seatsClient->events()->retrieveObjectStatus($lan->event_key_id, $value);
            if ($status->status === 'booked' || $status->status === 'arrived') {
                return false;
            }
        } catch (SeatsioException $exception) {
            return true;
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
        return trans('validation.seat_once_per_lan_seat_io');
    }
}