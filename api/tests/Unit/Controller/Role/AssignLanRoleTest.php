<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AssignLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $role;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();

        $this->lan = factory('App\Model\Lan')->create();
        $this->user = factory('App\Model\User')->create();
        $this->role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'assign-lan-role')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);
    }

    public function testAssignLanRole(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/assign', [
                'role_id' => $this->role->id,
                'email' => $this->user->email,
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                'name' => $this->role->name,
                'en_display_name' => $this->role->en_display_name,
                'en_description' => $this->role->en_description,
                'fr_display_name' => $this->role->fr_display_name,
                'fr_description' => $this->role->fr_description,
                'lan_id' => $this->lan->id
            ])
            ->assertResponseStatus(200);
    }

    public function testAssignLanRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('POST', '/api/role/lan/assign', [
                'role_id' => $this->role->id,
                'email' => $this->user->email,
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testAssignLanRoleEmailRequired(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/assign', [
                'role_id' => $this->role->id,
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'email' => [
                        0 => 'The email field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testAssignLanRoleEmailExist(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/assign', [
                'role_id' => $this->role->id,
                'email' => '☭',
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'email' => [
                        0 => 'The selected email is invalid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testAssignLanRoleIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/assign', [
                'role_id' => '☭',
                'email' => $this->user->email,
                'lan_id' => $this->lan->id
            ])
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

    public function testAssignLanRoleIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/assign', [
                'role_id' => -1,
                'email' => $this->user->email,
                'lan_id' => $this->lan->id
            ])
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

    public function testAssignLanIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/assign', [
                'role_id' => $this->role->id,
                'email' => $this->user->email,
                'lan_id' => -1
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The selected lan id is invalid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testAssignLanIdRequired(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/assign', [
                'role_id' => $this->role->id,
                'email' => $this->user->email,
                'lan_id' => '☭'
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The lan id must be an integer.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}