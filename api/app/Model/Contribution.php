<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int lan_id
 * @property string user_full_name
 * @property int user_id
 * @property int contribution_category_id
 */
class Contribution extends Model
{
    use SoftDeletes;

    protected $table = 'contribution';

    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $hidden = ['user_id', 'lan_id', 'pivot', 'deleted_at'];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Lan()
    {
        return $this->belongsTo(User::class);
    }

    public function ContributionCategory()
    {
        return $this->belongsToMany(ContributionCategory::class, 'contribution_cat_contribution');
    }
}
