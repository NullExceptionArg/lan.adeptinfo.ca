<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteGlobalRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $globalRole;

    protected $requestContent = [
        'role_id' => null,
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
        $this->globalRole = factory('App\Model\GlobalRole')->create();

        $this->addGlobalPermissionToUser(
            $this->user->id,
            'delete-global-role'
        );

        $permissions = Permission::inRandomOrder()
            ->take(10)
            ->pluck('id')
            ->toArray();

        foreach ($permissions as $permissionId) {
            factory('App\Model\PermissionGlobalRole')->create([
                'role_id'       => $this->globalRole->id,
                'permission_id' => $permissionId,
            ]);
        }

        factory('App\Model\GlobalRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $this->globalRole->id,
        ]);

        $this->requestContent['role_id'] = $this->globalRole->id;
    }

    public function testDeleteGlobalRole(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/global', $this->requestContent)
            ->seeJsonEquals([
                'id'           => $this->globalRole->id,
                'name'         => $this->globalRole->name,
                'display_name' => $this->globalRole->en_display_name,
                'description'  => $this->globalRole->en_description,
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteGlobalRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/global', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testDeleteGlobalRoleIdRequired(): void
    {
        $this->requestContent['role_id'] = null;
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/global', $this->requestContent)
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

    public function testDeleteGlobalRoleIdInteger(): void
    {
        $this->requestContent['role_id'] = '☭';
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/global', $this->requestContent)
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

    public function testDeleteGlobalRoleIdExist(): void
    {
        $this->requestContent['role_id'] = -1;
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/role/global', $this->requestContent)
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
