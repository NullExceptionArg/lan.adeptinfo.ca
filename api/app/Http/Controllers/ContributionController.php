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
     * ContributionController constructor.
     * @param ContributionServiceImpl $contributionService
     */
    public function __construct(ContributionServiceImpl $contributionService)
    {
        $this->contributionService = $contributionService;
    }

    public function createContributionCategory(Request $request)
    {
        return response()->json($this->contributionService->createCategory($request), 201);
    }

    public function getContributionCategories(Request $request)
    {
        return response()->json($this->contributionService->getCategories($request), 200);
    }

    public function deleteContributionCategory(Request $request)
    {
        return response()->json($this->contributionService->deleteCategory($request), 200);
    }

    public function createContribution(Request $request)
    {
        return response()->json($this->contributionService->createContribution($request), 201);
    }

    public function getContributions(Request $request)
    {
        return response()->json($this->contributionService->getContributions($request), 200);
    }

    public function deleteContribution(Request $request)
    {
        return response()->json($this->contributionService->deleteContribution($request), 200);
    }
}
