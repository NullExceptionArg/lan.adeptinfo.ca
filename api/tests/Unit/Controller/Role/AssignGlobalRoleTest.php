<?php

namespace Tests\Unit\Controller\Role;

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

        $this->addGlobalPermissionToUser(
            $this->user->id,
            'assign-global-role'
        );
    }

    public function testAssignGlobalRole(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/assign', [
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

    public function testAssignGlobalRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/assign', [
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

    public function testAssignGlobalRoleEmailRequired(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/assign', [
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

    public function testAssignGlobalRoleEmailExist(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/assign', [
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

    public function testAssignGlobalRoleIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/assign', [
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

    public function testAssignGlobalRoleIdGlobalRoleOncePerUser(): void
    {
        factory('App\Model\GlobalRoleUser')->create([
            'role_id' => $this->role->id,
            'user_id' => $this->user->id,
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/assign', [
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

    public function testAssignGlobalRoleIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/global/assign', [
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
