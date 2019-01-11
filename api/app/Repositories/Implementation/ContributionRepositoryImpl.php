<?php

namespace App\Repositories\Implementation;

use App\Model\{Contribution, ContributionCategory};
use App\Repositories\ContributionRepository;
use Illuminate\{Support\Collection, Support\Facades\DB};

class ContributionRepositoryImpl implements ContributionRepository
{
    public function attachContributionCategoryContribution(
        int $contributionId,
        int $contributionCategoryId
    ): void
    {
        DB::table('contribution_cat_contribution')->insert([
            'contribution_id' => $contributionId,
            'contribution_category_id' => $contributionCategoryId
        ]);
    }

    public function createCategory(int $lanId, string $name): int
    {
        return DB::table('contribution_category')
            ->insertGetId([
                'lan_id' => $lanId,
                'name' => $name
            ]);
    }

    public function createContributionUserFullName(string $userFullName): Contribution
    {
        $contribution = new Contribution();
        $contribution->user_full_name = $userFullName;

        $contribution->save();

        return $contribution;
    }

    public function createContributionUserId(int $userId): Contribution
    {
        $contribution = new Contribution();
        $contribution->user_id = $userId;

        $contribution->save();

        return $contribution;
    }

    public function deleteCategoryById(int $contributionCategoryId): void
    {
        ContributionCategory::destroy($contributionCategoryId);
    }

    public function deleteContributionById(int $contributionId): void
    {
        Contribution::destroy($contributionId);
    }

    public function findCategoryById(int $categoryId): ?ContributionCategory
    {
        return ContributionCategory::find($categoryId);
    }

    public function findContributionById(int $contributionId): ?Contribution
    {
        return Contribution::find($contributionId);
    }

    public function getCategories(int $lanId): Collection
    {
        return ContributionCategory::where('lan_id', $lanId)
            ->select('id', 'name')
            ->get();
    }
}
