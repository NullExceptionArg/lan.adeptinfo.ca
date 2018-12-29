<?php

namespace Tests\Unit\Controller\User;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetAdminSummaryTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
    }

    public function testGetAdminSummaryHasPermission(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $admin = factory('App\Model\User')->create();
        $this->actingAs($admin)
            ->json('GET', '/api/admin/summary', [
                'lan_id' => $lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testGetAdminSummary(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'admin-summary')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);
        $permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'admin-summary')
            ->take(8)
            ->get();
        $lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $globalRole = factory('App\Model\GlobalRole')->create();
        for ($i = 0; $i < 5; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $lanRole->id
            ]);
        }
        for ($i = 5; $i < 8; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $globalRole->id
            ]);
        }
        factory('App\Model\LanRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $lanRole->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $globalRole->id
        ]);
        $this->actingAs($this->user)
            ->json('GET', '/api/admin/summary', [
                'lan_id' => $lan->id
            ])
            ->seeJsonEquals([
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'has_tournaments' => false,
                'permissions' => [
                    [
                        'id' => $permission->id,
                        'name' => 'admin-summary',
                        'can_be_per_lan' => (boolean)$permission->can_be_per_lan,
                        'display_name' => trans('permission.display-name-admin-summary'),
                        'description' => trans('permission.description-admin-summary')
                    ],
                    [
                        'id' => $permissions[0]->id,
                        'name' => $permissions[0]->name,
                        'can_be_per_lan' => (boolean)$permissions[0]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[0]->name),
                        'description' => trans('permission.description-' . $permissions[0]->name)
                    ],
                    [
                        'id' => $permissions[1]->id,
                        'name' => $permissions[1]->name,
                        'can_be_per_lan' => (boolean)$permissions[1]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[1]->name),
                        'description' => trans('permission.description-' . $permissions[1]->name)
                    ],
                    [
                        'id' => $permissions[2]->id,
                        'name' => $permissions[2]->name,
                        'can_be_per_lan' => (boolean)$permissions[2]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[2]->name),
                        'description' => trans('permission.description-' . $permissions[2]->name)
                    ],
                    [
                        'id' => $permissions[3]->id,
                        'name' => $permissions[3]->name,
                        'can_be_per_lan' => (boolean)$permissions[3]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[3]->name),
                        'description' => trans('permission.description-' . $permissions[3]->name)
                    ],
                    [
                        'id' => $permissions[4]->id,
                        'name' => $permissions[4]->name,
                        'can_be_per_lan' => (boolean)$permissions[4]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[4]->name),
                        'description' => trans('permission.description-' . $permissions[4]->name)
                    ],
                    [
                        'id' => $permissions[5]->id,
                        'name' => $permissions[5]->name,
                        'can_be_per_lan' => (boolean)$permissions[5]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[5]->name),
                        'description' => trans('permission.description-' . $permissions[5]->name)
                    ],
                    [
                        'id' => $permissions[6]->id,
                        'name' => $permissions[6]->name,
                        'can_be_per_lan' => (boolean)$permissions[6]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[6]->name),
                        'description' => trans('permission.description-' . $permissions[6]->name)
                    ],
                    [
                        'id' => $permissions[7]->id,
                        'name' => $permissions[7]->name,
                        'can_be_per_lan' => (boolean)$permissions[7]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[7]->name),
                        'description' => trans('permission.description-' . $permissions[7]->name)
                    ],
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetAdminSummaryCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'admin-summary')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);
        $permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'admin-summary')
            ->take(8)
            ->get();
        $lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $globalRole = factory('App\Model\GlobalRole')->create();
        for ($i = 0; $i < 5; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $lanRole->id
            ]);
        }
        for ($i = 5; $i < 8; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $globalRole->id
            ]);
        }
        factory('App\Model\LanRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $lanRole->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $globalRole->id
        ]);
        $this->actingAs($this->user)
            ->json('GET', '/api/admin/summary', [
                'lan_id' => $lan->id
            ])
            ->seeJsonEquals([
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'has_tournaments' => false,
                'permissions' => [
                    [
                        'id' => $permission->id,
                        'name' => 'admin-summary',
                        'can_be_per_lan' => (boolean)$permission->can_be_per_lan,
                        'display_name' => trans('permission.display-name-admin-summary'),
                        'description' => trans('permission.description-admin-summary')
                    ],
                    [
                        'id' => $permissions[0]->id,
                        'name' => $permissions[0]->name,
                        'can_be_per_lan' => (boolean)$permissions[0]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[0]->name),
                        'description' => trans('permission.description-' . $permissions[0]->name)
                    ],
                    [
                        'id' => $permissions[1]->id,
                        'name' => $permissions[1]->name,
                        'can_be_per_lan' => (boolean)$permissions[1]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[1]->name),
                        'description' => trans('permission.description-' . $permissions[1]->name)
                    ],
                    [
                        'id' => $permissions[2]->id,
                        'name' => $permissions[2]->name,
                        'can_be_per_lan' => (boolean)$permissions[2]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[2]->name),
                        'description' => trans('permission.description-' . $permissions[2]->name)
                    ],
                    [
                        'id' => $permissions[3]->id,
                        'name' => $permissions[3]->name,
                        'can_be_per_lan' => (boolean)$permissions[3]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[3]->name),
                        'description' => trans('permission.description-' . $permissions[3]->name)
                    ],
                    [
                        'id' => $permissions[4]->id,
                        'name' => $permissions[4]->name,
                        'can_be_per_lan' => (boolean)$permissions[4]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[4]->name),
                        'description' => trans('permission.description-' . $permissions[4]->name)
                    ],
                    [
                        'id' => $permissions[5]->id,
                        'name' => $permissions[5]->name,
                        'can_be_per_lan' => (boolean)$permissions[5]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[5]->name),
                        'description' => trans('permission.description-' . $permissions[5]->name)
                    ],
                    [
                        'id' => $permissions[6]->id,
                        'name' => $permissions[6]->name,
                        'can_be_per_lan' => (boolean)$permissions[6]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[6]->name),
                        'description' => trans('permission.description-' . $permissions[6]->name)
                    ],
                    [
                        'id' => $permissions[7]->id,
                        'name' => $permissions[7]->name,
                        'can_be_per_lan' => (boolean)$permissions[7]->can_be_per_lan,
                        'display_name' => trans('permission.display-name-' . $permissions[7]->name),
                        'description' => trans('permission.description-' . $permissions[7]->name)
                    ],
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetAdminSummaryLanIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('GET', '/api/admin/summary', [
                'lan_id' => -1
            ])
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

    public function testGetAdminSummaryLanIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('GET', '/api/admin/summary', [
                'lan_id' => '☭'
            ])
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
