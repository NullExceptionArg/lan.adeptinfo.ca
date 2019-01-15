<?php

namespace App\Rules\Seat;

use App\Model\{Reservation, User};
use Illuminate\{Contracts\Auth\Authenticatable, Contracts\Validation\Rule};

class UserOncePerLan implements Rule
{
    protected $user;
    protected $email;

    /**
     * SeatOncePerLan constructor.
     * @param Authenticatable $user
     * @param string|null $email
     */
    public function __construct(?Authenticatable $user, ?string $email)
    {
        $this->user = $user;
        $this->email = $email;
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
        if ($this->user == null) {
            if ($this->email == null) {
                return true; // Une autre validation devrait échouer
            }
            $this->user = User::where('email', $this->email)->first();
            if ($this->user == null) {
                return true;
            }
        }

        $lanUserReservation = Reservation::where('user_id', $this->user->id)
            ->where('lan_id', $value)->first();

        if ($lanUserReservation != null && $lanUserReservation->count() > 0) {
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
        return trans('validation.user_once_per_lan');
    }
}
