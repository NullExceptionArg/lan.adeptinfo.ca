<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int lan_id
 * @property string name
 * @property string en_display_name
 * @property string en_description
 * @property string fr_display_name
 * @property string fr_description
 */
class GlobalRole extends Model
{
    protected $table = 'global_role';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $hidden = ['id', 'deleted_at', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($role) {
            PermissionGlobalRole::where('role_id', $role->id)->delete();
            GlobalRoleUser::where('role_id', $role->id)->delete();
        });
    }
}
