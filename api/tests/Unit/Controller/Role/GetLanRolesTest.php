<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetLanRolesTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lanRoles;
    protected $lan;
    protected $accessRole;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRoles = factory('App\Model\LanRole', 3)->create([
            'lan_id' => $this->lan->id
        ]);

        $this->accessRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'get-lan-roles')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $this->accessRole,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $this->accessRole,
            'user_id' => $this->user->id
        ]);
    }

    public function testGetLanRolesTest(): void
    {
        $this->actingAs($this->user)
            ->json('GET', '/api/role/lan', ['lan_id' => $this->lan->id])
            ->seeJsonEquals([
                [
                    'name' => $this->lanRoles[0]->name,
                    'en_display_name' => $this->lanRoles[0]->en_display_name,
                    'en_description' => $this->lanRoles[0]->en_description,
                    'fr_display_name' => $this->lanRoles[0]->fr_display_name,
                    'fr_description' => $this->lanRoles[0]->fr_description,
                    'lan_id' => $this->lanRoles[0]->lan_id
                ],
                [
                    'name' => $this->lanRoles[1]->name,
                    'en_display_name' => $this->lanRoles[1]->en_display_name,
                    'en_description' => $this->lanRoles[1]->en_description,
                    'fr_display_name' => $this->lanRoles[1]->fr_display_name,
                    'fr_description' => $this->lanRoles[1]->fr_description,
                    'lan_id' => $this->lanRoles[1]->lan_id
                ],
                [
                    'name' => $this->lanRoles[2]->name,
                    'en_display_name' => $this->lanRoles[2]->en_display_name,
                    'en_description' => $this->lanRoles[2]->en_description,
                    'fr_display_name' => $this->lanRoles[2]->fr_display_name,
                    'fr_description' => $this->lanRoles[2]->fr_description,
                    'lan_id' => $this->lanRoles[2]->lan_id
                ],
                [
                    'name' => $this->accessRole->name,
                    'en_display_name' => $this->accessRole->en_display_name,
                    'en_description' => $this->accessRole->en_description,
                    'fr_display_name' => $this->accessRole->fr_display_name,
                    'fr_description' => $this->accessRole->fr_description,
                    'lan_id' => $this->accessRole->lan_id
                ]
            ])
            ->assertResponseStatus(200);
    }

    public function testGetLanRolesLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('GET', '/api/role/lan', ['lan_id' => $this->lan->id])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testCreateLanRoleLanIdExists(): void
    {
        $this->actingAs($this->user)
            ->json('GET', '/api/role/lan', ['lan_id' => -1])
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

    public function testCreateLanRoleLanIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('GET', '/api/role/lan', ['lan_id' => '☭'])
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
