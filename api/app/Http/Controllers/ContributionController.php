<?php

namespace App\Http\Controllers;

use App\Rules\{General\OneOfTwoFields, User\HasPermissionInLan};
use App\Services\Implementation\ContributionServiceImpl;
use Illuminate\{Http\Request, Support\Facades\Auth, Support\Facades\Validator};

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

    public function createCategory(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'name' => $request->input('name'),
            'permission' => 'create-contribution-category'
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'name' => 'required|string',
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->contributionService->createCategory(
            $request->input('lan_id'),
            $request->input('name')
        ), 201);
    }

    public function createContribution(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'contribution_category_id' => $request->input('contribution_category_id'),
            'user_full_name' => $request->input('user_full_name'),
            'user_email' => $request->input('user_email'),
            'permission' => 'create-contribution'
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'contribution_category_id' => 'required|integer|exists:contribution_category,id,deleted_at,NULL',
            'user_full_name' => [
                'required_without:user_email',
                'string',
                'nullable',
                new OneOfTwoFields($request->input('user_email'), 'user_email')
            ],
            'user_email' => [
                'required_without:user_full_name',
                'string',
                'nullable',
                'exists:user,email',
                new OneOfTwoFields($request->input('user_full_name'), 'user_full_name')
            ],
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->contributionService->createContribution(
            $request->input('contribution_category_id'),
            $request->input('user_full_name'),
            $request->input('user_email')
        ), 201);
    }

    public function deleteCategory(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'contribution_category_id' => $request->input('contribution_category_id'),
            'permission' => 'delete-contribution-category'
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'contribution_category_id' => 'required|integer|exists:contribution_category,id,deleted_at,NULL',
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->contributionService->deleteCategory(
            $request->input('contribution_category_id')
        ), 200);
    }

    public function deleteContribution(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'contribution_id' => $request->input('contribution_id'),
            'permission' => 'delete-contribution'
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'contribution_id' => 'required|integer|exists:contribution,id,deleted_at,NULL',
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->contributionService->deleteContribution(
            $request->input('contribution_id')
        ), 200);
    }

    public function getCategories(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL'
        ]);

        $this->checkValidation($validator);

        return response()->json($this->contributionService->getCategories(
            $request->input('lan_id')
        ), 200);
    }

    public function getContributions(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id')
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
        ]);

        $this->checkValidation($validator);

        return response()->json($this->contributionService->getContributions(
            $request->input('lan_id')
        ), 200);
    }
}
