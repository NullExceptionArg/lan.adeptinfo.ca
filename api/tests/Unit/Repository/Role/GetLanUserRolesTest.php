<?php

namespace Tests\Unit\Repository\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetLanUserRolesTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');
    }

    public function testGetLanUserRoles(): void
    {
        $users = factory('App\Model\User', 4)->create();
        $lan = factory('App\Model\Lan')->create();
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role,
            'permission_id' => $permission->id
        ]);
        foreach ($users as $user) {
            factory('App\Model\LanRoleUser')->create([
                'role_id' => $role,
                'user_id' => $user->id
            ]);
        }

        $result = $this->roleRepository->getLanUserRoles($role->id);

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
