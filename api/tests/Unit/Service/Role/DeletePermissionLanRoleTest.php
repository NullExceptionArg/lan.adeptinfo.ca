<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class DeletePermissionLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $lanRole;
    protected $lan;
    protected $permissions;

    protected $paramsContent = [
        'role_id' => null,
        'permissions' => null,
        'lan_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'delete-permissions-lan-role')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->permissions = Permission::inRandomOrder()
            ->where('can_be_per_lan', true)
            ->take(5)
            ->pluck('id')
            ->toArray();

        foreach ($this->permissions as $permissionId) {
            factory('App\Model\PermissionLanRole')->create([
                'role_id' => $this->lanRole->id,
                'permission_id' => $permissionId
            ]);
        }

        $this->paramsContent['role_id'] = $this->lanRole->id;
        $this->paramsContent['lan_id'] = $this->lan->id;
        $this->paramsContent['permissions'] = collect($this->permissions)->take(5)->toArray();

        $this->be($this->user);
    }

    public function testDeletePermissionLanRole(): void
    {
        $request = new Request($this->paramsContent);

        $result = $this->roleService->deletePermissionsLanRole($request);

        $this->assertEquals($this->lanRole->name, $result->name);
        $this->assertEquals($this->lanRole->en_display_name, $result->en_display_name);
        $this->assertEquals($this->lanRole->en_description, $result->en_description);
        $this->assertEquals($this->lanRole->fr_display_name, $result->fr_display_name);
        $this->assertEquals($this->lanRole->fr_description, $result->fr_description);
    }

    public function testDeletePermissionLanRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deletePermissionsLanRole($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testDeletePermissionLanRoleIdRequired(): void
    {
        $this->paramsContent['role_id'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deletePermissionsLanRole($request);
            $this->fail('Expected: {"role_id":["The role id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id field is required."]}', $e->getMessage());
        }
    }

    public function testDeletePermissionLanRoleIdInteger(): void
    {
        $this->paramsContent['role_id'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deletePermissionsLanRole($request);
            $this->fail('Expected: {"role_id":["The role id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id must be an integer."]}', $e->getMessage());
        }
    }

    public function testDeletePermissionLanRolePermissionsRequired(): void
    {
        $this->paramsContent['permissions'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deletePermissionsLanRole($request);
            $this->fail('Expected: {"permissions":["The permissions field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The permissions field is required."]}', $e->getMessage());
        }
    }

    public function testDeletePermissionLanRolePermissionsArray(): void
    {
        $this->paramsContent['permissions'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deletePermissionsLanRole($request);
            $this->fail('Expected: {"permissions":["The permissions must be an array."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The permissions must be an array."]}', $e->getMessage());
        }
    }

    public function testDeletePermissionLanRolePermissionsArrayOfInteger(): void
    {
        $this->paramsContent['permissions'] = [(string)$this->paramsContent['permissions'][0], $this->paramsContent['permissions'][1]];
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deletePermissionsLanRole($request);
            $this->fail('Expected: {"permissions":["The array must contain only integers."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The array must contain only integers."]}', $e->getMessage());
        }
    }

    public function testDeletePermissionLanRolePermissionsPermissionsDontBelongToRole(): void
    {
        $this->paramsContent['permissions'] = collect($this->paramsContent['permissions'])->push(-1)->toArray();
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->deletePermissionsLanRole($request);
            $this->fail('Expected: {"permissions":["One of the provided permissions is not attributed to this role."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["One of the provided permissions is not attributed to this role."]}', $e->getMessage());
        }
    }
}
