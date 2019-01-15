<?php

namespace App\Rules\Team;

use App\Model\Team;
use Illuminate\{Contracts\Validation\Rule, Support\Facades\Auth, Support\Facades\DB};

class UniqueUserPerTournament implements Rule
{
    protected $tournamentId;
    protected $teamId;

    public function __construct(?int $tournamentId, ?int $teamId)
    {
        $this->tournamentId = $tournamentId;
        $this->teamId = $teamId;
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
        if ($this->tournamentId == null) {
            $team = Team::find($this->teamId);
            if ($team == null) {
                return true; // Une autre validation devrait échouer
            }
            $this->tournamentId = $team->tournament_id;
        }

        $teamIds = DB::table('team')
            ->select('id')
            ->where('tournament_id', $this->tournamentId)
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