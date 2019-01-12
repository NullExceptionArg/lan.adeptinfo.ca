<?php

namespace App\Rules\Seat;

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
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if(Lan::find($this->lanId) == null){
            return true;
        }
        $this->seatId = $value;
        return Reservation::where('lan_id', $this->lanId)
            ->where('seat_id', $value)->first() != null;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.seat_lan_relation_exists', ['seat_id' => $this->seatId, 'lan_id' => $this->lanId]);
    }
}