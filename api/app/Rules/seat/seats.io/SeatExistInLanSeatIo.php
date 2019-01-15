<?php

namespace App\Rules\Seat;

use App\Model\Lan;
use Illuminate\Contracts\Validation\Rule;
use Seatsio\{SeatsioClient, SeatsioException};

class SeatExistInLanSeatIo implements Rule
{
    protected $lanId;

    /**
     * SeatOncePerLan constructor.
     * @param string|null $lanId
     */
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
        $seatsClient = new SeatsioClient($lan->secret_key);
        try {
            $seatsClient->events->retrieveObjectStatus($lan->event_key, $value);
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
        return trans('validation.seat_exist_in_lan_seat_io');
    }
}