<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class DeletePermissionGlobalRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $globalRole;

    protected $paramsContent = [
        'role_id' => null,
        'permissions' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->globalRole = factory('App\Model\GlobalRole')->create();

        $role = factory('App\Model\GlobalRole')->create();
        $permission = Permission::where('name', 'delete-permissions-global-role')->first();
        factory('App\Model\PermissionGlobalRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->permissions = Permission::inRandomOrder()
            ->take(10)
            ->pluck('id')
            ->toArray();

        foreach ($this->permissions as $permissionId) {
            factory('App\Model\PermissionGlobalRole')->create([
                'role_id' => $this->globalRole->id,
                'permission_id' => $permissionId
            ]);
        }

        $this->paramsContent['role_id'] = $this->globalRole->id;
        $this->paramsContent['permissions'] = collect($this->permissions)->take(5)->toArray();

        $this->be($this->user);
    }

    public function testDeletePermissionGlobalRole(): void
    {
        $request = new Request($this->paramsContent);

        $result = $this->roleService->deletePermissionsGlobalRole($request);

        $this->assertEquals($this->globalRole->name, $result->name);
        $this->assertEquals($this->globalRole->en_display_name, $result->en_display_name);
        $this->assertEquals($this->globalRole->en_description, $result->en_description);
        $this->assertEquals($this->globalRole->fr_display_name, $result->fr_display_name);
        $this->assertEquals($this->globalRole->fr_description, $result->fr_description);
    }

    public function testDeletePermissionGlobalRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deletePermissionsGlobalRole($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testDeletePermissionGlobalRoleIdRequired(): void
    {
        $this->paramsContent['role_id'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deletePermissionsGlobalRole($request);
            $this->fail('Expected: {"role_id":["The role id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id field is required."]}', $e->getMessage());
        }
    }

    public function testDeletePermissionGlobalRoleIdInteger(): void
    {
        $this->paramsContent['role_id'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deletePermissionsGlobalRole($request);
            $this->fail('Expected: {"role_id":["The role id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id must be an integer."]}', $e->getMessage());
        }
    }

    public function testDeletePermissionGlobalRoleIdExist(): void
    {
        $this->paramsContent['role_id'] = -1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deletePermissionsGlobalRole($request);
            $this->fail('Expected: {"role_id":["The selected role id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The selected role id is invalid."]}', $e->getMessage());
        }
    }

    public function testDeletePermissionGlobalRolePermissionsRequired(): void
    {
        $this->paramsContent['permissions'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deletePermissionsGlobalRole($request);
            $this->fail('Expected: {"permissions":["The permissions field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The permissions field is required."]}', $e->getMessage());
        }
    }

    public function testDeletePermissionGlobalRolePermissionsArray(): void
    {
        $this->paramsContent['permissions'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deletePermissionsGlobalRole($request);
            $this->fail('Expected: {"permissions":["The permissions must be an array."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The permissions must be an array."]}', $e->getMessage());
        }
    }

    public function testDeletePermissionGlobalRolePermissionsArrayOfInteger(): void
    {
        $this->paramsContent['permissions'] = [(string)$this->paramsContent['permissions'][0], $this->paramsContent['permissions'][1]];
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deletePermissionsGlobalRole($request);
            $this->fail('Expected: {"permissions":["The array must contain only integers."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The array must contain only integers."]}', $e->getMessage());
        }
    }

    public function testDeletePermissionGlobalRolePermissionsPermissionsDontBelongToRole(): void
    {
        $this->paramsContent['permissions'] = collect($this->paramsContent['permissions'])->push(-1)->toArray();
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deletePermissionsGlobalRole($request);
            $this->fail('Expected: {"permissions":["One of the provided permissions is not attributed to this role."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["One of the provided permissions is not attributed to this role."]}', $e->getMessage());
        }
    }
}
