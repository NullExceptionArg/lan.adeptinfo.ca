<?php

namespace App\Services;


use App\Model\ContributionCategory;
use Illuminate\Http\Request;

interface ContributionService
{
    public function createCategory(Request $request, string $lanId): ContributionCategory;

    public function getCategories($lanId): array;

    public function deleteCategory($lanId, $contributionCategoryId): array;
}