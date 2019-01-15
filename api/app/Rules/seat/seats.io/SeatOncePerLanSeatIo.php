<?php

namespace App\Rules\Seat;

use App\Model\Lan;
use Illuminate\Contracts\Validation\Rule;
use Seatsio\{SeatsioClient, SeatsioException};

class SeatOncePerLanSeatIo implements Rule
{
    protected $lanId;

    /**
     * SeatOncePerLan constructor.
     * @param string $lanId
     */
    public function __construct(string $lanId)
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
        $seatsClient = new SeatsioClient($lan->secret_key);
        try {
            $status = $seatsClient->events->retrieveObjectStatus($lan->event_key, $value);
            if ($status->status === 'booked' || $status->status === 'arrived') {
                return false;
            }
        } catch (SeatsioException $exception) {
            return true;
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
        return trans('validation.seat_once_per_lan_seat_io');
    }
}
