<?php

namespace App\Rules\Lan;

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
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $placeCount = Reservation::where('lan_id', $this->lanId)->count();
        return $placeCount <= $value;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.lower_reserved_place');
    }
}