<?php

namespace App\Rules\Team;

use Illuminate\{Contracts\Validation\Rule, Support\Facades\DB};

class UniqueTeamNamePerTournament implements Rule
{
    protected $tournamentId;

    public function __construct(?int $tournamentId)
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
        return DB::table('team')
                ->where('tournament_id', $this->tournamentId)
                ->where('name', $value)
                ->count() == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.unique_team_name_per_tournament');
    }
}