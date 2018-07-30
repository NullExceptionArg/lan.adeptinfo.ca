<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int tournament_id
 * @property string name
 * @property string tag
 */
class Team extends Model
{
    protected $table = 'team';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];
}