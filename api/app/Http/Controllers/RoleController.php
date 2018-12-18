<?php

namespace App\Http\Controllers;

use App\Services\Implementation\RoleServiceImpl;
use Illuminate\Http\Request;

class RoleController extends Controller
{

    protected $roleService;

    /**
     * RoleController constructor.
     * @param RoleServiceImpl $roleService
     */
    public function __construct(RoleServiceImpl $roleService)
    {
        $this->roleService = $roleService;
    }

    public function createLanRole(Request $request)
    {
        return response()->json($this->roleService->createLanRole($request), 201);
    }

    public function editLanRole(Request $request)
    {
        return response()->json($this->roleService->editLanRole($request), 200);
    }

    public function assignLanRole(Request $request){
        return response()->json($this->roleService->assignLanRole($request), 200);
    }

    public function addPermissionsLanRole(Request $request)
    {
        return response()->json($this->roleService->addPermissionsLanRole($request), 200);
    }

    public function getLanRoles(Request $request)
    {
        return response()->json($this->roleService->getLanRoles($request), 200);
    }

    public function getLanRolePermissions(Request $request)
    {
        return response()->json($this->roleService->getLanRolePermissions($request), 200);
    }


    public function getLanUsers(Request $request)
    {
        return response()->json($this->roleService->getLanUsers($request), 200);
    }

    public function createGlobalRole(Request $request)
    {
        return response()->json($this->roleService->createGlobalRole($request), 201);
    }

    public function editGlobalRole(Request $request)
    {
        return response()->json($this->roleService->editGlobalRole($request), 200);
    }

    public function assignGlobalRole(Request $request){
        return response()->json($this->roleService->assignGlobalRole($request), 200);
    }

    public function addPermissionsGlobalRole(Request $request)
    {
        return response()->json($this->roleService->addPermissionsGlobalRole($request), 200);
    }

    public function getGlobalRoles(Request $request)
    {
        return response()->json($this->roleService->getGlobalRoles($request), 200);
    }

    public function getGlobalRolePermissions(Request $request)
    {
        return response()->json($this->roleService->getGlobalRolePermissions($request), 200);
    }

    public function getPermissions(Request $request)
    {
        return response()->json($this->roleService->getPermissions($request), 200);
    }

    public function getGlobalUsers(Request $request)
    {
        return response()->json($this->roleService->getGlobalUsers($request), 200);
    }
}
