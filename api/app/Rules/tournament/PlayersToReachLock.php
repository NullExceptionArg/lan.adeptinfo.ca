<?php

namespace App\Rules\Tournament;
use App\Model\Team;
use App\Model\Tournament;
use Illuminate\Contracts\Validation\Rule;

class PlayersToReachLock implements Rule
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
        if ($tournament == null || $value == null) {
            return true;
        }
        $teamsCount = Team::where('tournament_id', $tournament->id)
            ->count();
        return $teamsCount == 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.players_to_reach_lock');
    }
}