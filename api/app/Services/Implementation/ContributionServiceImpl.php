<?php

namespace App\Services\Implementation;

use App\Http\Resources\Contribution\GetContributionsResource;
use App\Model\Contribution;
use App\Model\ContributionCategory;
use App\Repositories\Implementation\ContributionRepositoryImpl;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\UserRepositoryImpl;
use App\Rules\OneOfTwoFields;
use App\Services\ContributionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
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

    public function createCategory(Request $input): ContributionCategory
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $categoryValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'name' => $input->input('name')
        ], [
            'lan_id' => 'integer|exists:lan,id',
            'name' => 'required|string',
        ]);

        if ($categoryValidator->fails()) {
            throw new BadRequestHttpException($categoryValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }
        return $this->contributionRepository->createCategory($lan, $input->input('name'));
    }

    public function getCategories(Request $input): Collection
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $reservationValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
        ], [
            'lan_id' => 'integer|exists:lan,id'
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }

        return $this->contributionRepository->getCategories($lan);
    }

    public function deleteCategory(Request $input): ContributionCategory
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $reservationValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'contribution_category_id' => $input->input('contribution_category_id')
        ], [
            'lan_id' => 'integer|exists:lan,id',
            'contribution_category_id' => 'required|integer|exists:contribution_category,id'
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        $contributionCategory = $this->contributionRepository->findCategoryById($input->input('contribution_category_id'));

        $this->contributionRepository->deleteCategoryById($input->input('contribution_category_id'));

        return $contributionCategory;
    }

    public function createContribution(Request $input): Contribution
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $contributionValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'contribution_category_id' => $input->input('contribution_category_id'),
            'user_full_name' => $input->input('user_full_name'),
            'user_email' => $input->input('user_email'),
        ], [
            'lan_id' => 'integer|exists:lan,id',
            'contribution_category_id' => 'required|integer|exists:contribution_category,id',
            'user_full_name' => [
                'required_without:user_email',
                'string',
                'nullable',
                new OneOfTwoFields($input->input('user_email'), 'user_email')
            ],
            'user_email' => [
                'required_without:user_full_name',
                'string',
                'nullable',
                'exists:user,email',
                new OneOfTwoFields($input->input('user_full_name'), 'user_full_name')
            ],
        ]);

        if ($contributionValidator->fails()) {
            throw new BadRequestHttpException($contributionValidator->errors());
        }

        $contributionCategory = $this->contributionRepository->findCategoryById($input->input('contribution_category_id'));
        $contribution = null;
        if ($input->input('user_full_name') != null) {
            $userFullName = $input->input('user_full_name');
            $contribution = $this->contributionRepository->createContributionUserFullName($userFullName);
        } else {
            $user = $this->userRepository->findByEmail($input->input('user_email'));
            $contribution = $this->contributionRepository->createContributionUserId($user->id);
            $contribution->user_full_name = $user->getFullName();
        }

        $this->contributionRepository->attachContributionCategoryContribution($contribution, $contributionCategory);

        $contribution->contribution_category_id = $contributionCategory->id;

        return $contribution;
    }

    public function getContributions(Request $input): AnonymousResourceCollection
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $contributionValidator = Validator::make([
            'lan_id' => $input->input('lan_id')
        ], [
            'lan_id' => 'integer|exists:lan,id',
        ]);

        if ($contributionValidator->fails()) {
            throw new BadRequestHttpException($contributionValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findById($input->input('lan_id'));
        }
        return GetContributionsResource::collection($this->contributionRepository->getCategories($lan));
    }

    public function deleteContribution(Request $input): Contribution
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $contributionValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'contribution_id' => $input->input('contribution_id'),
        ], [
            'lan_id' => 'integer|exists:lan,id',
            'contribution_id' => 'required|integer|exists:contribution,id'
        ]);

        if ($contributionValidator->fails()) {
            throw new BadRequestHttpException($contributionValidator->errors());
        }

        $contribution = $this->contributionRepository->findContributionById($input->input('contribution_id'));

        if ($contribution->user_full_name == null) {
            $contribution->user_full_name = $this->userRepository->findById($contribution->user_id)->getFullName();
        }

        $this->contributionRepository->deleteContributionById($input->input('contribution_id'));

        return $contribution;
    }
}