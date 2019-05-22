<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddPermissionsLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lanRole;
    protected $lan;

    protected $requestContent = [
        'lan_id'      => null,
        'role_id'     => null,
        'permissions' => null,
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id,
        ]);

        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'add-permissions-lan-role'
        );

        $this->requestContent['lan_id'] = $this->lan->id;
        $this->requestContent['role_id'] = $this->lanRole->id;
        $this->requestContent['permissions'] = Permission::inRandomOrder()
            ->where('can_be_per_lan', true)
            ->take(5)
            ->pluck('id')
            ->toArray();
    }

    public function testAddPermissionsLanRole(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'id'           => $this->lanRole->id,
                'name'         => $this->lanRole->name,
                'display_name' => $this->lanRole->en_display_name,
                'description'  => $this->lanRole->en_description,
            ])
            ->assertResponseStatus(200);
    }

    public function testAddPermissionsLanRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testAddPermissionsLanRoleIdRequired(): void
    {
        $this->requestContent['role_id'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
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

    public function testAddPermissionsLanRoleIdInteger(): void
    {
        $this->requestContent['role_id'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
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

    public function testAddPermissionsLanRolePermissionsRequired(): void
    {
        $this->requestContent['permissions'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
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

    public function testAddPermissionsLanRolePermissionsArray(): void
    {
        $this->requestContent['permissions'] = 1;
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
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

    public function testAddPermissionsLanRolePermissionsArrayOfInteger(): void
    {
        $this->requestContent['permissions'] = [(string) $this->requestContent['permissions'][0], $this->requestContent['permissions'][1]];
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
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

    public function testAddPermissionsLanRolePermissionCanBePerLan(): void
    {
        $permission = Permission::where('can_be_per_lan', false)->first();
        $this->requestContent['permissions'] = [intval($permission->id)];
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'permissions' => [
                        0 => 'One of the provided permissions cannot be attributed to a LAN role.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testAddPermissionsLanRolePermissionsElementsInArrayExistInPermission(): void
    {
        $permission = factory('App\Model\Permission')->create();
        $permission->delete();
        $this->requestContent['permissions'] = [
            $this->requestContent['permissions'][0],
            $permission->id,
        ];
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'permissions' => [
                        0 => 'An element of the array is not an existing permission id.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testAddPermissionsLanRolePermissionsPermissionsDontBelongToRole(): void
    {
        factory('App\Model\PermissionLanRole')->create([
            'role_id'       => $this->lanRole->id,
            'permission_id' => $this->requestContent['permissions'][0],
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://'.env('API_DOMAIN').'/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'permissions' => [
                        0 => 'One of the provided permissions is already attributed to this role.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}
