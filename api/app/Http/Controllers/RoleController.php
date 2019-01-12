<?php

namespace App\Http\Controllers;

use App\Rules\{General\ArrayOfInteger,
    LanRoleOncePerUser,
    Role\ElementsInArrayExistInPermission,
    Role\GlobalRoleOncePerUser,
    Role\HasPermissionInLan,
    Role\LanRoleNameOncePerLan,
    Role\PermissionsBelongToRole,
    Role\PermissionsCanBePerLan,
    Role\PermissionsDontBelongToGlobalRole,
    Role\PermissionsDontBelongToLanRole,
    User\HasPermission};
use App\Services\Implementation\RoleServiceImpl;
use Illuminate\{Http\Request, Support\Facades\Auth, Support\Facades\Validator};

/**
 * Validation et application de la logique applicative sur les rôles.
 *
 * Class RoleController
 * @package App\Http\Controllers
 */
class RoleController extends Controller
{
    /**
     * Service de rôle.
     *
     * @var RoleServiceImpl
     */
    protected $roleService;

    /**
     * RoleController constructor.
     * @param RoleServiceImpl $roleService
     */
    public function __construct(RoleServiceImpl $roleService)
    {
        $this->roleService = $roleService;
    }

    public function addPermissionsGlobalRole(Request $request)
    {
        $validator = Validator::make([
            'role_id' => $request->input('role_id'),
            'permissions' => $request->input('permissions'),
            'permission' => 'add-permissions-global-role',
        ], [
            'role_id' => 'required|integer|exists:global_role,id',
            'permissions' => [
                'required',
                'array',
                new ArrayOfInteger,
                new ElementsInArrayExistInPermission,
                new PermissionsDontBelongToGlobalRole($request->input('role_id'))
            ],
            'permission' => new HasPermission(Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->addPermissionsGlobalRole(
            $request->input('role_id'),
            $request->input('permissions')
        ), 200);
    }

    public function addPermissionsLanRole(Request $request)
    {
        $validator = Validator::make([
            'role_id' => $request->input('role_id'),
            'permissions' => $request->input('permissions'),
            'permission' => 'add-permissions-lan-role',
        ], [
            'role_id' => 'required|integer|exists:lan_role,id',
            'permissions' => [
                'required',
                'array',
                new ArrayOfInteger,
                new ElementsInArrayExistInPermission,
                new PermissionsCanBePerLan,
                new PermissionsDontBelongToLanRole($request->input('role_id'))
            ],
            'permission' => new HasPermissionInLan($request->input('role_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->addPermissionsLanRole(
            $request->input('role_id'),
            $request->input('permissions')
        ), 200);
    }

    public function assignGlobalRole(Request $request)
    {
        $validator = Validator::make([
            'email' => $request->input('email'),
            'role_id' => $request->input('role_id'),
            'permission' => 'assign-global-role',
        ], [
            'email' => 'required|exists:user,email',
            'role_id' => ['integer', 'exists:global_role,id', new GlobalRoleOncePerUser($request->input('email'))],
            'permission' => new HasPermission(Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->assignGlobalRole(
            $request->input('role_id'),
            $request->input('email')
        ), 200);
    }

    public function assignLanRole(Request $request)
    {
        $validator = Validator::make([
            'email' => $request->input('email'),
            'role_id' => $request->input('role_id'),
            'permission' => 'assign-lan-role',
        ], [
            'email' => 'required|exists:user,email',
            'role_id' => ['integer', 'exists:lan_role,id', new LanRoleOncePerUser($request->input('email'))],
            'permission' => new HasPermissionInLan($request->input('role_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->assignLanRole(
            $request->input('role_id'),
            $request->input('email')
        ), 200);
    }

    public function createGlobalRole(Request $request)
    {
        $validator = Validator::make([
            'name' => $request->input('name'),
            'en_display_name' => $request->input('en_display_name'),
            'en_description' => $request->input('en_description'),
            'fr_display_name' => $request->input('fr_display_name'),
            'fr_description' => $request->input('fr_description'),
            'permissions' => $request->input('permissions'),
            'permission' => 'create-global-role',
        ], [
            'name' => 'required|string|max:50|unique:global_role,name',
            'en_display_name' => 'required|string|max:70',
            'en_description' => 'required|string|max:1000',
            'fr_display_name' => 'required|string|max:70',
            'fr_description' => 'required|string|max:1000',
            'permissions' => ['required', 'array', new ArrayOfInteger, new ElementsInArrayExistInPermission],
            'permission' => new HasPermission(Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->createGlobalRole(
            $request->input('name'),
            $request->input('en_display_name'),
            $request->input('en_description'),
            $request->input('fr_display_name'),
            $request->input('fr_description'),
            $request->input('permissions')
        ), 201);
    }

    public function createLanRole(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'name' => $request->input('name'),
            'en_display_name' => $request->input('en_display_name'),
            'en_description' => $request->input('en_description'),
            'fr_display_name' => $request->input('fr_display_name'),
            'fr_description' => $request->input('fr_description'),
            'permissions' => $request->input('permissions'),
            'permission' => 'create-lan-role',
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'name' => ['required', 'string', 'max:50', new LanRoleNameOncePerLan($request->input('lan_id'))],
            'en_display_name' => 'required|string|max:70',
            'en_description' => 'required|string|max:1000',
            'fr_display_name' => 'required|string|max:70',
            'fr_description' => 'required|string|max:1000',
            'permissions' => ['required', 'array', new ArrayOfInteger, new ElementsInArrayExistInPermission, new PermissionsCanBePerLan],
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->createLanRole(
            $request->input('lan_id'),
            $request->input('name'),
            $request->input('en_display_name'),
            $request->input('en_description'),
            $request->input('fr_display_name'),
            $request->input('fr_description'),
            $request->input('permissions')
        ), 201);
    }

    public function deleteGlobalRole(Request $request)
    {
        $validator = Validator::make([
            'role_id' => $request->input('role_id'),
            'permission' => 'delete-global-role',
        ], [
            'role_id' => 'required|integer|exists:global_role,id',
            'permission' => new HasPermission(Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->deleteGlobalRole(
            $request->input('role_id')
        ), 200);
    }

    public function deleteLanRole(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'role_id' => $request->input('role_id'),
            'permission' => 'delete-lan-role',
        ], [
            'lan_id' => 'required|integer|exists:lan,id,deleted_at,NULL',
            'role_id' => 'required|integer|exists:lan_role,id',
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->deleteLanRole(
            $request->input('role_id')
        ), 200);
    }

    public function deletePermissionsGlobalRole(Request $request)
    {
        $validator = Validator::make([
            'role_id' => $request->input('role_id'),
            'permissions' => $request->input('permissions'),
            'permission' => 'delete-permissions-global-role',
        ], [
            'role_id' => 'required|integer|exists:global_role,id',
            'permissions' => [
                'required',
                'array',
                new ArrayOfInteger,
                new PermissionsBelongToRole($request->input('role_id'))
            ],
            'permission' => new HasPermission(Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->deletePermissionsGlobalRole(
            $request->input('role_id'),
            $request->input('permissions')
        ), 200);
    }

    public function deletePermissionsLanRole(Request $request)
    {
        $validator = Validator::make([
            'role_id' => $request->input('role_id'),
            'permissions' => $request->input('permissions'),
            'permission' => 'delete-permissions-lan-role',
        ], [
            'role_id' => 'required|integer|exists:lan_role,id',
            'permissions' => [
                'required',
                'array',
                new ArrayOfInteger,
                new PermissionsBelongToRole($request->input('role_id'))
            ],
            'permission' => new HasPermissionInLan($request->input('role_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->deletePermissionsLanRole(
            $request->input('role_id'),
            $request->input('permissions')
        ), 200);
    }

    public function getGlobalRolePermissions(Request $request)
    {
        $validator = Validator::make([
            'role_id' => $request->input('role_id'),
            'permission' => 'get-global-role-permissions',
        ], [
            'role_id' => 'required|integer|exists:global_role,id',
            'permission' => new HasPermission(Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->getGlobalRolePermissions(
            $request->input('role_id')
        ), 200);
    }

    public function getGlobalRoles(Request $request)
    {
        $validator = Validator::make([
            'permission' => 'get-global-roles',
        ], [
            'permission' => new HasPermission(Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->getGlobalRoles(), 200);
    }

    public function getLanRolePermissions(Request $request)
    {
        $validator = Validator::make([
            'role_id' => $request->input('role_id'),
            'permission' => 'get-lan-role-permissions',
        ], [
            'role_id' => 'required|integer|exists:lan_role,id',
            'permission' => new HasPermissionInLan($request->input('role_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->getLanRolePermissions(
            $request->input('role_id')
        ), 200);
    }

    public function getLanRoles(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id' => $request->input('lan_id'),
            'permission' => 'get-lan-roles',
        ], [
            'lan_id' => 'required|integer|exists:lan,id,deleted_at,NULL',
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->getLanRoles(
            $request->input('lan_id')
        ), 200);
    }

    public function getLanUsers(Request $request)
    {
        $validator = Validator::make([
            'role_id' => $request->input('role_id'),
            'permission' => 'get-lan-user-roles',
        ], [
            'role_id' => 'required|integer|exists:lan_role,id',
            'permission' => new HasPermissionInLan($request->input('role_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->getLanUsers(
            $request->input('role_id')
        ), 200);
    }

    public function getPermissions(Request $request)
    {
        $validator = Validator::make([
            'permission' => 'get-permissions',
        ], [
            'permission' => new HasPermission(Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->getPermissions(), 200);
    }

    public function getRoleUsers(Request $request)
    {
        $validator = Validator::make([
            'role_id' => $request->input('role_id'),
            'permission' => 'get-global-user-roles',
        ], [
            'role_id' => 'required|integer|exists:global_role,id',
            'permission' => new HasPermission(Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->getRoleUsers(
            $request->input('role_id')
        ), 200);
    }

    public function updateGlobalRole(Request $request)
    {
        $validator = Validator::make([
            'role_id' => $request->input('role_id'),
            'name' => $request->input('name'),
            'en_display_name' => $request->input('en_display_name'),
            'en_description' => $request->input('en_description'),
            'fr_display_name' => $request->input('fr_display_name'),
            'fr_description' => $request->input('fr_description'),
            'permission' => 'update-global-role',
        ], [
            'role_id' => 'required|integer|exists:global_role,id',
            'name' => 'string|max:50|unique:global_role,name',
            'en_display_name' => 'string|max:70',
            'en_description' => 'string|max:1000',
            'fr_display_name' => 'string|max:70',
            'fr_description' => 'string|max:1000',
            'permission' => new HasPermission(Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->updateGlobalRole(
            $request->input('role_id'),
            $request->input('name'),
            $request->input('en_display_name'),
            $request->input('en_description'),
            $request->input('fr_display_name'),
            $request->input('fr_description')
        ), 200);
    }

    public function updateLanRole(Request $request)
    {
        $validator = Validator::make([
            'role_id' => $request->input('role_id'),
            'name' => $request->input('name'),
            'en_display_name' => $request->input('en_display_name'),
            'en_description' => $request->input('en_description'),
            'fr_display_name' => $request->input('fr_display_name'),
            'fr_description' => $request->input('fr_description'),
            'permission' => 'update-lan-role',
        ], [
            'role_id' => 'required|exists:lan_role,id',
            'name' => 'string|max:50|unique:lan_role,name',
            'en_display_name' => 'string|max:70',
            'en_description' => 'string|max:1000',
            'fr_display_name' => 'string|max:70',
            'fr_description' => 'string|max:1000',
            'permission' => new HasPermissionInLan($request->input('role_id'), Auth::id())
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->updateLanRole(
            $request->input('role_id'),
            $request->input('name'),
            $request->input('en_display_name'),
            $request->input('en_description'),
            $request->input('fr_display_name'),
            $request->input('fr_description')
        ), 200);
    }
}
