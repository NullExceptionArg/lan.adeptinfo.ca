<?php

namespace App\Services;


use App\Model\Contribution;
use App\Model\ContributionCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface ContributionService
{
    public function createCategory(Request $request): ContributionCategory;

    public function createContribution(Request $input): Contribution;

    public function getContributions(Request $input): AnonymousResourceCollection;

    public function getCategories(Request $input): Collection;

    public function deleteCategory(Request $input): ContributionCategory;

    public function deleteContribution(Request $input): Contribution;
}