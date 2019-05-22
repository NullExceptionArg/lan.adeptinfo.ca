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
            ->where('name', '!=', 'edit-tournament')
            ->take(8)
            ->get();
        $lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id,
        ]);
        $globalRole = factory('App\Model\GlobalRole')->create();
        for ($i = 0; $i < 5; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id'       => $lanRole->id,
            ]);
        }
        for ($i = 5; $i < 8; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id'       => $globalRole->id,
            ]);
        }
        factory('App\Model\LanRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $lanRole->id,
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $globalRole->id,
        ]);
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/admin/summary', [
                'lan_id' => $lan->id,
            ])
            ->seeJsonEquals([
                'first_name'      => $this->user->first_name,
                'last_name'       => $this->user->last_name,
                'has_tournaments' => false,
                'email'           => $this->user->email,
                'permissions'     => [
                    [
                        'id'   => $permissions[0]->id,
                        'name' => $permissions[0]->name,
                    ],
                    [
                        'id'   => $permissions[1]->id,
                        'name' => $permissions[1]->name,
                    ],
                    [
                        'id'   => $permissions[2]->id,
                        'name' => $permissions[2]->name,
                    ],
                    [
                        'id'   => $permissions[3]->id,
                        'name' => $permissions[3]->name,
                    ],
                    [
                        'id'   => $permissions[4]->id,
                        'name' => $permissions[4]->name,
                    ],
                    [
                        'id'   => $permissions[5]->id,
                        'name' => $permissions[5]->name,
                    ],
                    [
                        'id'   => $permissions[6]->id,
                        'name' => $permissions[6]->name,
                    ],
                    [
                        'id'   => $permissions[7]->id,
                        'name' => $permissions[7]->name,
                    ],
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetAdminSummaryCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true,
        ]);
        $permissions = Permission::inRandomOrder()
            ->take(8)
            ->get();
        $lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id,
        ]);
        $globalRole = factory('App\Model\GlobalRole')->create();
        for ($i = 0; $i < 5; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id'       => $lanRole->id,
            ]);
        }
        for ($i = 5; $i < 8; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id'       => $globalRole->id,
            ]);
        }
        factory('App\Model\LanRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $lanRole->id,
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $globalRole->id,
        ]);
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/admin/summary', [
                'lan_id' => $lan->id,
            ])
            ->seeJsonEquals([
                'first_name'      => $this->user->first_name,
                'last_name'       => $this->user->last_name,
                'has_tournaments' => false,
                'email'           => $this->user->email,
                'permissions'     => [
                    [
                        'id'   => $permissions[0]->id,
                        'name' => $permissions[0]->name,
                    ],
                    [
                        'id'   => $permissions[1]->id,
                        'name' => $permissions[1]->name,
                    ],
                    [
                        'id'   => $permissions[2]->id,
                        'name' => $permissions[2]->name,
                    ],
                    [
                        'id'   => $permissions[3]->id,
                        'name' => $permissions[3]->name,
                    ],
                    [
                        'id'   => $permissions[4]->id,
                        'name' => $permissions[4]->name,
                    ],
                    [
                        'id'   => $permissions[5]->id,
                        'name' => $permissions[5]->name,
                    ],
                    [
                        'id'   => $permissions[6]->id,
                        'name' => $permissions[6]->name,
                    ],
                    [
                        'id'   => $permissions[7]->id,
                        'name' => $permissions[7]->name,
                    ],
                ],
            ])
            ->assertResponseStatus(200);
    }
}
