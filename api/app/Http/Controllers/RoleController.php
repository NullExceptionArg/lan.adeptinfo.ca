<?php

namespace App\Http\Controllers;

use App\Rules\General\ArrayOfInteger;
use App\Rules\LanRoleOncePerUser;
use App\Rules\Role\ElementsInArrayExistInPermission;
use App\Rules\Role\GlobalRoleOncePerUser;
use App\Rules\Role\HasPermissionInLan;
use App\Rules\Role\LanRoleNameOncePerLan;
use App\Rules\Role\PermissionsBelongToGlobalRole;
use App\Rules\Role\PermissionsBelongToLanRole;
use App\Rules\Role\PermissionsCanBePerLan;
use App\Rules\Role\PermissionsDontBelongToGlobalRole;
use App\Rules\Role\PermissionsDontBelongToLanRole;
use App\Rules\User\HasPermission;
use App\Services\Implementation\RoleServiceImpl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * Validation et application de la logique applicative sur les rôles.
 *
 * Class RoleController
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
     *
     * @param RoleServiceImpl $roleService
     */
    public function __construct(RoleServiceImpl $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#ajouter-des-permissions-a-un-role-global
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPermissionsGlobalRole(Request $request)
    {
        $validator = Validator::make([
            'role_id'     => $request->input('role_id'),
            'permissions' => $request->input('permissions'),
            'permission'  => 'add-permissions-global-role',
        ], [
            'role_id'     => 'required|integer|exists:global_role,id',
            'permissions' => [
                'required',
                'array',
                new ArrayOfInteger(),
                new ElementsInArrayExistInPermission(),
                new PermissionsDontBelongToGlobalRole($request->input('role_id')),
            ],
            'permission' => new HasPermission(Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->addPermissionsGlobalRole(
            $request->input('role_id'),
            $request->input('permissions')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#ajouter-des-permissions-a-un-role-de-lan
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPermissionsLanRole(Request $request)
    {
        $validator = Validator::make([
            'role_id'     => $request->input('role_id'),
            'permissions' => $request->input('permissions'),
            'permission'  => 'add-permissions-lan-role',
        ], [
            'role_id'     => 'required|integer|exists:lan_role,id',
            'permissions' => [
                'required',
                'array',
                new ArrayOfInteger(),
                new ElementsInArrayExistInPermission(),
                new PermissionsCanBePerLan(),
                new PermissionsDontBelongToLanRole($request->input('role_id')),
            ],
            'permission' => new HasPermissionInLan($request->input('role_id'), Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->addPermissionsLanRole(
            $request->input('role_id'),
            $request->input('permissions')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#assigner-un-role-global
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignGlobalRole(Request $request)
    {
        $validator = Validator::make([
            'email'      => $request->input('email'),
            'role_id'    => $request->input('role_id'),
            'permission' => 'assign-global-role',
        ], [
            'email'      => 'required|exists:user,email',
            'role_id'    => ['integer', 'exists:global_role,id', new GlobalRoleOncePerUser($request->input('email'))],
            'permission' => new HasPermission(Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->assignGlobalRole(
            $request->input('role_id'),
            $request->input('email')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#assigner-un-role-de-lan
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignLanRole(Request $request)
    {
        $validator = Validator::make([
            'email'      => $request->input('email'),
            'role_id'    => $request->input('role_id'),
            'permission' => 'assign-lan-role',
        ], [
            'email'      => 'required|exists:user,email',
            'role_id'    => ['integer', 'exists:lan_role,id', new LanRoleOncePerUser($request->input('email'))],
            'permission' => new HasPermissionInLan($request->input('role_id'), Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->assignLanRole(
            $request->input('role_id'),
            $request->input('email')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#creer-un-role-global
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createGlobalRole(Request $request)
    {
        $validator = Validator::make([
            'name'            => $request->input('name'),
            'en_display_name' => $request->input('en_display_name'),
            'en_description'  => $request->input('en_description'),
            'fr_display_name' => $request->input('fr_display_name'),
            'fr_description'  => $request->input('fr_description'),
            'permissions'     => $request->input('permissions'),
            'permission'      => 'create-global-role',
        ], [
            'name'            => 'required|string|max:50|unique:global_role,name',
            'en_display_name' => 'required|string|max:70',
            'en_description'  => 'required|string|max:1000',
            'fr_display_name' => 'required|string|max:70',
            'fr_description'  => 'required|string|max:1000',
            'permissions'     => ['required', 'array', new ArrayOfInteger(), new ElementsInArrayExistInPermission()],
            'permission'      => new HasPermission(Auth::id()),
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

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#creer-un-role-de-lan
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function createLanRole(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id'          => $request->input('lan_id'),
            'name'            => $request->input('name'),
            'en_display_name' => $request->input('en_display_name'),
            'en_description'  => $request->input('en_description'),
            'fr_display_name' => $request->input('fr_display_name'),
            'fr_description'  => $request->input('fr_description'),
            'permissions'     => $request->input('permissions'),
            'permission'      => 'create-lan-role',
        ], [
            'lan_id'          => 'integer|exists:lan,id,deleted_at,NULL',
            'name'            => ['required', 'string', 'max:50', new LanRoleNameOncePerLan($request->input('lan_id'))],
            'en_display_name' => 'required|string|max:70',
            'en_description'  => 'required|string|max:1000',
            'fr_display_name' => 'required|string|max:70',
            'fr_description'  => 'required|string|max:1000',
            'permissions'     => ['required', 'array', new ArrayOfInteger(), new ElementsInArrayExistInPermission(), new PermissionsCanBePerLan()],
            'permission'      => new HasPermissionInLan($request->input('lan_id'), Auth::id()),
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

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#supprimer-un-role-global
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteGlobalRole(Request $request)
    {
        $validator = Validator::make([
            'role_id'    => $request->input('role_id'),
            'permission' => 'delete-global-role',
        ], [
            'role_id'    => 'required|integer|exists:global_role,id',
            'permission' => new HasPermission(Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->deleteGlobalRole(
            $request->input('role_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#supprimer-un-role-de-lan
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteLanRole(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id'     => $request->input('lan_id'),
            'role_id'    => $request->input('role_id'),
            'permission' => 'delete-lan-role',
        ], [
            'lan_id'     => 'required|integer|exists:lan,id,deleted_at,NULL',
            'role_id'    => 'required|integer|exists:lan_role,id',
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->deleteLanRole(
            $request->input('role_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#supprimer-des-permissions-a-un-role-global
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePermissionsGlobalRole(Request $request)
    {
        $validator = Validator::make([
            'role_id'     => $request->input('role_id'),
            'permissions' => $request->input('permissions'),
            'permission'  => 'delete-permissions-global-role',
        ], [
            'role_id'     => 'required|integer|exists:global_role,id',
            'permissions' => [
                'required',
                'array',
                new ArrayOfInteger(),
                new PermissionsBelongToGlobalRole($request->input('role_id')),
            ],
            'permission' => new HasPermission(Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->deletePermissionsGlobalRole(
            $request->input('role_id'),
            $request->input('permissions')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#supprimer-des-permissions-a-un-role-de-lan
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePermissionsLanRole(Request $request)
    {
        $validator = Validator::make([
            'role_id'     => $request->input('role_id'),
            'permissions' => $request->input('permissions'),
            'permission'  => 'delete-permissions-lan-role',
        ], [
            'role_id'     => 'required|integer|exists:lan_role,id',
            'permissions' => [
                'required',
                'array',
                new ArrayOfInteger(),
                new PermissionsBelongToLanRole($request->input('role_id')),
            ],
            'permission' => new HasPermissionInLan($request->input('role_id'), Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->deletePermissionsLanRole(
            $request->input('role_id'),
            $request->input('permissions')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#obtenir-les-permissions-d-39-un-role-global
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGlobalRolePermissions(Request $request)
    {
        $validator = Validator::make([
            'role_id'    => $request->input('role_id'),
            'permission' => 'get-global-role-permissions',
        ], [
            'role_id'    => 'required|integer|exists:global_role,id',
            'permission' => new HasPermission(Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->getGlobalRolePermissions(
            $request->input('role_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#obtenir-les-roles-globaux
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGlobalRoles(Request $request)
    {
        $validator = Validator::make([
            'permission' => 'get-global-roles',
        ], [
            'permission' => new HasPermission(Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->getGlobalRoles(), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#obtenir-les-utilisateurs-possedants-un-role-global
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGlobalRoleUsers(Request $request)
    {
        $validator = Validator::make([
            'role_id'    => $request->input('role_id'),
            'permission' => 'get-global-user-roles',
        ], [
            'role_id'    => 'required|integer|exists:global_role,id',
            'permission' => new HasPermission(Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->getGlobalRoleUsers(
            $request->input('role_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#obtenir-les-permissions-d-39-un-role-de-lan
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLanRolePermissions(Request $request)
    {
        $validator = Validator::make([
            'role_id'    => $request->input('role_id'),
            'permission' => 'get-lan-role-permissions',
        ], [
            'role_id'    => 'required|integer|exists:lan_role,id',
            'permission' => new HasPermissionInLan($request->input('role_id'), Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->getLanRolePermissions(
            $request->input('role_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#obtenir-les-roles-de-lan
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLanRoles(Request $request)
    {
        $request = $this->adjustRequestForLan($request);
        $validator = Validator::make([
            'lan_id'     => $request->input('lan_id'),
            'permission' => 'get-lan-roles',
        ], [
            'lan_id'     => 'required|integer|exists:lan,id,deleted_at,NULL',
            'permission' => new HasPermissionInLan($request->input('lan_id'), Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->getLanRoles(
            $request->input('lan_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#obtenir-les-utilisateurs-possedants-un-role-de-lan
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLanRoleUsers(Request $request)
    {
        $validator = Validator::make([
            'role_id'    => $request->input('role_id'),
            'permission' => 'get-lan-user-roles',
        ], [
            'role_id'    => 'required|integer|exists:lan_role,id',
            'permission' => new HasPermissionInLan($request->input('role_id'), Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->getLanRoleUsers(
            $request->input('role_id')
        ), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#obtenir-les-permissions
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPermissions(Request $request)
    {
        $validator = Validator::make([
            'permission' => 'get-permissions',
        ], [
            'permission' => new HasPermission(Auth::id()),
        ]);

        $this->checkValidation($validator);

        return response()->json($this->roleService->getPermissions(), 200);
    }

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#modifier-un-role-global
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateGlobalRole(Request $request)
    {
        $validator = Validator::make([
            'role_id'         => $request->input('role_id'),
            'name'            => $request->input('name'),
            'en_display_name' => $request->input('en_display_name'),
            'en_description'  => $request->input('en_description'),
            'fr_display_name' => $request->input('fr_display_name'),
            'fr_description'  => $request->input('fr_description'),
            'permission'      => 'update-global-role',
        ], [
            'role_id'         => 'required|integer|exists:global_role,id',
            'name'            => 'string|max:50|unique:global_role,name',
            'en_display_name' => 'string|max:70',
            'en_description'  => 'string|max:1000',
            'fr_display_name' => 'string|max:70',
            'fr_description'  => 'string|max:1000',
            'permission'      => new HasPermission(Auth::id()),
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

    /**
     * @link https://adept-informatique.github.io/lan.adeptinfo.ca/#modifier-un-role-de-lan
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLanRole(Request $request)
    {
        $validator = Validator::make([
            'role_id'         => $request->input('role_id'),
            'name'            => $request->input('name'),
            'en_display_name' => $request->input('en_display_name'),
            'en_description'  => $request->input('en_description'),
            'fr_display_name' => $request->input('fr_display_name'),
            'fr_description'  => $request->input('fr_description'),
            'permission'      => 'update-lan-role',
        ], [
            'role_id'         => 'required|exists:lan_role,id',
            'name'            => 'string|max:50|unique:lan_role,name',
            'en_display_name' => 'string|max:70',
            'en_description'  => 'string|max:1000',
            'fr_display_name' => 'string|max:70',
            'fr_description'  => 'string|max:1000',
            'permission'      => new HasPermissionInLan($request->input('role_id'), Auth::id()),
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
