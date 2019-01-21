<?php

namespace App\Rules\Seat;

use App\Model\{Reservation, User};
use Illuminate\{Contracts\Auth\Authenticatable, Contracts\Validation\Rule};

/**
 * Un utilisateur ne possède une réservation qu'une fois dans un LAN.
 *
 * Class UserOncePerLan
 * @package App\Rules\Seat
 */
class UserOncePerLan implements Rule
{
    protected $user;
    protected $email;

    /**
     * UserOncePerLan constructor.
     * @param Authenticatable|null $user Utilisateur
     * @param string|null $email Courriel de l'utilisateur
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
     * @param  mixed $lanId Id du LAN
     * @return bool
     */
    public function passes($attribute, $lanId): bool
    {
        // Si aucun utilisateur n'a été passé, utiliser le courriel
        if (is_null($this->user)) {

            // Si le courriel est null, aucun des deux champ n'a été fourni. Une autre validation devrait échouer
            if (is_null($this->user = User::where('email', $this->email)->first())) {
                return true;
            }
        }

        // Chercher une réservation ayant l'id de l'utilisateur et l'id du LAN
        $lanUserReservation = Reservation::where('user_id', $this->user->id)
            ->where('lan_id', $lanId)->first();

        // Si des réservation a été trouvée et que le nombre de réservation est plus grand que 0
        if (!is_null($lanUserReservation) && $lanUserReservation->count() > 0) {

            // La validation échoue
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
