<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string lan_id
 * @property string name
 */
class ContributionCategory extends Model
{
    protected $table = 'contribution_category';

    public $timestamps = false;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'lan_id',
    ];

    public function Lan()
    {
        return $this->belongsTo(Lan::class);
    }

    public function contributionCategoryContribution()
    {
        return $this->hasMany(ContributionCategoryContribution::class);
    }
}
