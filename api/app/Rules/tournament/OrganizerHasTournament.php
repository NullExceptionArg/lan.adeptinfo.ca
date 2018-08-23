<?php

namespace App\Rules;

use App\Model\OrganizerTournament;
use App\Model\Tournament;
use Illuminate\Contracts\Validation\Rule;

class OrganizerHasTournament implements Rule
{

    protected $userId;

    public function __construct(?int $userId)
    {
        $this->userId = $userId;
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
        $tournament = Tournament::find($value);
        if ($tournament == null) {
            return true;
        }

        return OrganizerTournament::where('organizer_id', $this->userId)
                ->where('tournament_id', $value)
                ->count() > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.organizer_has_tournament');
    }
}