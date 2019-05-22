<?php

namespace Tests\Unit\Controller\Role;

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
            'lan_id' => $this->lan->id,
        ]);

        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'assign-lan-role'
        );
    }

    public function testAssignLanRole(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/assign', [
                'role_id' => $this->role->id,
                'email'   => $this->user->email,
            ])
            ->seeJsonEquals([
                'id'           => $this->role->id,
                'name'         => $this->role->name,
                'display_name' => $this->role->en_display_name,
                'description'  => $this->role->en_description,
            ])
            ->assertResponseStatus(200);
    }

    public function testAssignLanRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/assign', [
                'role_id' => $this->role->id,
                'email'   => $this->user->email,
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testAssignLanRoleEmailRequired(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/assign', [
                'role_id' => $this->role->id,
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'email' => [
                        0 => 'The email field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testAssignLanRoleEmailExist(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/assign', [
                'role_id' => $this->role->id,
                'email'   => '☭',
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'email' => [
                        0 => 'The selected email is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testAssignLanRoleIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/assign', [
                'role_id' => '☭',
                'email'   => $this->user->email,
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'role_id' => [
                        0 => 'The role id must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testAssignGlobalRoleIdLanRoleOncePerUser(): void
    {
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $this->role->id,
            'user_id' => $this->user->id,
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/assign', [
                'role_id' => $this->role->id,
                'email'   => $this->user->email,
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'role_id' => [
                        0 => 'The user already has this role.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testAssignLanRoleIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/assign', [
                'role_id' => -1,
                'email'   => $this->user->email,
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'role_id' => [
                        0 => 'The selected role id is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}
