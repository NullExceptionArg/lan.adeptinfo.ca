<?php

namespace App\Rules\Lan;

use App\Model\Reservation;
use Illuminate\Contracts\Validation\Rule;

/**
 * Le nombre de réservations dans un LAN est moins grand que le nombre spécifié.
 *
 * Class LowerReservedPlace
 * @package App\Rules\Lan
 */
class LowerReservedPlace implements Rule
{
    protected $lanId;

    /**
     * LowerReservedPlace constructor.
     * @param string $lanId Id du LAN
     */
    public function __construct(string $lanId)
    {
        $this->lanId = $lanId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  int $places
     * @return bool
     */
    public function passes($attribute, $places): bool
    {
        $placeCount = Reservation::where('lan_id', $this->lanId)->count();
        return $placeCount <= $places;
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