<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeletePermissionGlobalRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $globalRole;
    protected $permissions;

    protected $requestContent = [
        'role_id' => null,
        'permissions' => null
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
        $this->globalRole = factory('App\Model\GlobalRole')->create();

        $role = factory('App\Model\GlobalRole')->create();
        $permission = Permission::where('name', 'delete-permissions-global-role')->first();
        factory('App\Model\PermissionGlobalRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->permissions = Permission::inRandomOrder()
            ->take(10)
            ->pluck('id')
            ->toArray();

        foreach ($this->permissions as $permissionId) {
            factory('App\Model\PermissionGlobalRole')->create([
                'role_id' => $this->globalRole->id,
                'permission_id' => $permissionId
            ]);
        }

        $this->requestContent['role_id'] = $this->globalRole->id;
        $this->requestContent['permissions'] = collect($this->permissions)->take(5)->toArray();
    }

    public function testUnlinkPermissionIdLanRole(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', '/api/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'name' => $this->globalRole->name,
                'en_display_name' => $this->globalRole->en_display_name,
                'en_description' => $this->globalRole->en_description,
                'fr_display_name' => $this->globalRole->fr_display_name,
                'fr_description' => $this->globalRole->fr_description,
            ])
            ->assertResponseStatus(200);
    }

    public function testDeletePermissionGlobalRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('DELETE', '/api/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testDeletePermissionGlobalRoleIdRequired(): void
    {
        $this->requestContent['role_id'] = null;
        $this->actingAs($this->user)
            ->json('DELETE', '/api/role/global/permissions', $this->requestContent)
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

    public function testDeletePermissionGlobalRoleIdInteger(): void
    {
        $this->requestContent['role_id'] = '☭';
        $this->actingAs($this->user)
            ->json('DELETE', '/api/role/global/permissions', $this->requestContent)
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

    public function testDeletePermissionGlobalRolePermissionsRequired(): void
    {
        $this->requestContent['permissions'] = null;
        $this->actingAs($this->user)
            ->json('DELETE', '/api/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'permissions' => [
                        0 => 'The permissions field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testDeletePermissionGlobalRolePermissionsArray(): void
    {
        $this->requestContent['permissions'] = 1;
        $this->actingAs($this->user)
            ->json('DELETE', '/api/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'permissions' => [
                        0 => 'The permissions must be an array.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testDeletePermissionGlobalRolePermissionsArrayOfInteger(): void
    {
        $this->requestContent['permissions'] = [(string)$this->requestContent['permissions'][0], $this->requestContent['permissions'][1]];
        $this->actingAs($this->user)
            ->json('DELETE', '/api/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'permissions' => [
                        0 => 'The array must contain only integers.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testDeletePermissionGlobalRolePermissionsPermissionsDontBelongToRole(): void
    {

        $this->requestContent['permissions'] = collect($this->requestContent['permissions'])->push(-1)->toArray();
        $this->actingAs($this->user)
            ->json('DELETE', '/api/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'permissions' => [
                        0 => 'One of the provided permissions is not attributed to this role.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
