<?php

namespace App\Repositories\Implementation;


use App\Model\ContributionCategory;
use App\Model\Lan;
use App\Repositories\ContributionRepository;

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

    public function getCategoryForLan(Lan $lan): array
    {
        return $lan->contributionCategory()->where('lan_id', $lan->id)->get()->toArray();
    }

    public function findCategoryById(int $categoryId): ?ContributionCategory
    {
        return ContributionCategory::find($categoryId);
    }

    public function deleteCategory(ContributionCategory $contributionCategory): void
    {
        try {
            $contributionCategory->delete();
        } catch (\Exception $e) {
        }
    }
}