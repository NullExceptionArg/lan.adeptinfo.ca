<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetGlobalRolesTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $globalRoles;
    protected $accessRole;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
        $this->globalRoles = factory('App\Model\GlobalRole', 3)->create();

        $this->accessRole = factory('App\Model\GlobalRole')->create();
        $permission = Permission::where('name', 'get-global-roles')->first();
        factory('App\Model\PermissionGlobalRole')->create([
            'role_id' => $this->accessRole->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'role_id' => $this->accessRole->id,
            'user_id' => $this->user->id
        ]);
    }

    public function testGetGlobalRoles(): void
    {
        $this->actingAs($this->user)
            ->json('GET', '/api/role/global')
            ->seeJsonEquals([
                [
                    'name' => $this->globalRoles[0]->name,
                    'en_display_name' => $this->globalRoles[0]->en_display_name,
                    'en_description' => $this->globalRoles[0]->en_description,
                    'fr_display_name' => $this->globalRoles[0]->fr_display_name,
                    'fr_description' => $this->globalRoles[0]->fr_description,
                ],
                [
                    'name' => $this->globalRoles[1]->name,
                    'en_display_name' => $this->globalRoles[1]->en_display_name,
                    'en_description' => $this->globalRoles[1]->en_description,
                    'fr_display_name' => $this->globalRoles[1]->fr_display_name,
                    'fr_description' => $this->globalRoles[1]->fr_description,
                ],
                [
                    'name' => $this->globalRoles[2]->name,
                    'en_display_name' => $this->globalRoles[2]->en_display_name,
                    'en_description' => $this->globalRoles[2]->en_description,
                    'fr_display_name' => $this->globalRoles[2]->fr_display_name,
                    'fr_description' => $this->globalRoles[2]->fr_description,
                ],
                [
                    'name' => $this->accessRole->name,
                    'en_display_name' => $this->accessRole->en_display_name,
                    'en_description' => $this->accessRole->en_description,
                    'fr_display_name' => $this->accessRole->fr_display_name,
                    'fr_description' => $this->accessRole->fr_description,
                ]
            ])
            ->assertResponseStatus(200);
    }

    public function testGetGlobalRolesLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('GET', '/api/role/global')
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }
}
