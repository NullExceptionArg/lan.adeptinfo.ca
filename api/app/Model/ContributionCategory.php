<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ContributionCategory extends Model
{
    protected $table = 'contribution_category';

    public function Lan()
    {
        return $this->belongsTo(Lan::class);
    }

    public function contributionCategoryContribution()
    {
        return $this->hasMany(ContributionCategoryContribution::class);
    }
}
