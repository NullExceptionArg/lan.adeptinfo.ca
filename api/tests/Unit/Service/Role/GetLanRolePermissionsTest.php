<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetLanRolePermissionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanService;

    protected $user;
    protected $lan;
    protected $lanRole;
    protected $permissions;
    protected $accessRole;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);

        $this->permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-lan-role-permissions')
            ->where('can_be_per_lan', true)
            ->take(3)
            ->get();

        foreach ($this->permissions as $permission) {
            factory('App\Model\PermissionLanRole')->create([
                'role_id' => $this->lanRole->id,
                'permission_id' => $permission->id
            ]);
        }

        $this->accessRole = factory('App\Model\GlobalRole')->create();
        $permission = Permission::where('name', 'get-lan-role-permissions')->first();
        factory('App\Model\PermissionGlobalRole')->create([
            'role_id' => $this->accessRole->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'role_id' => $this->accessRole->id,
            'user_id' => $this->user->id
        ]);

        $this->be($this->user);
    }

    public function testGetLanRolePermissions(): void
    {
        $request = new Request(['role_id' => $this->lanRole->id]);

        $result = $this->roleService->getLanRolePermissions($request);
        $permissionsResult = $result->jsonSerialize();

        $this->assertEquals($this->permissions[0]['id'], $result[0]->id);
        $this->assertEquals($this->permissions[0]['name'], $result[0]->name);
        $this->assertEquals(trans('permission.display-name-' . $this->permissions[0]['name']), $permissionsResult[0]['display_name']);
        $this->assertEquals(trans('permission.description-' . $this->permissions[0]['name']), $permissionsResult[0]['description']);

        $this->assertEquals($this->permissions[1]['id'], $result[1]->id);
        $this->assertEquals($this->permissions[1]['name'], $result[1]->name);
        $this->assertEquals(trans('permission.display-name-' . $this->permissions[1]['name']), $permissionsResult[1]['display_name']);
        $this->assertEquals(trans('permission.description-' . $this->permissions[1]['name']), $permissionsResult[1]['description']);

        $this->assertEquals($this->permissions[2]['id'], $result[2]->id);
        $this->assertEquals($this->permissions[2]['name'], $result[2]->name);
        $this->assertEquals(trans('permission.display-name-' . $this->permissions[2]['name']), $permissionsResult[2]['display_name']);
        $this->assertEquals(trans('permission.description-' . $this->permissions[2]['name']), $permissionsResult[2]['description']);
    }

    public function testGetLanRolePermissionsLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request(['role_id' => $this->lanRole->id]);
        try {
            $this->roleService->getLanRolePermissions($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testGetLanRolePermissionsRoleIdRequired(): void
    {
        $request = new Request(['role_id' => null]);
        try {
            $this->roleService->getLanRolePermissions($request);
            $this->fail('Expected: {"role_id":["The role id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id field is required."]}', $e->getMessage());
        }
    }

    public function testGetLanRolePermissionsRoleIdExist(): void
    {
        $request = new Request(['role_id' => '☭']);
        try {
            $this->roleService->getLanRolePermissions($request);
            $this->fail('Expected: {"role_id":["The role id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id must be an integer."]}', $e->getMessage());
        }
    }
}
