<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeletePermissionsGlobalRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $globalRole;
    protected $permissions;

    protected $requestContent = [
        'role_id'     => null,
        'permissions' => null,
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
        $this->globalRole = factory('App\Model\GlobalRole')->create();

        $this->addGlobalPermissionToUser(
            $this->user->id,
            'delete-permissions-global-role'
        );

        $this->permissions = Permission::inRandomOrder()
            ->take(10)
            ->pluck('id')
            ->toArray();

        foreach ($this->permissions as $permissionId) {
            factory('App\Model\PermissionGlobalRole')->create([
                'role_id'       => $this->globalRole->id,
                'permission_id' => $permissionId,
            ]);
        }

        $this->requestContent['role_id'] = $this->globalRole->id;
        $this->requestContent['permissions'] = collect($this->permissions)->take(5)->toArray();
    }

    public function testDeletePermissionsGlobalRole(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'id'           => $this->globalRole->id,
                'name'         => $this->globalRole->name,
                'display_name' => $this->globalRole->en_display_name,
                'description'  => $this->globalRole->en_description,
            ])
            ->assertResponseStatus(200);
    }

    public function testDeletePermissionsGlobalRoleHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testDeletePermissionsGlobalRoleIdRequired(): void
    {
        $this->requestContent['role_id'] = null;
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'role_id' => [
                        0 => 'The role id field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testDeletePermissionsGlobalRoleIdInteger(): void
    {
        $this->requestContent['role_id'] = '☭';
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
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

    public function testDeletePermissionsGlobalRoleIdExist(): void
    {
        $this->requestContent['role_id'] = -1;
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
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

    public function testDeletePermissionsGlobalRolePermissionsRequired(): void
    {
        $this->requestContent['permissions'] = null;
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'permissions' => [
                        0 => 'The permissions field is required.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testDeletePermissionsGlobalRolePermissionsArray(): void
    {
        $this->requestContent['permissions'] = 1;
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'permissions' => [
                        0 => 'The permissions must be an array.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testDeletePermissionsGlobalRolePermissionsArrayOfInteger(): void
    {
        $this->requestContent['permissions'] = [(string) $this->requestContent['permissions'][0], $this->requestContent['permissions'][1]];
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'permissions' => [
                        0 => 'The array must contain only integers.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testDeletePermissionsGlobalRolePermissionsPermissionsDontBelongToRole(): void
    {
        $permission = factory('App\Model\Permission')->create();
        $permission->delete();
        $this->requestContent['permissions'] = collect($this->requestContent['permissions'])
            ->push($permission->id)
            ->toArray();

        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/global/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'permissions' => [
                        0 => 'One of the provided permissions is not attributed to this role.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}
