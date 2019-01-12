<?php

namespace App\Rules\Team;

use App\Model\OrganizerTournament;
use App\Model\Team;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserIsTournamentAdmin implements Rule
{

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $team = Team::find($value);
        if (is_null($team)) {
            return true;
        }

        return OrganizerTournament::where('organizer_id', Auth::id())
                ->where('tournament_id', $team->tournament_id)
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