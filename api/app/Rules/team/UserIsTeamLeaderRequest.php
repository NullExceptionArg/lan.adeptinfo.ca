<?php

namespace App\Rules\Team;

use App\Model\Request;
use App\Model\TagTeam;
use App\Model\Team;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserIsTeamLeaderRequest implements Rule
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
        $request = null;
        $team = null;
        if (is_null($request = Request::find($value)) || is_null($team = Team::find($request->team_id))) {
            return true;
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