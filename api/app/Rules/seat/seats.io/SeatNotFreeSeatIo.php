<?php

namespace App\Rules\Seat;

use App\Model\Lan;
use Illuminate\Contracts\Validation\Rule;
use Seatsio\SeatsioClient;
use Seatsio\SeatsioException;

class SeatNotFreeSeatIo implements Rule
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
            return true;
        }
        $seatsClient = new SeatsioClient($lan->secret_key);
        try {
            $status = $seatsClient->events->retrieveObjectStatus($lan->event_key, $value);
            return $status->status != 'free';
        } catch (SeatsioException $exception) {
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
        return trans('validation.seat_not_free_seat_io');
    }
}