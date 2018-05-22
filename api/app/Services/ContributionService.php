<?php

namespace App\Services;


use App\Model\Contribution;
use App\Model\ContributionCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface ContributionService
{
    public function createCategory(Request $request, string $lanId): ContributionCategory;

    public function createContribution(Request $request, string $lanId): Contribution;

    public function getCategories(string $lanId): Collection;

    public function deleteCategory(string $lanId, string $contributionCategoryId): ContributionCategory;

    public function deleteContribution(string $lanId, string $contributionId): Contribution;
}