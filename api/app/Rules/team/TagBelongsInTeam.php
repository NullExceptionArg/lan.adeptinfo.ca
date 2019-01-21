<?php

namespace App\Rules\Team;

use App\Model\{Tag, TagTeam, Team};
use Illuminate\Contracts\Validation\Rule;

/**
 * Un tag de joueur fait parti d'une équipe.
 *
 * Class TagBelongsInTeam
 * @package App\Rules\Team
 */
class TagBelongsInTeam implements Rule
{
    protected $teamId;

    /**
     * TagBelongsInTeam constructor.
     * @param string|null $teamId Id de l'équipe
     */
    public function __construct(?string $teamId)
    {
        $this->teamId = $teamId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $tagId Id du tag de joueur
     * @return bool
     */
    public function passes($attribute, $tagId): bool
    {
        $tag = Tag::find($tagId);
        $team = Team::find($this->teamId);

        /*
         * Conditions de garde :
         * Un tag de joueur existe pour l'id de tag de joueur passé
         * Une équipe existe pour l'id de l'équipe passée
         */
        if (is_null($tag) || is_null($team)) {
            return true; // Une autre validation devrait échouer
        }

        // Si un lien entre un tag et une équipe existe pour le tag et l'équipe
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
