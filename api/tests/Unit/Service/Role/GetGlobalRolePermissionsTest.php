<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetGlobalRolePermissionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $globalRole;
    protected $permissions;
    protected $accessRole;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->globalRole = factory('App\Model\GlobalRole')->create();
        $this->permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-global-role-permissions')
            ->take(3)
            ->get();

        foreach ($this->permissions as $permission) {
            factory('App\Model\PermissionGlobalRole')->create([
                'role_id' => $this->globalRole->id,
                'permission_id' => $permission->id
            ]);
        }

        $this->be($this->user);
    }

    public function testGetGlobalRolePermissions(): void
    {
        $result = $this->roleService->getGlobalRolePermissions($this->globalRole->id);
        $permissionsResult = $result->jsonSerialize();

        $this->assertEquals($this->permissions[0]['id'], $result[0]->id);
        $this->assertEquals($this->permissions[0]['name'], $result[0]->name);
        $this->assertEquals($this->permissions[0]['can_be_per_lan'], $result[0]->can_be_per_lan);
        $this->assertEquals(trans('permission.display-name-' . $this->permissions[0]['name']), $permissionsResult[0]['display_name']);
        $this->assertEquals(trans('permission.description-' . $this->permissions[0]['name']), $permissionsResult[0]['description']);

        $this->assertEquals($this->permissions[1]['id'], $result[1]->id);
        $this->assertEquals($this->permissions[1]['name'], $result[1]->name);
        $this->assertEquals($this->permissions[1]['can_be_per_lan'], $result[1]->can_be_per_lan);
        $this->assertEquals(trans('permission.display-name-' . $this->permissions[1]['name']), $permissionsResult[1]['display_name']);
        $this->assertEquals(trans('permission.description-' . $this->permissions[1]['name']), $permissionsResult[1]['description']);

        $this->assertEquals($this->permissions[2]['id'], $result[2]->id);
        $this->assertEquals($this->permissions[2]['name'], $result[2]->name);
        $this->assertEquals($this->permissions[2]['can_be_per_lan'], $result[2]->can_be_per_lan);
        $this->assertEquals(trans('permission.display-name-' . $this->permissions[2]['name']), $permissionsResult[2]['display_name']);
        $this->assertEquals(trans('permission.description-' . $this->permissions[2]['name']), $permissionsResult[2]['description']);
    }
}
