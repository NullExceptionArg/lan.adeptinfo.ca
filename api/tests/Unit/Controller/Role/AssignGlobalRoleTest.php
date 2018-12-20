<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AssignGlobalRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $role;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
        $this->role = factory('App\Model\GlobalRole')->create();

        $role = factory('App\Model\GlobalRole')->create();
        $permission = Permission::where('name', 'assign-global-role')->first();
        factory('App\Model\PermissionGlobalRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);
    }

    public function testAssignGlobalRole(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/role/global/assign', [
                'role_id' => $this->role->id,
                'email' => $this->user->email
            ])
            ->seeJsonEquals([
                'name' => $this->role->name,
                'en_display_name' => $this->role->en_display_name,
                'en_description' => $this->role->en_description,
                'fr_display_name' => $this->role->fr_display_name,
                'fr_description' => $this->role->fr_description
            ])
            ->assertResponseStatus(200);
    }

    public function testAssignGlobalRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('POST', '/api/role/global/assign', [
                'role_id' => $this->role->id,
                'email' => $this->user->email
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testAssignGlobalRoleEmailRequired(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/role/global/assign', [
                'role_id' => $this->role->id
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

    public function testAssignGlobalRoleEmailExist(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/role/global/assign', [
                'role_id' => $this->role->id,
                'email' => '☭'
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

    public function testAssignGlobalRoleIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/role/global/assign', [
                'role_id' => '☭',
                'email' => $this->user->email
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

    public function testAssignGlobalRoleIdGlobalRoleOncePerUser(): void
    {
        factory('App\Model\GlobalRoleUser')->create([
            'role_id' => $this->role->id,
            'user_id' => $this->user->id
        ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/role/global/assign', [
                'role_id' => $this->role->id,
                'email' => $this->user->email
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'role_id' => [
                        0 => 'The user already has this role.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testAssignGlobalRoleIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/role/global/assign', [
                'role_id' => -1,
                'email' => $this->user->email
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
}
