<?php

namespace App\Rules\Seat;

use App\Model\Lan;
use Illuminate\Contracts\Validation\Rule;
use Seatsio\{SeatsioClient, SeatsioException};

class ValidEventKey implements Rule
{
    protected $lanId;
    protected $secretKey;

    public function __construct(?string $lanId, ?string $secretKey)
    {
        $this->lanId = $lanId;
        $this->secretKey = $secretKey;
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
        $seatsClient = null;
        if ($this->secretKey == null) {
            if ($this->lanId == null || $value == null) {
                return true; // Une autre validation devrait échouer
            }
            $this->secretKey = Lan::find($this->lanId)->secret_key;
            if ($this->secretKey == null) {
                return true; // Une autre validation devrait échouer
            }
        }

        $seatsClient = new SeatsioClient($this->secretKey);
        try {
            $seatsClient->events->retrieve($value);
        } catch (SeatsioException $exception) {
            return false;
        }
        return true;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.valid_event_key');
    }
}
