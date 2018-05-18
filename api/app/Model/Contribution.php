<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    protected $table = 'category';

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Lan()
    {
        return $this->belongsTo(User::class);
    }

    public function contributionCategory()
    {
        return $this->hasMany(ContributionCategory::class);
    }
}
