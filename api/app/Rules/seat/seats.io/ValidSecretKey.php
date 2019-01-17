<?php

namespace App\Rules\Seat;

use Illuminate\Contracts\Validation\Rule;
use Seatsio\{SeatsioClient, SeatsioException};

/**
 * Une clé secrète seats.io est valide pour un certain LAN.
 *
 * Class ValidSecretKey
 * @package App\Rules\Seat
 */
class ValidSecretKey implements Rule
{
    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $secretKey
     * @return bool
     */
    public function passes($attribute, $secretKey): bool
    {
        $seatsClient = new SeatsioClient($secretKey);
        try {
            // Tenter d'utiliser n'importe quelle méthode qui ne nécessite par de paramètre
            $seatsClient->charts->listAllTags();
        } catch (SeatsioException $exception) {
            // Une erreur est lancée si la clé n'est pas valide
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
