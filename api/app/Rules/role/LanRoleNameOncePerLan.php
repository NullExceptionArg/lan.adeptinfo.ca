<?php

namespace App\Rules\Role;

use App\Model\LanRole;
use Illuminate\Contracts\Validation\Rule;

class LanRoleNameOncePerLan implements Rule
{
    protected $lanId;

    /**
     * SeatOncePerLan constructor.
     * @param string $lanId
     */
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
        if (is_null($this->lanId)) {
            return true;
        }

        $lanSeatReservation = LanRole::where('lan_id', $this->lanId)
            ->where('name', $value)->first();
        return $lanSeatReservation == null || $lanSeatReservation->count() == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.lan_role_name_once_per_lan');
    }
}