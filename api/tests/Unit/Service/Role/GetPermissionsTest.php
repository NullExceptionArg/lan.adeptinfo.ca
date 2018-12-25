<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetPermissionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();

        $role = factory('App\Model\GlobalRole')->create();
        $permission = Permission::where('name', 'get-permissions')->first();
        factory('App\Model\PermissionGlobalRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->be($this->user);
    }

    public function testGetPermissions(): void
    {
        $result = $this->roleService->getPermissions();
        $arrayResults = $result->collection->jsonSerialize();
        $permissions = include(base_path() . '/resources/permissions.php');
        for ($i = 0; $i < count($permissions); $i++) {
            $this->assertNotNull($arrayResults[$i]['id']);
            $this->assertEquals($permissions[$i]['name'], $arrayResults[$i]['name']);
            $this->assertEquals($permissions[$i]['can_be_per_lan'], (bool)$arrayResults[$i]['can_be_per_lan']);
            $this->assertEquals(trans('permission.display-name-' . $permissions[$i]['name']), $arrayResults[$i]['display_name']);
            $this->assertEquals(trans('permission.description-' . $permissions[$i]['name']), $arrayResults[$i]['description']);
        }
    }

    public function testGetPermissionsLanHasPermission(): void
    {
        $user = $this->user = factory('App\Model\User')->create();
        $this->be($user);
        try {
            $this->roleService->getPermissions();
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }
}
