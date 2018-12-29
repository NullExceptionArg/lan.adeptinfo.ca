<?php

namespace App\Services\Implementation;

use App\Http\Resources\Contribution\ContributionCategoryResource;
use App\Http\Resources\Contribution\ContributionResource;
use App\Http\Resources\Contribution\GetContributionsResource;
use App\Model\ContributionCategory;
use App\Repositories\Implementation\ContributionRepositoryImpl;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\UserRepositoryImpl;
use App\Services\ContributionService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ContributionServiceImpl implements ContributionService
{
    protected $lanRepository;
    protected $contributionRepository;
    protected $userRepository;

    /**
     * LanServiceImpl constructor.
     * @param LanRepositoryImpl $lanRepositoryImpl
     * @param ContributionRepositoryImpl $contributionRepository
     * @param UserRepositoryImpl $userRepository
     */
    public function __construct(
        LanRepositoryImpl $lanRepositoryImpl,
        ContributionRepositoryImpl $contributionRepository,
        UserRepositoryImpl $userRepository
    )
    {
        $this->lanRepository = $lanRepositoryImpl;
        $this->contributionRepository = $contributionRepository;
        $this->userRepository = $userRepository;
    }

    public function createCategory(int $lanId, string $name): ContributionCategory
    {
        $contributionId = $this->contributionRepository->createCategory($lanId, $name);
        return $this->contributionRepository->findCategoryById($contributionId);
    }

    public function createContribution(
        int $contributionCategoryId,
        ?string $userFullName,
        ?string $email
    ): ContributionResource
    {
        $contribution = null;
        if ($userFullName != null) {
            $contribution = $this->contributionRepository->createContributionUserFullName($userFullName);
        } else {
            $user = $this->userRepository->findByEmail($email);
            $contribution = $this->contributionRepository->createContributionUserId($user->id);
        }

        $this->contributionRepository->attachContributionCategoryContribution($contribution->id, $contributionCategoryId);
        return new ContributionResource($contribution);
    }

    public function getCategories(int $lanId): AnonymousResourceCollection
    {
        return ContributionCategoryResource::collection($this->contributionRepository->getCategories($lanId));
    }

    public function getContributions(int $lanId): AnonymousResourceCollection
    {
        return GetContributionsResource::collection($this->contributionRepository->getCategories($lanId));
    }

    public function deleteCategory(int $contributionCategoryId): ContributionCategory
    {
        $contributionCategory = $this->contributionRepository->findCategoryById($contributionCategoryId);
        $this->contributionRepository->deleteCategoryById($contributionCategoryId);

        return $contributionCategory;
    }

    public function deleteContribution(int $contributionId): ContributionResource
    {
        $contribution = $this->contributionRepository->findContributionById($contributionId);
        $this->contributionRepository->deleteContributionById($contributionId);

        return new ContributionResource($contribution);
    }
}