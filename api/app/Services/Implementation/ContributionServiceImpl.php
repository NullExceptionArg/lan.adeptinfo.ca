<?php

namespace App\Services\Implementation;

use App\Http\Resources\Contribution\GetContributionsResource;
use App\Model\Contribution;
use App\Model\ContributionCategory;
use App\Repositories\Implementation\ContributionRepositoryImpl;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\UserRepositoryImpl;
use App\Services\ContributionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
    public function __construct(LanRepositoryImpl $lanRepositoryImpl, ContributionRepositoryImpl $contributionRepository, UserRepositoryImpl $userRepository)
    {
        $this->lanRepository = $lanRepositoryImpl;
        $this->contributionRepository = $contributionRepository;
        $this->userRepository = $userRepository;
    }

    public function createCategory(Request $request, string $lanId): ContributionCategory
    {
        $categoryValidator = Validator::make([
            'lan_id' => $lanId,
            'name' => $request->input('name')
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'name' => 'required|string',
        ]);

        if ($categoryValidator->fails()) {
            throw new BadRequestHttpException($categoryValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        return $this->contributionRepository->createCategory($lan, $request->input('name'));
    }

    public function getCategories(string $lanId): Collection
    {
        $reservationValidator = Validator::make([
            'lan_id' => $lanId,
        ], [
            'lan_id' => 'required|integer|exists:lan,id'
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        return $this->contributionRepository->getCategories($lan);
    }

    public function deleteCategory(string $lanId, string $contributionCategoryId): ContributionCategory
    {
        $reservationValidator = Validator::make([
            'lan_id' => $lanId,
            'contribution_category_id' => $contributionCategoryId
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'contribution_category_id' => 'required|integer|exists:contribution_category,id'
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        $contributionCategory = $this->contributionRepository->findCategoryById($contributionCategoryId);

        $this->contributionRepository->deleteCategoryById($contributionCategoryId);

        return $contributionCategory;
    }

    public function createContribution(Request $request, string $lanId): Contribution
    {
        $contributionValidator = Validator::make([
            'lan_id' => $lanId,
            'contribution_category_id' => $request->input('contribution_category_id'),
            'user_full_name' => $request->input('user_full_name'),
            'user_email' => $request->input('user_email'),
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'contribution_category_id' => 'required|integer|exists:contribution_category,id',
            'user_full_name' => 'required_without:user_email|string|nullable',
            'user_email' => 'required_without:user_full_name|string|nullable|exists:user,email',
        ]);

        if ($contributionValidator->fails()) {
            throw new BadRequestHttpException($contributionValidator->errors());
        }

        $contributionCategory = $this->contributionRepository->findCategoryById($request->input('contribution_category_id'));

        if ($request->input('user_full_name') != null && $request->input('user_email') != null) {
            throw new BadRequestHttpException(json_encode([
                "user_full_name" => [
                    'Field can\'t be used if user_email is used too.'
                ],
                "user_email" => [
                    'Field can\'t be used if user_full_name is used too.'
                ],
            ]));
        }

        $contribution = null;
        if ($request->input('user_full_name') != null) {
            $userFullName = $request->input('user_full_name');
            $contribution = $this->contributionRepository->createContributionUserFullName($userFullName);
        } else {
            $user = $this->userRepository->findByEmail($request->input('user_email'));
            $contribution = $this->contributionRepository->createContributionUserId($user);
            $contribution->user_full_name = $user->getFullName();
        }

        $this->contributionRepository->attachContributionCategoryContribution($contribution, $contributionCategory);

        $contribution->contribution_category_id = $contributionCategory->id;

        return $contribution;
    }

    public function getContributions(string $lanId)
    {
        $contributionValidator = Validator::make([
            'lan_id' => $lanId
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
        ]);

        if ($contributionValidator->fails()) {
            throw new BadRequestHttpException($contributionValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        return GetContributionsResource::collection($this->contributionRepository->getCategories($lan));
    }

    public function deleteContribution(string $lanId, string $contributionId): Contribution
    {
        $contributionValidator = Validator::make([
            'lan_id' => $lanId,
            'contribution_id' => $contributionId,
        ], [
            'lan_id' => 'required|integer|exists:lan,id',
            'contribution_id' => 'required|integer|exists:contribution,id'
        ]);

        if ($contributionValidator->fails()) {
            throw new BadRequestHttpException($contributionValidator->errors());
        }

        $contribution = $this->contributionRepository->findContributionById($contributionId);

        $this->contributionRepository->deleteContributionById($contributionId);

        return $contribution;
    }
}