<?php

namespace App\Rules;

use App\Model\Lan;
use App\Model\Reservation;
use Illuminate\Contracts\Validation\Rule;

class SeatLanRelationExists implements Rule
{

    protected $lanId;
    protected $seatId;

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
        if(Lan::find($this->lanId) == null){
            return true;
        }
        $this->seatId = $value;
        return Reservation::where('lan_id', $this->lanId)
            ->where('seat_id', $value)->first() != null;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.seat_lan_relation_exists', ['seat_id' => $this->seatId, 'lan_id' => $this->lanId]);
    }
}