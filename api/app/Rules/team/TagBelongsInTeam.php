<?php

namespace App\Rules\Team;

use App\Model\{Tag, TagTeam, Team};
use Illuminate\Contracts\Validation\Rule;

class TagBelongsInTeam implements Rule
{
    protected $teamId;

    public function __construct(?string $teamId)
    {
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
        $tag = Tag::find($value);
        $team = Team::find($this->teamId);

        if (is_null($tag) || is_null($team)) {
            return true; // Une autre validation devrait échouer
        }

        return TagTeam::where('tag_id', $tag->id)
                ->where('team_id', $team->id)
                ->count() > 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.tag_belongs_in_team');
    }
}
