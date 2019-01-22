<?php

namespace App\Rules\Team;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

/**
 * Le nom d'un tag d'équipe n'est déjà utilisé dans un tournoi.
 *
 * Class UniqueTeamTagPerTournament
 * @package App\Rules\Team
 */
class UniqueTeamTagPerTournament implements Rule
{
    protected $tournamentId;

    /**
     * UniqueTeamTagPerTournament constructor.
     * @param int|null $tournamentId Id du tournoi
     */
    public function __construct(?int $tournamentId)
    {
        $this->tournamentId = $tournamentId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $name
     * @return bool
     */
    public function passes($attribute, $name): bool
    {
        // Chercher si des équipes ont le tag du nom spécifié dans le tournoi
        return DB::table('team')
                ->where('tournament_id', $this->tournamentId)
                ->where('tag', $name)
                ->count() == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.unique_team_tag_per_tournament');
    }
}
