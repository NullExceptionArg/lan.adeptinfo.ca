<?php

namespace App\Repositories;


use App\Model\Contribution;
use App\Model\ContributionCategory;
use App\Model\Lan;
use App\Model\User;
use Illuminate\Database\Eloquent\Collection;

interface ContributionRepository
{
    public function createCategory(Lan $lan, string $name): ContributionCategory;

    public function getCategories(Lan $lan): Collection;

    public function findCategoryById(int $categoryId): ?ContributionCategory;

    public function deleteCategoryById(int $contributionCategoryId): void;

    public function createContributionUserFullName(string $userFullName): Contribution;

    public function createContributionUserId(User $user): Contribution;

    public function attachContributionCategoryContribution(
        Contribution $contribution,
        ContributionCategory $contributionCategory
    ): void;

    public function deleteContributionById(int $contributionId): void;

    public function findContributionById(int $contributionId): ?Contribution;
}