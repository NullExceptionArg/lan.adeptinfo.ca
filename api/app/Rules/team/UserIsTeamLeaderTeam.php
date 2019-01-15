<?php

namespace App\Rules\Team;

use App\Model\{TagTeam, Team};
use Illuminate\{Auth\Access\AuthorizationException,
    Contracts\Validation\Rule,
    Support\Facades\Auth,
    Support\Facades\DB};

class UserIsTeamLeaderTeam implements Rule
{
    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     * @throws AuthorizationException
     */
    public function passes($attribute, $value): bool
    {
        $team = null;
        if (is_null($team = Team::find($value))) {
            return true; // Une autre validation devrait échouer
        }

        $tagIds = DB::table('tag')
            ->select('id')
            ->where('user_id', Auth::id())
            ->pluck('id')
            ->toArray();

        $isInTeam = TagTeam::whereIn('tag_id', $tagIds)
                ->where('team_id', $team->id)
                ->where('is_leader', true)
                ->count() > 0;

        if (!$isInTeam) {
            throw new AuthorizationException(trans('validation.forbidden'));
        } else {
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
        return trans('validation.user_is_team_leader');
    }
}