<?php

namespace App\Rules\Team;

use App\Model\Tag;
use Illuminate\{Auth\Access\AuthorizationException, Contracts\Validation\Rule};

/**
 * Un tag de joueur appartient à un utilisateur.
 *
 * Class TagBelongsToUser
 * @package App\Rules\Team
 */
class TagBelongsToUser implements Rule
{
    protected $userId;

    /**
     * TagBelongsToUser constructor.
     * @param int $userId Id de l'utilisateur
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }


    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $tagId Id du tag
     * @return bool
     * @throws AuthorizationException
     */
    public function passes($attribute, $tagId): bool
    {
        $tag = Tag::find($tagId);

        /*
         * Condition de garde :
         * Un tag de joueur doit correspondre à l'id de tag de joueur
         */
        if (is_null($tag)) {
            return true; // Une autre validation devrait échouer
        }

        // L'id d'utilisateur du tag ne correspond pas à celui de l'utilisateur, lancer une exception
        if ($tag->user_id != $this->userId) {
            throw new AuthorizationException(trans('validation.forbidden'));
        }

        return true;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.tag_belongs_to_user');
    }
}
