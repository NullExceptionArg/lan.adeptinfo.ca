<?php

namespace App\Http\Controllers;

use App\Rules\HasPermissionInLan;
use App\Services\Implementation\ContributionServiceImpl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ContributionController extends Controller
{
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
        $request = $this->adjustRequestForLan($request);
        $categoryValidator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'name' => $request->input('name'),
            'permission' => 'create-contribution-category'
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'name' => 'required|string',
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        if ($categoryValidator->fails()) {
            throw new BadRequestHttpException($categoryValidator->errors());
        }

        return response()->json($this->contributionService->createCategory(
            $request->input('lan_id'),
            $request->input('name')
        ), 201);
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
