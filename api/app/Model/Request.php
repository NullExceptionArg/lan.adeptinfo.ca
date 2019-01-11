<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int team_id
 * @property int tag_id
 */
class Request extends Model
{
    protected $table = 'request';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $casts = ['tag_id' => 'integer', 'team_id' => 'integer'];
}
