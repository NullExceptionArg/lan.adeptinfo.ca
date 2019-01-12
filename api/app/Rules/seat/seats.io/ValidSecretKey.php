<?php

namespace App\Rules\Seat;

use Illuminate\Contracts\Validation\Rule;
use Seatsio\SeatsioClient;
use Seatsio\SeatsioException;

class ValidSecretKey implements Rule
{
    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $seatsClient = new SeatsioClient($value);
        try {
            $seatsClient->charts->listAllTags();
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
        return trans('validation.valid_secret_key');
    }
}
