<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Lien entre un tag et une équipe.
 *
 * @property mixed tag_id
 * @property int team_id
 * @property bool is_leader
 */
class TagTeam extends Model
{
    protected $table = 'tag_team';

    /**
     * Les attributs qui doivent être mutés en dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
