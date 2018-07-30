<?php

namespace App\Rules\Team;

use App\Model\Team;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UniqueUserPerRequest implements Rule
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
        $team = Team::find($value);

        if ($team == null) {
            return true;
        }

        $tagIds = DB::table('tag')
            ->select('id')
            ->where('user_id', Auth::id())
            ->pluck('id')
            ->toArray();

        return DB::table('request')
                ->whereIn('id', $tagIds)
                ->where('team_id', $team->id)
                ->count() == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.unique_user_per_request');
    }
}