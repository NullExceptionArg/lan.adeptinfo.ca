<?php

namespace App\Rules\Team;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueTeamNamePerTournament implements Rule
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
        return DB::table('team')
                ->where('tournament_id', $this->tournamentId)
                ->where('name', $value)
                ->count() == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.unique_team_name_per_tournament');
    }
}