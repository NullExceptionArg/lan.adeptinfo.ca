<?php

namespace App\Repositories\Implementation;

use App\Model\{Contribution, ContributionCategory};
use App\Repositories\ContributionRepository;
use Illuminate\{Support\Collection, Support\Facades\DB};

class ContributionRepositoryImpl implements ContributionRepository
{
    public function createCategory(int $lanId, string $name): int
    {
        return DB::table('contribution_category')
            ->insertGetId([
                'lan_id' => $lanId,
                'name' => $name
            ]);
    }

    public function createContributionUserFullName(string $userFullName, int $contributionCategoryId): int
    {
        return DB::table('contribution')
            ->insertGetId([
                'user_full_name' => $userFullName,
                'contribution_category_id' => $contributionCategoryId
            ]);
    }

    public function createContributionUserId(int $userId, int $contributionCategoryId): int
    {
        return DB::table('contribution')
            ->insertGetId([
                'user_id' => $userId,
                'contribution_category_id' => $contributionCategoryId
            ]);
    }

    public function deleteCategoryById(int $contributionCategoryId): void
    {
        DB::table('contribution_category')
            ->where('id', $contributionCategoryId)
            ->delete();
    }

    public function deleteContributionById(int $contributionId): void
    {
        DB::table('contribution')
            ->where('id', $contributionId)
            ->delete();
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
