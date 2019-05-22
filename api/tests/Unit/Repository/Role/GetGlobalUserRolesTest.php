<?php

namespace Tests\Unit\Repository\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetGlobalUserRolesTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');
    }

    public function testGetGlobalUserRoles(): void
    {
        $users = factory('App\Model\User', 4)->create();
        $role = factory('App\Model\GlobalRole')->create();
        $permission = Permission::first();
        factory('App\Model\PermissionGlobalRole')->create([
            'role_id'       => $role,
            'permission_id' => $permission->id,
        ]);
        foreach ($users as $user) {
            factory('App\Model\GlobalRoleUser')->create([
                'role_id' => $role,
                'user_id' => $user->id,
            ]);
        }

        $result = $this->roleRepository->getGlobalUserRoles($role->id);

        $this->assertEquals($users[0]['email'], $result[0]->email);
        $this->assertEquals($users[0]['first_name'], $result[0]->first_name);
        $this->assertEquals($users[0]['last_name'], $result[0]->last_name);

        $this->assertEquals($users[1]['email'], $result[1]->email);
        $this->assertEquals($users[1]['first_name'], $result[1]->first_name);
        $this->assertEquals($users[1]['last_name'], $result[1]->last_name);

        $this->assertEquals($users[2]['email'], $result[2]->email);
        $this->assertEquals($users[2]['first_name'], $result[2]->first_name);
        $this->assertEquals($users[2]['last_name'], $result[2]->last_name);

        $this->assertEquals($users[3]['email'], $result[3]->email);
        $this->assertEquals($users[3]['first_name'], $result[3]->first_name);
        $this->assertEquals($users[3]['last_name'], $result[3]->last_name);
    }
}
