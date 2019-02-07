<?php

namespace App\Rules\Team;

use App\Model\{Tag, TagTeam, Team};
use Illuminate\Contracts\Validation\Rule;

/**
 * Un tag de joueur n'appartient à à un chef d'une équipe.
 *
 * Class TagNotBelongsLeader
 * @package App\Rules\Team
 */
class TagNotBelongsLeader implements Rule
{
    protected $teamId;

    /**
     * TagNotBelongsLeader constructor.
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
         * L'id du tag de joueur correspond à un tag de joueur
         * L'id de l'équipe correspond à une équipe
         */
        if (is_null($tag) || is_null($team)) {
            return true; // Une autre validation devrait échouer
        }

        // Chercher si un lien existe entre le tag, l'équipe, et le tag est le chef
        return TagTeam::where('tag_id', $tag->id)
                ->where('team_id', $team->id)
                ->where('is_leader', true)
                ->count() == 0;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.tag_not_belongs_leader');
    }
}
