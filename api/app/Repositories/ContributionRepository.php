<?php

namespace App\Repositories;


use App\Model\ContributionCategory;
use App\Model\Lan;

interface ContributionRepository
{
    public function createCategory(Lan $lan, string $name): ContributionCategory;

    public function getCategoryForLan(Lan $lan): array;
}