<?php

namespace App\Model;

use Illuminate\{Database\Eloquent\Model, Database\Eloquent\SoftDeletes};

/**
 * @property int id
 * @property int tournament_id
 * @property string name
 * @property string tag
 */
class Team extends Model
{
    use SoftDeletes;

    protected $table = 'team';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    protected $casts = ['tournament_id' => 'integer'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($team) {
            TagTeam::where('team_id', $team->id)->delete();
            Request::where('team_id', $team->id)->delete();
        });
    }
}
