<?php

namespace App\Rules\Team;

use App\Model\Tag;
use Illuminate\{Auth\Access\AuthorizationException, Contracts\Validation\Rule, Support\Facades\Auth};

class TagBelongsToUser implements Rule
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
        $tag = Tag::find($value);
        if (is_null($tag)) {
            return true; // Une autre validation devrait échouer
        }
        if ($tag->user_id != Auth::id()) {
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