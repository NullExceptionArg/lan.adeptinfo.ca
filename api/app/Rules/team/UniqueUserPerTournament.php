<?php

namespace App\Rules\Team;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UniqueUserPerTournament implements Rule
{

    protected $tournamentId;

    public function __construct(?int $tournamentId)
    {
        $this->tournamentId = $tournamentId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $teamIds = DB::table('team')
            ->select('id')
            ->where('tournament_id', $this->tournamentId)
            ->pluck('id')
            ->toArray();

        $tagIds = DB::table('tag_team')
            ->select('tag_id')
            ->whereIn('team_id', $teamIds)
            ->pluck('tag_id')
            ->toArray();

        return DB::table('tag')
                ->select('id')
                ->whereIn('id', $tagIds)
                ->where('user_id', Auth::id())
                ->count() == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.unique_user_per_tournament');
    }
}