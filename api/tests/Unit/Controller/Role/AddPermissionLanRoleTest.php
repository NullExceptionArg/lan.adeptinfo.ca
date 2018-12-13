<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddPermissionLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lanRole;
    protected $lan;

    protected $requestContent = [
        'lan_id' => null,
        'role_id' => null,
        'permissions' => null
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'add-permissions-lan-role')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->requestContent['lan_id'] = $this->lan->id;
        $this->requestContent['role_id'] = $this->lanRole->id;
        $this->requestContent['permissions'] = Permission::inRandomOrder()
            ->where('can_be_per_lan', true)
            ->take(5)
            ->pluck('id')
            ->toArray();
    }

    public function testAddPermissionLanRoleTest(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'lan_id' => $this->requestContent['lan_id'],
                'name' => $this->lanRole->name,
                'en_display_name' => $this->lanRole->en_display_name,
                'en_description' => $this->lanRole->en_description,
                'fr_display_name' => $this->lanRole->fr_display_name,
                'fr_description' => $this->lanRole->fr_description,
            ])
            ->assertResponseStatus(200);
    }

    public function testAddPermissionLanRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('POST', '/api/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testAddPermissionLanRoleLanIdExists(): void
    {
        $this->requestContent['lan_id'] = -1;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/permissions', $this->requestContent)
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

    public function testAddPermissionLanRoleLanIdInteger(): void
    {
        $this->requestContent['lan_id'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/permissions', $this->requestContent)
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

    public function testAddPermissionLanRoleIdRequired(): void
    {
        $this->requestContent['role_id'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/permissions', $this->requestContent)
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

    public function testAddPermissionLanRoleIdInteger(): void
    {
        $this->requestContent['role_id'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/permissions', $this->requestContent)
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

    public function testAddPermissionLanRolePermissionsRequired(): void
    {
        $this->requestContent['permissions'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/permissions', $this->requestContent)
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

    public function testAddPermissionLanRolePermissionsArray(): void
    {
        $this->requestContent['permissions'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/permissions', $this->requestContent)
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

    public function testAddPermissionLanRolePermissionsArrayOfInteger(): void
    {
        $this->requestContent['permissions'] = [(string)$this->requestContent['permissions'][0], $this->requestContent['permissions'][1]];
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/permissions', $this->requestContent)
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

    public function testAddPermissionLanRolePermissionCanBePerLan(): void
    {
        $permission = Permission::where('can_be_per_lan', false)->first();
        $this->requestContent['permissions'] = [intval($permission->id)];
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'permissions' => [
                        0 => 'One of the provided permissions cannot be attributed to a LAN role.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testAddPermissionLanRolePermissionsElementsInArrayExistInPermission(): void
    {
        $this->requestContent['permissions'] = [$this->requestContent['permissions'][0], -1];
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'permissions' => [
                        0 => 'An element of the array is not an existing permission id.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testAddPermissionLanRolePermissionsPermissionsDontBelongToRole(): void
    {
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $this->lanRole->id,
            'permission_id' => $this->requestContent['permissions'][0]
        ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan/permissions', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'permissions' => [
                        0 => 'One of the provided permissions is already attributed to this role.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
