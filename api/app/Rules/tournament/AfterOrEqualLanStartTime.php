<?php

namespace App\Rules\Tournament;

use App\Model\Lan;
use Illuminate\Contracts\Validation\Rule;

class AfterOrEqualLanStartTime implements Rule
{
    protected $lanId;

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
        $lan = Lan::find($this->lanId);
        if ($lan == null) {
            return true; // Une autre validation devrait échouer
        }
        return $value >= $lan->lan_start;
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
