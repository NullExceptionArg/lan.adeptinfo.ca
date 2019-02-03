<?php

namespace Tests\Unit\Service\User;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetAdminSummaryTest extends TestCase
{
    use DatabaseMigrations;

    protected $userService;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->app->make('App\Services\Implementation\UserServiceImpl');

        $this->user = factory('App\Model\User')->create();
    }

    public function testGetAdminSummary(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'admin-summary')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);
        $permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'admin-summary')
            ->take(8)
            ->get();
        $lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $globalRole = factory('App\Model\GlobalRole')->create();
        for ($i = 0; $i < 5; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $lanRole->id
            ]);
        }
        for ($i = 5; $i < 8; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $globalRole->id
            ]);
        }
        factory('App\Model\LanRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $lanRole->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $globalRole->id
        ]);

        $result = $this->userService->getAdminSummary($this->user->id, $lan->id)->jsonSerialize();
        $permissionsResult = $result['permissions']->jsonSerialize();

        $this->assertEquals($this->user->first_name, $result['first_name']);
        $this->assertEquals($this->user->last_name, $result['last_name']);

        $this->assertEquals($permission->id, $permissionsResult[0]['id']);
        $this->assertEquals($permission->name, $permissionsResult[0]['name']);
        $this->assertEquals($permission->can_be_per_lan, $permissionsResult[0]['can_be_per_lan']);
        $this->assertEquals(trans('permission.display-name-admin-summary'), $permissionsResult[0]['display_name']);
        $this->assertEquals(trans('permission.description-admin-summary'), $permissionsResult[0]['description']);

        $this->assertEquals($permissions[0]->id, $permissionsResult[1]['id']);
        $this->assertEquals($permissions[0]->name, $permissionsResult[1]['name']);
        $this->assertEquals((boolean)$permissions[0]->can_be_per_lan, $permissionsResult[1]['can_be_per_lan']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[0]->name), $permissionsResult[1]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[0]->name), $permissionsResult[1]['description']);

        $this->assertEquals($permissions[1]->id, $permissionsResult[2]['id']);
        $this->assertEquals($permissions[1]->name, $permissionsResult[2]['name']);
        $this->assertEquals((boolean)$permissions[1]->can_be_per_lan, $permissionsResult[2]['can_be_per_lan']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[1]->name), $permissionsResult[2]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[1]->name), $permissionsResult[2]['description']);

        $this->assertEquals($permissions[2]->id, $permissionsResult[3]['id']);
        $this->assertEquals($permissions[2]->name, $permissionsResult[3]['name']);
        $this->assertEquals((boolean)$permissions[2]->can_be_per_lan, $permissionsResult[3]['can_be_per_lan']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[2]->name), $permissionsResult[3]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[2]->name), $permissionsResult[3]['description']);

        $this->assertEquals($permissions[3]->id, $permissionsResult[4]['id']);
        $this->assertEquals($permissions[3]->name, $permissionsResult[4]['name']);
        $this->assertEquals((boolean)$permissions[3]->can_be_per_lan, $permissionsResult[4]['can_be_per_lan']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[3]->name), $permissionsResult[4]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[3]->name), $permissionsResult[4]['description']);

        $this->assertEquals($permissions[4]->id, $permissionsResult[5]['id']);
        $this->assertEquals($permissions[4]->name, $permissionsResult[5]['name']);
        $this->assertEquals((boolean)$permissions[4]->can_be_per_lan, $permissionsResult[5]['can_be_per_lan']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[4]->name), $permissionsResult[5]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[4]->name), $permissionsResult[5]['description']);

        $this->assertEquals($permissions[5]->id, $permissionsResult[6]['id']);
        $this->assertEquals($permissions[5]->name, $permissionsResult[6]['name']);
        $this->assertEquals((boolean)$permissions[5]->can_be_per_lan, $permissionsResult[6]['can_be_per_lan']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[5]->name), $permissionsResult[6]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[5]->name), $permissionsResult[6]['description']);

        $this->assertEquals($permissions[6]->id, $permissionsResult[7]['id']);
        $this->assertEquals($permissions[6]->name, $permissionsResult[7]['name']);
        $this->assertEquals((boolean)$permissions[6]->can_be_per_lan, $permissionsResult[7]['can_be_per_lan']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[6]->name), $permissionsResult[7]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[6]->name), $permissionsResult[7]['description']);

        $this->assertEquals($permissions[7]->id, $permissionsResult[8]['id']);
        $this->assertEquals($permissions[7]->name, $permissionsResult[8]['name']);
        $this->assertEquals((boolean)$permissions[7]->can_be_per_lan, $permissionsResult[8]['can_be_per_lan']);
        $this->assertEquals(trans('permission.display-name-' . $permissions[7]->name), $permissionsResult[8]['display_name']);
        $this->assertEquals(trans('permission.description-' . $permissions[7]->name), $permissionsResult[8]['description']);
    }
}
