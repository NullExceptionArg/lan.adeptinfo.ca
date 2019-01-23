<?php

namespace App\Rules\Tournament;

use App\Model\Lan;
use Illuminate\Contracts\Validation\Rule;

/**
 * Une date et heure est avant ou en même temps que le moment de fin d'un LAN.
 *
 * Class BeforeOrEqualLanEndTime
 * @package App\Rules\Tournament
 */
class BeforeOrEqualLanEndTime implements Rule
{
    protected $lanId;

    /**
     * BeforeOrEqualLanEndTime constructor.
     * @param string|null $lanId Id du LAN
     */
    public function __construct(?string $lanId)
    {
        $this->lanId = $lanId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $dateTime Date et heure
     * @return bool
     */
    public function passes($attribute, $dateTime): bool
    {
        $lan = Lan::find($this->lanId);

        /*
         * Condition de garde :
         * L'id du LAN correspond à un LAN
         */
        if (is_null($lan)) {
            return true; // Une autre validation devrait échouer
        }

        return $dateTime <= $lan->lan_end;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.before_or_equal_lan_end_time');
    }
}
