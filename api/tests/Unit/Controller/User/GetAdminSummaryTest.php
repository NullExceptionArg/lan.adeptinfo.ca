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

    public function testGetAdminSummary(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $permissions = Permission::inRandomOrder()
            ->take(5)
            ->get();
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        foreach ($permissions as $permission) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permission->id,
                'role_id' => $role->id
            ]);
        }
        factory('App\Model\LanRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $role->id
        ]);
        $this->actingAs($this->user)
            ->json('GET', '/api/admin/summary', [
                'lan_id' => $lan->id
            ])
            ->seeJsonEquals([
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'permissions' => [
                    [
                        'id' => $permissions[0]->id,
                        'name' => $permissions[0]->name,
                        'display_name' => trans('permission.display-name-' . $permissions[0]->name),
                        'description' => trans('permission.description-' . $permissions[0]->name)
                    ],
                    [
                        'id' => $permissions[1]->id,
                        'name' => $permissions[1]->name,
                        'display_name' => trans('permission.display-name-' . $permissions[1]->name),
                        'description' => trans('permission.description-' . $permissions[1]->name)
                    ],
                    [
                        'id' => $permissions[2]->id,
                        'name' => $permissions[2]->name,
                        'display_name' => trans('permission.display-name-' . $permissions[2]->name),
                        'description' => trans('permission.description-' . $permissions[2]->name)
                    ],
                    [
                        'id' => $permissions[3]->id,
                        'name' => $permissions[3]->name,
                        'display_name' => trans('permission.display-name-' . $permissions[3]->name),
                        'description' => trans('permission.description-' . $permissions[3]->name)
                    ],
                    [
                        'id' => $permissions[4]->id,
                        'name' => $permissions[4]->name,
                        'display_name' => trans('permission.display-name-' . $permissions[4]->name),
                        'description' => trans('permission.description-' . $permissions[4]->name)
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
        $permissions = Permission::inRandomOrder()
            ->take(5)
            ->get();
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        foreach ($permissions as $permission) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permission->id,
                'role_id' => $role->id
            ]);
        }
        factory('App\Model\LanRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $role->id
        ]);
        $this->actingAs($this->user)
            ->json('GET', '/api/admin/summary')
            ->seeJsonEquals([
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'permissions' => [
                    [
                        'id' => $permissions[0]->id,
                        'name' => $permissions[0]->name,
                        'display_name' => trans('permission.display-name-' . $permissions[0]->name),
                        'description' => trans('permission.description-' . $permissions[0]->name)
                    ],
                    [
                        'id' => $permissions[1]->id,
                        'name' => $permissions[1]->name,
                        'display_name' => trans('permission.display-name-' . $permissions[1]->name),
                        'description' => trans('permission.description-' . $permissions[1]->name)
                    ],
                    [
                        'id' => $permissions[2]->id,
                        'name' => $permissions[2]->name,
                        'display_name' => trans('permission.display-name-' . $permissions[2]->name),
                        'description' => trans('permission.description-' . $permissions[2]->name)
                    ],
                    [
                        'id' => $permissions[3]->id,
                        'name' => $permissions[3]->name,
                        'display_name' => trans('permission.display-name-' . $permissions[3]->name),
                        'description' => trans('permission.description-' . $permissions[3]->name)
                    ],
                    [
                        'id' => $permissions[4]->id,
                        'name' => $permissions[4]->name,
                        'display_name' => trans('permission.display-name-' . $permissions[4]->name),
                        'description' => trans('permission.description-' . $permissions[4]->name)
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
                        0 => 'The selected lan id is invalid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

}
