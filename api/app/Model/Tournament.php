<?php

namespace App\Model;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int lan_id
 * @property string name
 * @property DateTime tournament_start
 * @property DateTime tournament_end
 * @property int players_to_reach
 * @property int teams_to_reach
 * @property string state
 * @property string rules
 * @property int|null price
 */
class Tournament extends Model
{
    protected $table = 'tournament';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];
}