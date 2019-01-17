<?php

namespace App\Rules\Seat;

use App\Model\Lan;
use Illuminate\Contracts\Validation\Rule;
use Seatsio\{SeatsioClient, SeatsioException};

/**
 * Un siège n'est pas déjà réservé ou occupé par un utilisateur pour un certain LAN.
 *
 * Class SeatOncePerLanSeatIo
 * @package App\Rules\Seat
 */
class SeatOncePerLanSeatIo implements Rule
{
    protected $lanId;

    /**
     * SeatOncePerLanSeatIo constructor.
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
     * @param  string $seatId
     * @return bool
     */
    public function passes($attribute, $seatId): bool
    {
        $lan = Lan::find($this->lanId);

        /*
         * Condition de garde
         * Un LAN correspond à l'id de LAN passé
         */
        if (is_null($lan)) {
            return true; // Une autre validation devrait échouer
        }

        $seatsClient = new SeatsioClient($lan->secret_key);
        try {
            // Demander à l'API de retrouver le siège pour l'événement du LAN, pour l'id du siège
            $status = $seatsClient->events->retrieveObjectStatus($lan->event_key, $seatId);

            // Vérifier que le statut n'est pas à "booked" ou à "arrived" (Disponible)
            return !($status->status === 'booked') && !($status->status === 'arrived');
        } catch (SeatsioException $exception) {
            // Si aucun siège n'est trouvé, l'API retourne une erreur
            // Une autre validation devrait échouer
            return true;
        }
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.seat_once_per_lan_seat_io');
    }
}
