<?php

namespace App\Rules\Team;

use App\Model\Request;
use App\Model\Team;
use Illuminate\Contracts\Validation\Rule;

class RequestBelongsInTeam implements Rule
{
    protected $teamId;

    public function __construct(?string $teamId)
    {
        $this->teamId = $teamId;
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
        $request = Request::find($value);
        $team = Team::find($this->teamId);
        if ($request == null || $team == null) {
            return true;
        }

        return $request->team_id == $team->id;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.request_belongs_in_team');
    }
}