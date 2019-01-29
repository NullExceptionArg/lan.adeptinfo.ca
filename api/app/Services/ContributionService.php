<?php

namespace App\Services;

use App\Http\Resources\Contribution\ContributionResource;
use App\Model\ContributionCategory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

interface ContributionService
{
    public function createCategory(int $lanId, string $name): ContributionCategory;

    public function createContribution(
        int $contributionCategoryId,
        ?string $userFullName,
        ?string $email
    ): ContributionResource;

    public function deleteCategory(int $lanId): ContributionCategory;

    public function deleteContribution(int $contributionId): ContributionResource;

    public function getCategories(int $lanId): AnonymousResourceCollection;

    public function getContributions(int $lanId): AnonymousResourceCollection;
}
