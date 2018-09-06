<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string name
 * @property string enDisplayName
 * @property string enDescription
 * @property string frDisplayName
 * @property string frDescription
 */
class Role extends Model
{
    use SoftDeletes;

    protected $table = 'role';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $hidden = ['id', 'deleted_at'];
}
