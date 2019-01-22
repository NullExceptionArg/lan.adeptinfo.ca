<?php

namespace App\Rules\Tournament;

use Illuminate\{Contracts\Validation\Rule, Support\Facades\Auth, Support\Facades\DB};

/**
 * L'utilisateur courant n'est qu'une seule fois dans un tournoi.
 *
 * Class UniqueUserPerTournament
 * @package App\Rules\Team
 */
class UniqueUserPerTournament implements Rule
{
    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $tournamentId
     * @return bool
     */
    public function passes($attribute, $tournamentId): bool
    {
        if (is_null($tournamentId)) {
            return true; // Une autre validation devrait échouer
        }

        $teamIds = DB::table('team')
            ->select('id')
            ->where('tournament_id', $tournamentId)
            ->pluck('id')
            ->toArray();

        $tagIds = DB::table('tag_team')
            ->select('tag_id')
            ->whereIn('team_id', $teamIds)
            ->pluck('tag_id')
            ->toArray();

        return DB::table('tag')
                ->select('id')
                ->whereIn('id', $tagIds)
                ->where('user_id', Auth::id())
                ->count() == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.unique_user_per_tournament');
    }
}
