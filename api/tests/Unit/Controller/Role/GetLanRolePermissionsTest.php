<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetLanRolePermissionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;
    protected $lanRole;
    protected $permissions;
    protected $accessRole;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);

        $this->permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-lan-role-permissions')
            ->where('can_be_per_lan', true)
            ->take(3)
            ->get();

        foreach ($this->permissions as $permission) {
            factory('App\Model\PermissionLanRole')->create([
                'role_id' => $this->lanRole->id,
                'permission_id' => $permission->id
            ]);
        }

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'get-lan-role-permissions')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);
    }

    public function testGetLanRolePermissions(): void
    {
        $this->actingAs($this->user)
            ->json('GET', '/api/role/lan/permissions', [
                'role_id' => $this->lanRole->id
            ])
            ->seeJsonEquals([
                [
                    'id' => $this->permissions[0]['id'],
                    'name' => $this->permissions[0]['name'],
                    'can_be_per_lan' => $this->permissions[0]['can_be_per_lan'],
                    'display_name' => trans('permission.display-name-' . $this->permissions[0]->name),
                    'description' => trans('permission.description-' . $this->permissions[0]->name)
                ],
                [
                    'id' => $this->permissions[1]['id'],
                    'name' => $this->permissions[1]['name'],
                    'can_be_per_lan' => $this->permissions[1]['can_be_per_lan'],
                    'display_name' => trans('permission.display-name-' . $this->permissions[1]->name),
                    'description' => trans('permission.description-' . $this->permissions[1]->name)
                ],
                [
                    'id' => $this->permissions[2]['id'],
                    'name' => $this->permissions[2]['name'],
                    'can_be_per_lan' => $this->permissions[2]['can_be_per_lan'],
                    'display_name' => trans('permission.display-name-' . $this->permissions[2]->name),
                    'description' => trans('permission.description-' . $this->permissions[2]->name)
                ]
            ])
            ->assertResponseStatus(200);
    }

    public function testGetLanRolePermissionsLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('GET', '/api/role/lan/permissions', [
                'role_id' => $this->lanRole->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testGetLanRolePermissionsRoleIdRequired(): void
    {
        $this->actingAs($this->user)
            ->json('GET', '/api/role/lan/permissions', [
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

    public function testGetLanRolePermissionsRoleIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('GET', '/api/role/lan/permissions', [
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
