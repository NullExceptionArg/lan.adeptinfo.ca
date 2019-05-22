<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetLanRolePermissionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $lanService;

    protected $user;
    protected $lan;
    protected $lanRole;
    protected $permissions;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id,
        ]);

        $this->permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-lan-role-permissions')
            ->where('can_be_per_lan', true)
            ->take(3)
            ->get();

        foreach ($this->permissions as $permission) {
            factory('App\Model\PermissionLanRole')->create([
                'role_id'       => $this->lanRole->id,
                'permission_id' => $permission->id,
            ]);
        }
    }

    public function testGetLanRolePermissions(): void
    {
        $result = $this->roleService->getLanRolePermissions($this->lanRole->id);
        $permissionsResult = $result->jsonSerialize();

        $this->assertEquals($this->permissions[0]['id'], $result[0]->id);
        $this->assertEquals($this->permissions[0]['name'], $result[0]->name);
        $this->assertEquals($this->permissions[0]['can_be_per_lan'], $result[0]->can_be_per_lan);
        $this->assertEquals(trans('permission.display-name-'.$this->permissions[0]['name']), $permissionsResult[0]['display_name']);
        $this->assertEquals(trans('permission.description-'.$this->permissions[0]['name']), $permissionsResult[0]['description']);

        $this->assertEquals($this->permissions[1]['id'], $result[1]->id);
        $this->assertEquals($this->permissions[1]['name'], $result[1]->name);
        $this->assertEquals($this->permissions[1]['can_be_per_lan'], $result[1]->can_be_per_lan);
        $this->assertEquals(trans('permission.display-name-'.$this->permissions[1]['name']), $permissionsResult[1]['display_name']);
        $this->assertEquals(trans('permission.description-'.$this->permissions[1]['name']), $permissionsResult[1]['description']);

        $this->assertEquals($this->permissions[2]['id'], $result[2]->id);
        $this->assertEquals($this->permissions[2]['name'], $result[2]->name);
        $this->assertEquals($this->permissions[2]['can_be_per_lan'], $result[2]->can_be_per_lan);
        $this->assertEquals(trans('permission.display-name-'.$this->permissions[2]['name']), $permissionsResult[2]['display_name']);
        $this->assertEquals(trans('permission.description-'.$this->permissions[2]['name']), $permissionsResult[2]['description']);
    }
}
