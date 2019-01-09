<?php

namespace App\Rules\Team;

use App\Model\Request;
use App\Model\Tag;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class RequestBelongsToUser implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $request = null;
        $tag = null;

        if (is_null($request = Request::find($value)) || is_null($tag = Tag::find($request->tag_id))) {
            return true;
        }

        return $tag->user_id == Auth::id();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.request_belongs_to_user');
    }
}
