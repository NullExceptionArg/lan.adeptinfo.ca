<?php

namespace App\Http\Controllers;

use App\Services\Implementation\ContributionServiceImpl;
use Dingo\Api\Routing\Helpers;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    use Helpers;

    protected $contributionService;

    /**
     * LanController constructor.
     * @param ContributionServiceImpl $contributionService
     */
    public function __construct(ContributionServiceImpl $contributionService)
    {
        $this->contributionService = $contributionService;
    }

    public function createContributionCategory(Request $request, string $lanId)
    {
        return response()->json($this->contributionService->createCategory($request, $lanId), 201);
    }

    public function getContributionCategories(string $lanId)
    {
        return response()->json($this->contributionService->getCategories($lanId), 200);
    }

    public function deleteContributionCategory(string $lanId, string $contributionCategoryId)
    {
        return response()->json($this->contributionService->deleteCategory($lanId, $contributionCategoryId), 200);
    }

    public function createContribution(Request $request, string $lanId)
    {
        return response()->json($this->contributionService->createContribution($request, $lanId), 201);
    }

    public function getContributions(string $lanId)
    {
        return response()->json($this->contributionService->getContributions($lanId), 200);
    }

    public function deleteContribution(string $lanId, string $contributionCategoryId)
    {
        return response()->json($this->contributionService->deleteContribution($lanId, $contributionCategoryId), 200);
    }
}
