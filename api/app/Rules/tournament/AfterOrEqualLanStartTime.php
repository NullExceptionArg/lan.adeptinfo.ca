<?php

namespace App\Rules\Tournament;

use App\Model\Lan;
use Illuminate\Contracts\Validation\Rule;

/**
 * Une date et heure sont après ou au même moment que le début d'un LAN.
 *
 * Class AfterOrEqualLanStartTime
 * @package App\Rules\Tournament
 */
class AfterOrEqualLanStartTime implements Rule
{
    protected $lanId;

    /**
     * AfterOrEqualLanStartTime constructor.
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
         * Conditions de contribution :
         * L'id du LAN correspond à un LAN
         */
        if (is_null($lan)) {
            return true; // Une autre validation devrait échouer
        }

        return $dateTime >= $lan->lan_start;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.after_or_equal_lan_start_time');
    }
}
