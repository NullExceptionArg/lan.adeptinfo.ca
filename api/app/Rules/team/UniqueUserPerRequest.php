<?php

namespace App\Rules\Team;

use App\Model\Tag;
use App\Model\Team;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UniqueUserPerRequest implements Rule
{
    protected $tagId;

    public function __construct(?int $tagId)
    {
        $this->tagId = $tagId;
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
        $tag = Tag::find($this->tagId);
        if (is_null($tag) || $tag->user_id != Auth::id()) {
            return true;
        }

        $team = Team::find($value);

        if (is_null($team)) {
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
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.unique_user_per_request');
    }
}