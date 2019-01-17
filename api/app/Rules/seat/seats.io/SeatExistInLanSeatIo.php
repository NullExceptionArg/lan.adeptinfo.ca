<?php

namespace App\Rules\Seat;

use App\Model\Lan;
use Illuminate\Contracts\Validation\Rule;
use Seatsio\{SeatsioClient, SeatsioException};

/**
 * Un siège existe dans l'API seats.io pour certain LAN.
 *
 * Class SeatExistInLanSeatIo
 * @package App\Rules\Seat
 */
class SeatExistInLanSeatIo implements Rule
{
    protected $lanId;

    /**
     * SeatOncePerLan constructor.
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
     * @param  string $seatId Id du siège
     * @return bool
     */
    public function passes($attribute, $seatId): bool
    {
        $lan = Lan::find($this->lanId);

        /*
         * Condition de garde :
         * Un LAN correspond à l'id de LAN passé
         */
        if (is_null($lan)) {
            return true; // Une autre validation devrait échouer
        }

        $seatsClient = new SeatsioClient($lan->secret_key);
        try {
            // Demander à l'API de retrouver le siège pour l'événement du LAN, pour l'id du siège
            $seatsClient->events->retrieveObjectStatus($lan->event_key, $seatId);
        } catch (SeatsioException $exception) {
            // Une erreur est lancée si aucun siège n'est trouvé
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
        return trans('validation.seat_exist_in_lan_seat_io');
    }
}