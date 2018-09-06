<?php

namespace App\Http\Controllers;

use App\Services\Implementation\RoleServiceImpl;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    protected $roleService;

    /**
     * LanController constructor.
     * @param RoleServiceImpl $roleService
     */
    public function __construct(RoleServiceImpl $roleService)
    {
        $this->roleService = $roleService;
    }

    public function createContributionCategory(Request $request)
    {
        return response()->json($this->roleService->create($request), 201);
    }
}
