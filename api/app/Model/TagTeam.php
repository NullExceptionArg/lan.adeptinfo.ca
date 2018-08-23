<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed tag_id
 * @property int team_id
 * @property bool is_leader
 */
class TagTeam extends Model
{
    use SoftDeletes;

    protected $table = 'tag_team';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}