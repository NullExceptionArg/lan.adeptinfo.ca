<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property string lan_id
 * @property string name
 */
class ContributionCategory extends Model
{
    use SoftDeletes;

    protected $table = 'contribution_category';

    public $timestamps = false;

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
        'lan_id', 'deleted_at'
    ];

    public function Lan()
    {
        return $this->belongsTo(Lan::class);
    }

    public function Contribution()
    {
        return $this->belongsToMany(Contribution::class, 'contribution_cat_contribution');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($contributionCategory) {
            $contributions = $contributionCategory->Contribution()->get();
            foreach ($contributions as $contribution) {
                if ($contribution->ContributionCategory()->count() <= 1) {
                    $contribution->delete();
                }
            }
        });
    }
}
