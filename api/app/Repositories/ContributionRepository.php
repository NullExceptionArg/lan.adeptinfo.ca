<?php

namespace App\Repositories;

use App\Model\{Contribution, ContributionCategory};
use Illuminate\Support\Collection;

interface ContributionRepository
{
    public function attachContributionCategoryContribution(
        int $contributionId,
        int $contributionCategoryId
    ): void;

    public function createCategory(int $lanId, string $name): int;

    public function createContributionUserFullName(string $userFullName): Contribution;

    public function createContributionUserId(int $userId): Contribution;

    public function deleteCategoryById(int $contributionCategoryId): void;

    public function deleteContributionById(int $contributionId): void;

    public function findCategoryById(int $categoryId): ?ContributionCategory;

    public function findContributionById(int $contributionId): ?Contribution;

    public function getCategories(int $lanId): Collection;
}
