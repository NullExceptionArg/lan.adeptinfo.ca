<?php

namespace App\Rules\Seat;

use App\Model\Lan;
use Illuminate\Contracts\Validation\Rule;
use Seatsio\{SeatsioClient, SeatsioException};

/**
 * Une clé d'événement seats.io est valide pour un certain LAN.
 *
 * Class ValidEventKey
 * @package App\Rules\Seat
 */
class ValidEventKey implements Rule
{
    protected $lanId;
    protected $secretKey;

    /**
     * ValidEventKey constructor.
     * @param string|null $lanId Id du LAN
     * @param string|null $secretKey Clé secrète seats.io
     */
    public function __construct(?string $lanId, ?string $secretKey)
    {
        $this->lanId = $lanId;
        $this->secretKey = $secretKey;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  string $eventKey
     * @return bool
     */
    public function passes($attribute, $eventKey): bool
    {
        $lan = null;
        /*
         * Conditions de garde :
         * Si aucune clé secrète de seats.io n'a été passée, et que : (
         * La clé d'événement est non nulle
         * Un LAN correspond à l'id de LAN passé
         * Une clé secrète existe pour le LAN correspondant à l'id du LAN passé)
         */
        if (is_null($this->secretKey)) {
            if (is_null($eventKey) || is_null($lan = Lan::find($this->lanId)) || is_null($lan->secretKey)) {
                return true; // Une autre validation devrait échouer
            }

            // Ajuster la clé secrète de seats.io pour utiliser celle du LAN puisqu'aucune n'a été passée
            $this->secretKey = $lan->secret_key;
        }

        $seatsClient = new SeatsioClient($this->secretKey);
        // Vérifier que le problème n'est pas avec la clé secrète
        try {
            // Tenter d'utiliser n'importe quelle méthode qui ne nécessite par de paramètre
            $seatsClient->charts->listAllTags();
        } catch (SeatsioException $exception) {
            // Une autre validation devrait échouer
            return true;
        }

        try {
            // Tenter de retrouver l'événement associé à la clé
            $seatsClient->events->retrieve($eventKey);
        } catch (SeatsioException $exception) {
            // Si aucun événement n'a été trouvé, une erreur est lancée
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
