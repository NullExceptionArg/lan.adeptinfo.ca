<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed tag_id
 * @property int team_id
 * @property bool is_leader
 */
class TagTeam extends Model
{
    protected $table = 'tag_team';
}