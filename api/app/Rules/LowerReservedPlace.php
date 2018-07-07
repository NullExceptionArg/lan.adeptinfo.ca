<?php

namespace App\Rules;

use App\Model\Reservation;
use Illuminate\Contracts\Validation\Rule;

class LowerReservedPlace implements Rule
{

    protected $lanId;

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
        $placeCount = Reservation::where('lan_id', $this->lanId)->count();
        return $placeCount <= $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.lower_reserved_place');
    }
}