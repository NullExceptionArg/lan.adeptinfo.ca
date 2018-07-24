<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string name
 * @property int user_id
 */
class Tag extends Model
{
    protected $table = 'tag';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'user_id',
    ];
}