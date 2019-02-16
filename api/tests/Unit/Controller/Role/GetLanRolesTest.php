<?php

namespace Tests\Unit\Controller\Role;

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

        $this->accessRole = $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'get-lan-roles'
        );
    }

    public function testGetLanRoles(): void
    {
        $this->actingAs($this->user)
            ->json('GET', 'http://' . env('API_DOMAIN') . '/role/lan', ['lan_id' => $this->lan->id])
            ->seeJsonEquals([
                [
                    'id' => $this->lanRoles[0]->id,
                    'name' => $this->lanRoles[0]->name,
                    'display_name' => $this->lanRoles[0]->en_display_name,
                    'description' => $this->lanRoles[0]->en_description,
                ],
                [
                    'id' => $this->lanRoles[1]->id,
                    'name' => $this->lanRoles[1]->name,
                    'display_name' => $this->lanRoles[1]->en_display_name,
                    'description' => $this->lanRoles[1]->en_description,
                ],
                [
                    'id' => $this->lanRoles[2]->id,
                    'name' => $this->lanRoles[2]->name,
                    'display_name' => $this->lanRoles[2]->en_display_name,
                    'description' => $this->lanRoles[2]->en_description,
                ],
                [
                    'id' => $this->accessRole->id,
                    'name' => $this->accessRole->name,
                    'display_name' => $this->accessRole->en_display_name,
                    'description' => $this->accessRole->en_description,
                ]
            ])
            ->assertResponseStatus(200);
    }

    public function testGetLanRolesLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('GET', 'http://' . env('API_DOMAIN') . '/role/lan', ['lan_id' => $this->lan->id])
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
            ->json('GET', 'http://' . env('API_DOMAIN') . '/role/lan', ['lan_id' => -1])
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
            ->json('GET', 'http://' . env('API_DOMAIN') . '/role/lan', ['lan_id' => '☭'])
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
