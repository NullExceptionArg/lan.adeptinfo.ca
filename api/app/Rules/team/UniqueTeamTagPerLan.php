<?php

namespace App\Rules;

use App\Model\Tournament;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueTeamTagPerLan implements Rule
{

    protected $tournamentId;

    public function __construct(?string $tournamentId)
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
        $tournament = Tournament::find($this->tournamentId);
        $tournamentIds = DB::table('tournament')
            ->select('id')
            ->where('lan_id',  $tournament->lan_id)
            ->get();

        return DB::table('team')
                ->whereIn('tournament_id', $tournamentIds)
                ->where('tag', $value)
                ->count() == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.unique_team_tag_per_lan');
    }
}