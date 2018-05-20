<?php

namespace App\Repositories\Implementation;


use App\Model\Contribution;
use App\Model\ContributionCategory;
use App\Model\Lan;
use App\Model\User;
use App\Repositories\ContributionRepository;
use Illuminate\Database\Eloquent\Collection;

class ContributionRepositoryImpl implements ContributionRepository
{

    public function createCategory(Lan $lan, string $name): ContributionCategory
    {
        $category = new ContributionCategory();
        $category->lan_id = $lan->id;
        $category->name = $name;
        $category->save();

        return $category;
    }

    public function getCategories(Lan $lan): Collection
    {
        return $lan->contributionCategory()->where('lan_id', $lan->id)->get();
    }

    public function findCategoryById(int $categoryId): ?ContributionCategory
    {
        return ContributionCategory::find($categoryId);
    }

    public function deleteCategoryById(int $contributionCategoryId): void
    {
        ContributionCategory::destroy($contributionCategoryId);
    }

    public function createContributionUserFullName(string $userFullName): Contribution
    {
        $contribution = new Contribution();
        $contribution->user_full_name = $userFullName;

        $contribution->save();

        return $contribution;
    }

    public function createContributionUserId(User $user): Contribution
    {
        $contribution = new Contribution();
        $contribution->user_id = $user->id;

        $contribution->save();

        return $contribution;
    }

    public function attachContributionCategoryContribution(
        Contribution $contribution,
        ContributionCategory $contributionCategory
    ): void
    {
        $contribution->ContributionCategory()->attach($contributionCategory->id);
    }

    public function deleteContributionById(int $contributionId): void
    {
        Contribution::destroy($contributionId);
    }

    public function findContributionById(int $contributionId): ?Contribution
    {
        return Contribution::find($contributionId);
    }
}