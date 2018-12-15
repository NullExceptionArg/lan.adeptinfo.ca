<?php

namespace Tests\Unit\Controller\Role;

use App\Console\Commands\GeneratePermissions;
use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetPermissionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();

        $role = factory('App\Model\GlobalRole')->create();
        $permission = Permission::where('name', 'get-permissions')->first();
        factory('App\Model\PermissionGlobalRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);
    }

    public function testGetPermissions(): void
    {
        $response = $this->actingAs($this->user)
            ->json('GET', '/api/role/permissions');
        $response
            ->seeJsonStructure([[
                'id', 'name', 'can_be_per_lan', 'display_name', 'description'
            ]])
            ->assertResponseStatus(200);
        $this->assertEquals(count(GeneratePermissions::getPermissions()), count(json_decode($this->response->content())));

    }

    public function testGetPermissionsLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('GET', '/api/role/permissions')
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }
}
