<?php

namespace App\Rules\Team;

use App\Model\{Request, Tag};
use Illuminate\{Contracts\Validation\Rule, Support\Facades\Auth};

class RequestBelongsToUser implements Rule
{
    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $request = null;
        $tag = null;

        if (is_null($request = Request::find($value)) || is_null($tag = Tag::find($request->tag_id))) {
            return true; // Une autre validation devrait échouer
        }

        return $tag->user_id == Auth::id();
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.request_belongs_to_user');
    }
}
