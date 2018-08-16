<?php

namespace App\Rules\Team;

use App\Model\Tag;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class TagBelongsToUser implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     * @throws AuthorizationException
     */
    public function passes($attribute, $value)
    {
        $tag = Tag::find($value);
        if ($tag == null) {
            return true;
        }
        if ($tag->user_id != Auth::id()) {
            throw new AuthorizationException();
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.tag_belongs_to_user');
    }
}