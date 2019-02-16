<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetGlobalRolePermissionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $globalRole;
    protected $permissions;
    protected $accessRole;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
        $this->globalRole = factory('App\Model\GlobalRole')->create();
        $this->permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-global-role-permissions')
            ->take(3)
            ->get();

        foreach ($this->permissions as $permission) {
            factory('App\Model\PermissionGlobalRole')->create([
                'role_id' => $this->globalRole->id,
                'permission_id' => $permission->id
            ]);
        }

        $this->addGlobalPermissionToUser(
            $this->user->id,
            'get-global-role-permissions'
        );
    }

    public function testGetGlobalRolePermissions(): void
    {
        $this->actingAs($this->user)
            ->json('GET', 'http://' . env('API_DOMAIN') . '/role/global/permissions', [
                'role_id' => $this->globalRole->id
            ])
            ->seeJsonEquals([
                [
                    'id' => $this->permissions[0]['id'],
                    'name' => $this->permissions[0]['name'],
                    'can_be_per_lan' => (boolean)$this->permissions[0]['can_be_per_lan'],
                    'display_name' => trans('permission.display-name-' . $this->permissions[0]->name),
                    'description' => trans('permission.description-' . $this->permissions[0]->name)
                ],
                [
                    'id' => $this->permissions[1]['id'],
                    'name' => $this->permissions[1]['name'],
                    'can_be_per_lan' => (boolean)$this->permissions[1]['can_be_per_lan'],
                    'display_name' => trans('permission.display-name-' . $this->permissions[1]->name),
                    'description' => trans('permission.description-' . $this->permissions[1]->name)
                ],
                [
                    'id' => $this->permissions[2]['id'],
                    'name' => $this->permissions[2]['name'],
                    'can_be_per_lan' => (boolean)$this->permissions[2]['can_be_per_lan'],
                    'display_name' => trans('permission.display-name-' . $this->permissions[2]->name),
                    'description' => trans('permission.description-' . $this->permissions[2]->name)
                ]
            ])
            ->assertResponseStatus(200);
    }

    public function testGetGlobalRolePermissionsLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('GET', 'http://' . env('API_DOMAIN') . '/role/global/permissions', [
                'role_id' => $this->globalRole->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testGetGlobalRolePermissionsRoleIdRequired(): void
    {
        $this->actingAs($this->user)
            ->json('GET', 'http://' . env('API_DOMAIN') . '/role/global/permissions', [
                'role_id' => null
            ])
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

    public function testGetGlobalRolePermissionsRoleIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('GET', 'http://' . env('API_DOMAIN') . '/role/global/permissions', [
                'role_id' => '☭'
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
}
