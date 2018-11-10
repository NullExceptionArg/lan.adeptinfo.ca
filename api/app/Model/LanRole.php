<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property int lan_id
 * @property string name
 * @property string en_display_name
 * @property string en_description
 * @property string fr_display_name
 * @property string fr_description
 */
class LanRole extends Model
{
    protected $table = 'lan_role';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $hidden = ['id', 'deleted_at', 'created_at', 'updated_at'];

    protected $casts = [
        'id' => 'integer',
        'lan_id' => 'integer'
    ];
}
