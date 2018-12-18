<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetLanUsersTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;
    protected $lanRole;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->accessRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'get-lan-user-roles')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $this->accessRole,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $this->accessRole,
            'user_id' => $this->user->id
        ]);
    }

    public function testGetLanUsers(): void
    {
        $users = factory('App\Model\User', 4)->create();
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
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

        $this->actingAs($this->user)
            ->json('GET', '/api/role/lan/users', ['role_id' => $role->id])
            ->seeJsonEquals([
                [
                    'email' => $users[0]->email,
                    'first_name' => $users[0]->first_name,
                    'last_name' => $users[0]->last_name
                ], [
                    'email' => $users[1]->email,
                    'first_name' => $users[1]->first_name,
                    'last_name' => $users[1]->last_name
                ], [
                    'email' => $users[2]->email,
                    'first_name' => $users[2]->first_name,
                    'last_name' => $users[2]->last_name
                ], [
                    'email' => $users[3]->email,
                    'first_name' => $users[3]->first_name,
                    'last_name' => $users[3]->last_name
                ]
            ])
            ->assertResponseStatus(200);
    }

    public function testGetLanUsersHasPermission(): void
    {
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('GET', '/api/role/lan/users', ['role_id' => $role->id])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testGetLanUsersRoleIdRequired(): void
    {
        $this->actingAs($this->user)
            ->json('GET', '/api/role/lan/users', ['role_id' => null])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'role_id' => [
                        0 => 'The role id field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testGetLanUsersRoleIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('GET', '/api/role/lan/users', ['role_id' => '☭'])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'role_id' => [
                        0 => 'The role id must be an integer.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testGetLanUsersRoleIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('GET', '/api/role/lan/users', ['role_id' => -1])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'role_id' => [
                        0 => 'The selected role id is invalid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
