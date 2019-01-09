<?php

namespace App\Rules\Team;

use App\Model\Tag;
use App\Model\TagTeam;
use App\Model\Team;
use Illuminate\Contracts\Validation\Rule;

class TagBelongsInTeam implements Rule
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
        $tag = Tag::find($value);
        $team = Team::find($this->teamId);

        if (is_null($tag) || is_null($team)) {
            return true;
        }

        return TagTeam::where('tag_id', $tag->id)
                ->where('team_id', $team->id)
                ->count() > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.tag_belongs_in_team');
    }
}
