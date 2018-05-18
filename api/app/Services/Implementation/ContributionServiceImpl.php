<?php

namespace App\Services\Implementation;


use App\Model\ContributionCategory;
use App\Model\Lan;
use App\Repositories\Implementation\ContributionRepositoryImpl;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Services\ContributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ContributionServiceImpl implements ContributionService
{
    protected $lanRepository;
    protected $contributionRepository;

    /**
     * LanServiceImpl constructor.
     * @param LanRepositoryImpl $lanRepositoryImpl
     * @param ContributionRepositoryImpl $contributionRepository
     */
    public function __construct(LanRepositoryImpl $lanRepositoryImpl, ContributionRepositoryImpl $contributionRepository)
    {
        $this->lanRepository = $lanRepositoryImpl;
        $this->contributionRepository = $contributionRepository;
    }

    public function createCategory(Request $request, string $lanId): ContributionCategory
    {
        $reservationValidator = Validator::make([
            'lan_id' => $lanId,
            'name' => $request->input('name')
        ], [
            'lan_id' => 'required|integer',
            'name' => 'required|string',
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        if ($lan == null) {
            throw new BadRequestHttpException(json_encode([
                "lan_id" => [
                    'Lan with id ' . $lanId . ' doesn\'t exist'
                ]
            ]));
        }

        $category = $this->contributionRepository->createCategory($lan, $request->input('name'));

        return $category;
    }

    public function getCategories($lanId): array
    {
        $reservationValidator = Validator::make([
            'lan_id' => $lanId,
        ], [
            'lan_id' => 'required|integer'
        ]);

        if ($reservationValidator->fails()) {
            throw new BadRequestHttpException($reservationValidator->errors());
        }

        $lan = $this->lanRepository->findLanById($lanId);

        $this->lanExists($lanId, $lan);

        $categories = $this->contributionRepository->getCategoryForLan($lan);

        return $categories;
    }

    private function lanExists(int $lanId, ?Lan $lan)
    {
        if ($lan == null) {
            throw new BadRequestHttpException(json_encode([
                "lan_id" => [
                    'Lan with id ' . $lanId . ' doesn\'t exist'
                ]
            ]));
        }
    }
}