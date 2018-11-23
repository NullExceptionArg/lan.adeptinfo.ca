<?php

namespace App\Services\Implementation;

use App\Model\GlobalRole;
use App\Model\LanRole;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\RoleRepositoryImpl;
use App\Repositories\Implementation\UserRepositoryImpl;
use App\Rules\ArrayOfInteger;
use App\Rules\ElementsInArrayExistInPermission;
use App\Rules\HasPermission;
use App\Rules\HasPermissionInLan;
use App\Rules\PermissionsCanBePerLan;
use App\Rules\PermissionsDontBelongToRole;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class RoleServiceImpl implements RoleService
{
    protected $roleRepository;
    protected $lanRepository;
    protected $userRepository;

    /**
     * LanServiceImpl constructor.
     * @param RoleRepositoryImpl $roleRepository
     * @param LanRepositoryImpl $lanRepository
     * @param UserRepositoryImpl $userRepository
     */
    public function __construct(
        RoleRepositoryImpl $roleRepository,
        LanRepositoryImpl $lanRepository,
        UserRepositoryImpl $userRepository
    )
    {
        $this->roleRepository = $roleRepository;
        $this->lanRepository = $lanRepository;
        $this->userRepository = $userRepository;
    }

    public function createLanRole(Request $input): LanRole
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $roleValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'name' => $input->input('name'),
            'en_display_name' => $input->input('en_display_name'),
            'en_description' => $input->input('en_description'),
            'fr_display_name' => $input->input('fr_display_name'),
            'fr_description' => $input->input('fr_description'),
            'permissions' => $input->input('permissions'),
            'permission' => 'create-lan-role',
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'name' => 'required|string|max:50|unique:lan_role,name',
            'en_display_name' => 'required|string|max:70',
            'en_description' => 'required|string|max:1000',
            'fr_display_name' => 'required|string|max:70',
            'fr_description' => 'required|string|max:1000',
            'permissions' => ['required', 'array', new ArrayOfInteger, new ElementsInArrayExistInPermission, new PermissionsCanBePerLan],
            'permission' => new HasPermissionInLan($input->input('lan_id'), Auth::id())
        ]);

        if ($roleValidator->fails()) {
            throw new BadRequestHttpException($roleValidator->errors());
        }

        $role = $this->roleRepository->createLanRole(
            $input->input('lan_id'),
            $input->input('name'),
            $input->input('en_display_name'),
            $input->input('en_description'),
            $input->input('fr_display_name'),
            $input->input('fr_description')
        );

        foreach ($input->input('permissions') as $permissionId) {
            $this->roleRepository->linkPermissionIdLanRole($permissionId, $role);
        }

        return $role;
    }

    public function editLanRole(Request $input): LanRole
    {
        $role = null;
        if (is_int($input->input('role_id'))) {
            $role = $this->roleRepository->findLanRoleById($input->input('role_id'));
        }

        $roleValidator = Validator::make([
            'role_id' => $input->input('role_id'),
            'name' => $input->input('name'),
            'en_display_name' => $input->input('en_display_name'),
            'en_description' => $input->input('en_description'),
            'fr_display_name' => $input->input('fr_display_name'),
            'fr_description' => $input->input('fr_description'),
            'permission' => 'edit-lan-role',
        ], [
            'role_id' => 'required|exists:lan_role,id',
            'name' => 'string|max:50|unique:lan_role,name',
            'en_display_name' => 'string|max:70',
            'en_description' => 'string|max:1000',
            'fr_display_name' => 'string|max:70',
            'fr_description' => 'string|max:1000',
            'permission' => new HasPermissionInLan(is_null($role) ? null : $role->lan_id, Auth::id())
        ]);

        if ($roleValidator->fails()) {
            throw new BadRequestHttpException($roleValidator->errors());
        }

        $role = $this->roleRepository->findLanRoleById($input->input('role_id'));
        $role = $this->roleRepository->editLanRole(
            $role,
            $input->input('name'),
            $input->input('en_display_name'),
            $input->input('en_description'),
            $input->input('fr_display_name'),
            $input->input('fr_description')
        );

        return $role;
    }

    public function assignLanRole(Request $input): LanRole
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $roleValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'email' => $input->input('email'),
            'role_id' => $input->input('role_id'),
            'permission' => 'assign-lan-role',
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'email' => 'required|exists:user,email',
            'role_id' => 'integer|exists:lan_role,id',
            'permission' => new HasPermissionInLan($input->input('lan_id'), Auth::id())
        ]);

        if ($roleValidator->fails()) {
            throw new BadRequestHttpException($roleValidator->errors());
        }

        $role = $this->roleRepository->findLanRoleById($input->input('role_id'));
        $user = $this->userRepository->findByEmail($input->input('email'));

        $this->roleRepository->linkLanRoleUser($role, $user);

        return $role;
    }

    public function addPermissionsLanRole(Request $input): LanRole
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrent();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $roleValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'email' => $input->input('email'),
            'role_id' => $input->input('role_id'),
            'permissions' => $input->input('permissions'),
            'permission' => 'add-permissions-lan-role',
        ], [
            'lan_id' => 'integer|exists:lan,id,deleted_at,NULL',
            'email' => 'required|exists:user,email',
            'role_id' => 'integer|exists:lan_role,id',
            'permissions' => [
                'required',
                'array',
                new ArrayOfInteger,
                new ElementsInArrayExistInPermission,
                new PermissionsCanBePerLan,
                new PermissionsDontBelongToRole($input->input('role_id'))
            ],
            'permission' => new HasPermissionInLan($input->input('lan_id'), Auth::id())
        ]);

        if ($roleValidator->fails()) {
            throw new BadRequestHttpException($roleValidator->errors());
        }

        $role = $this->roleRepository->findLanRoleById($input->input('role_id'));
        $user = $this->userRepository->findByEmail($input->input('email'));

        $this->roleRepository->linkLanRoleUser($role, $user);

        return $role;
    }

    public function createGlobalRole(Request $input): GlobalRole
    {
        $roleValidator = Validator::make([
            'name' => $input->input('name'),
            'en_display_name' => $input->input('en_display_name'),
            'en_description' => $input->input('en_description'),
            'fr_display_name' => $input->input('fr_display_name'),
            'fr_description' => $input->input('fr_description'),
            'permissions' => $input->input('permissions'),
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

        if ($roleValidator->fails()) {
            throw new BadRequestHttpException($roleValidator->errors());
        }

        $role = $this->roleRepository->createGlobalRole(
            $input->input('name'),
            $input->input('en_display_name'),
            $input->input('en_description'),
            $input->input('fr_display_name'),
            $input->input('fr_description')
        );

        foreach ($input->input('permissions') as $permissionId) {
            $this->roleRepository->linkPermissionIdGlobalRole($permissionId, $role);
        }

        return $role;
    }

    public function editGlobalRole(Request $input): GlobalRole
    {
        $roleValidator = Validator::make([
            'role_id' => $input->input('role_id'),
            'name' => $input->input('name'),
            'en_display_name' => $input->input('en_display_name'),
            'en_description' => $input->input('en_description'),
            'fr_display_name' => $input->input('fr_display_name'),
            'fr_description' => $input->input('fr_description'),
            'permission' => 'edit-global-role',
        ], [
            'role_id' => 'required|exists:global_role,id',
            'name' => 'string|max:50|unique:global_role,name',
            'en_display_name' => 'string|max:70',
            'en_description' => 'string|max:1000',
            'fr_display_name' => 'string|max:70',
            'fr_description' => 'string|max:1000',
            'permission' => new HasPermission(Auth::id())
        ]);

        if ($roleValidator->fails()) {
            throw new BadRequestHttpException($roleValidator->errors());
        }

        $role = $this->roleRepository->findGlobalRoleById($input->input('role_id'));
        $role = $this->roleRepository->editGlobalRole(
            $role,
            $input->input('name'),
            $input->input('en_display_name'),
            $input->input('en_description'),
            $input->input('fr_display_name'),
            $input->input('fr_description')
        );

        return $role;
    }

    public function assignGlobalRole(Request $input): GlobalRole
    {
        $roleValidator = Validator::make([
            'email' => $input->input('email'),
            'role_id' => $input->input('role_id'),
            'permission' => 'assign-global-role',
        ], [
            'email' => 'required|exists:user,email',
            'role_id' => 'integer|exists:global_role,id',
            'permission' => new HasPermission(Auth::id())
        ]);

        if ($roleValidator->fails()) {
            throw new BadRequestHttpException($roleValidator->errors());
        }

        $role = $this->roleRepository->findGlobalRoleById($input->input('role_id'));
        $user = $this->userRepository->findByEmail($input->input('email'));

        $this->roleRepository->linkGlobalRoleUser($role, $user);

        return $role;
    }

}