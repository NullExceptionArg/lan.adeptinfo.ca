<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ContributionCategoryContribution extends Model
{
    protected $table = 'contribution_cat_contribution';

    public function contributionCategory()
    {
        return $this->belongsTo(ContributionCategory::class);
    }

    public function contribution()
    {
        return $this->belongsTo(Contribution::class);
    }
}
