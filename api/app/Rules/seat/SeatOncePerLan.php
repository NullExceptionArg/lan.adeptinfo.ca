<?php

namespace App\Rules\Seat;


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
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $lanSeatReservation = Reservation::where('lan_id', $this->lanId)
            ->where('seat_id', $value)->first();
        return $lanSeatReservation == null || $lanSeatReservation->count() <= 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.seat_once_per_lan');
    }
}