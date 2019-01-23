<?php

namespace App\Rules\Tournament;

use App\Model\{OrganizerTournament, Tournament};
use Illuminate\{Contracts\Validation\Rule};

/**
 * Un utilisateur est administrateur d'un tournoi.
 *
 * Class UserIsTournamentAdmin
 * @package App\Rules\Tournament
 */
class UserIsTournamentAdmin implements Rule
{
    protected $userId;

    /**
     * UserIsTournamentAdmin constructor.
     * @param int $userId Id de l'utilisateur
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }


    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $tournamentId
     * @return bool
     */
    public function passes($attribute, $tournamentId): bool
    {
        $tournament = Tournament::find($tournamentId);

        /*
         * Conditions de garde :
         * L'id du tournoi correspond à un tournoi
         */
        if (is_null($tournament)) {
            return true; // Une autre validation devrait échouer
        }

        // Chercher s'il existe un lien entre l'utilisateur et le tournoi
        return OrganizerTournament::where('organizer_id', $this->userId)
                ->where('tournament_id', $tournamentId)
                ->count() > 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.organizer_has_tournament');
    }
}
