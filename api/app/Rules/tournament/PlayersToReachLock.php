<?php

namespace App\Rules\Tournament;

use App\Model\{Team, Tournament};
use Illuminate\Contracts\Validation\Rule;

class PlayersToReachLock implements Rule
{
    protected $tournamentId;

    public function __construct(?string $tournamentId)
    {
        $this->tournamentId = $tournamentId;
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
        $tournament = Tournament::find($this->tournamentId);
        if ($tournament == null || $value == null) {
            return true; // Une autre validation devrait échouer
        }
        $teamsCount = Team::where('tournament_id', $tournament->id)
            ->count();
        return $teamsCount == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.players_to_reach_lock');
    }
}