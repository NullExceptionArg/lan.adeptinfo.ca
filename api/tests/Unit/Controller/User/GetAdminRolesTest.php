<?php

namespace Tests\Unit\Controller\User;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetAdminRoles extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
    }

    public function testGetAdminRolesUserBeingCheckedFrench(): void
    {
        $userBeingChecked = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();

        $this->addLanPermissionToUser(
            $this->user->id,
            $lan->id,
            'get-admin-roles'
        );

        $permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-admin-roles')
            ->take(8)
            ->get();

        $lanRoles = factory('App\Model\LanRole', 4)->create([
            'lan_id' => $lan->id
        ]);
        for ($i = 0; $i <= 3; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $lanRoles[$i]->id
            ]);
            factory('App\Model\LanRoleUser')->create([
                'user_id' => $userBeingChecked->id,
                'role_id' => $lanRoles[$i]->id
            ]);
        }

        $globalRoles = factory('App\Model\GlobalRole', 4)->create();
        for ($i = 4; $i <= 7; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
            factory('App\Model\GlobalRoleUser')->create([
                'user_id' => $userBeingChecked->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
        }

        $this->actingAs($this->user)
            ->json('GET', '/api/admin/roles', [
                'lan_id' => $lan->id,
                'email' => $userBeingChecked->email,
                'lang' => 'fr'
            ])
            ->seeJsonEquals([
                'global_roles' => [
                    [
                        'id' => $globalRoles[0]->id,
                        'name' => $globalRoles[0]->name,
                        'display_name' => $globalRoles[0]->fr_display_name,
                        'description' => $globalRoles[0]->fr_description,
                    ],
                    [
                        'id' => $globalRoles[1]->id,
                        'name' => $globalRoles[1]->name,
                        'display_name' => $globalRoles[1]->fr_display_name,
                        'description' => $globalRoles[1]->fr_description,
                    ],
                    [
                        'id' => $globalRoles[2]->id,
                        'name' => $globalRoles[2]->name,
                        'display_name' => $globalRoles[2]->fr_display_name,
                        'description' => $globalRoles[2]->fr_description,
                    ],
                    [
                        'id' => $globalRoles[3]->id,
                        'name' => $globalRoles[3]->name,
                        'display_name' => $globalRoles[3]->fr_display_name,
                        'description' => $globalRoles[3]->fr_description,
                    ]
                ],
                'lan_roles' => [
                    [
                        'id' => $lanRoles[0]->id,
                        'name' => $lanRoles[0]->name,
                        'display_name' => $lanRoles[0]->fr_display_name,
                        'description' => $lanRoles[0]->fr_description,
                    ],
                    [
                        'id' => $lanRoles[1]->id,
                        'name' => $lanRoles[1]->name,
                        'display_name' => $lanRoles[1]->fr_display_name,
                        'description' => $lanRoles[1]->fr_description,
                    ],
                    [
                        'id' => $lanRoles[2]->id,
                        'name' => $lanRoles[2]->name,
                        'display_name' => $lanRoles[2]->fr_display_name,
                        'description' => $lanRoles[2]->fr_description,
                    ],
                    [
                        'id' => $lanRoles[3]->id,
                        'name' => $lanRoles[3]->name,
                        'display_name' => $lanRoles[3]->fr_display_name,
                        'description' => $lanRoles[3]->fr_description,
                    ]
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetAdminRolesUserBeingCheckedEnglish(): void
    {
        $userBeingChecked = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();

        $this->addLanPermissionToUser(
            $this->user->id,
            $lan->id,
            'get-admin-roles'
        );

        $permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-admin-roles')
            ->take(8)
            ->get();

        $lanRoles = factory('App\Model\LanRole', 4)->create([
            'lan_id' => $lan->id
        ]);
        for ($i = 0; $i <= 3; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $lanRoles[$i]->id
            ]);
            factory('App\Model\LanRoleUser')->create([
                'user_id' => $userBeingChecked->id,
                'role_id' => $lanRoles[$i]->id
            ]);
        }

        $globalRoles = factory('App\Model\GlobalRole', 4)->create();
        for ($i = 4; $i <= 7; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
            factory('App\Model\GlobalRoleUser')->create([
                'user_id' => $userBeingChecked->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
        }

        $this->actingAs($this->user)
            ->json('GET', '/api/admin/roles', [
                'lan_id' => $lan->id,
                'email' => $userBeingChecked->email,
                'lang' => 'en'
            ])
            ->seeJsonEquals([
                'global_roles' => [
                    [
                        'id' => $globalRoles[0]->id,
                        'name' => $globalRoles[0]->name,
                        'display_name' => $globalRoles[0]->en_display_name,
                        'description' => $globalRoles[0]->en_description,
                    ],
                    [
                        'id' => $globalRoles[1]->id,
                        'name' => $globalRoles[1]->name,
                        'display_name' => $globalRoles[1]->en_display_name,
                        'description' => $globalRoles[1]->en_description,
                    ],
                    [
                        'id' => $globalRoles[2]->id,
                        'name' => $globalRoles[2]->name,
                        'display_name' => $globalRoles[2]->en_display_name,
                        'description' => $globalRoles[2]->en_description,
                    ],
                    [
                        'id' => $globalRoles[3]->id,
                        'name' => $globalRoles[3]->name,
                        'display_name' => $globalRoles[3]->en_display_name,
                        'description' => $globalRoles[3]->en_description,
                    ]
                ],
                'lan_roles' => [
                    [
                        'id' => $lanRoles[0]->id,
                        'name' => $lanRoles[0]->name,
                        'display_name' => $lanRoles[0]->en_display_name,
                        'description' => $lanRoles[0]->en_description,
                    ],
                    [
                        'id' => $lanRoles[1]->id,
                        'name' => $lanRoles[1]->name,
                        'display_name' => $lanRoles[1]->en_display_name,
                        'description' => $lanRoles[1]->en_description,
                    ],
                    [
                        'id' => $lanRoles[2]->id,
                        'name' => $lanRoles[2]->name,
                        'display_name' => $lanRoles[2]->en_display_name,
                        'description' => $lanRoles[2]->en_description,
                    ],
                    [
                        'id' => $lanRoles[3]->id,
                        'name' => $lanRoles[3]->name,
                        'display_name' => $lanRoles[3]->en_display_name,
                        'description' => $lanRoles[3]->en_description,
                    ]
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetAdminRolesCurrentUser(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-admin-roles')
            ->take(8)
            ->get();

        $lanRoles = factory('App\Model\LanRole', 4)->create([
            'lan_id' => $lan->id
        ]);
        for ($i = 0; $i <= 3; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $lanRoles[$i]->id
            ]);
            factory('App\Model\LanRoleUser')->create([
                'user_id' => $this->user->id,
                'role_id' => $lanRoles[$i]->id
            ]);
        }

        $globalRoles = factory('App\Model\GlobalRole', 4)->create();
        for ($i = 4; $i <= 7; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
            factory('App\Model\GlobalRoleUser')->create([
                'user_id' => $this->user->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
        }

        $this->actingAs($this->user)
            ->json('GET', '/api/admin/roles', [
                'lan_id' => $lan->id,
                'email' => $this->user->email,
                'lang' => 'en'
            ])
            ->seeJsonEquals([
                'global_roles' => [
                    [
                        'id' => $globalRoles[0]->id,
                        'name' => $globalRoles[0]->name,
                        'display_name' => $globalRoles[0]->en_display_name,
                        'description' => $globalRoles[0]->en_description,
                    ],
                    [
                        'id' => $globalRoles[1]->id,
                        'name' => $globalRoles[1]->name,
                        'display_name' => $globalRoles[1]->en_display_name,
                        'description' => $globalRoles[1]->en_description,
                    ],
                    [
                        'id' => $globalRoles[2]->id,
                        'name' => $globalRoles[2]->name,
                        'display_name' => $globalRoles[2]->en_display_name,
                        'description' => $globalRoles[2]->en_description,
                    ],
                    [
                        'id' => $globalRoles[3]->id,
                        'name' => $globalRoles[3]->name,
                        'display_name' => $globalRoles[3]->en_display_name,
                        'description' => $globalRoles[3]->en_description,
                    ]
                ],
                'lan_roles' => [
                    [
                        'id' => $lanRoles[0]->id,
                        'name' => $lanRoles[0]->name,
                        'display_name' => $lanRoles[0]->en_display_name,
                        'description' => $lanRoles[0]->en_description,
                    ],
                    [
                        'id' => $lanRoles[1]->id,
                        'name' => $lanRoles[1]->name,
                        'display_name' => $lanRoles[1]->en_display_name,
                        'description' => $lanRoles[1]->en_description,
                    ],
                    [
                        'id' => $lanRoles[2]->id,
                        'name' => $lanRoles[2]->name,
                        'display_name' => $lanRoles[2]->en_display_name,
                        'description' => $lanRoles[2]->en_description,
                    ],
                    [
                        'id' => $lanRoles[3]->id,
                        'name' => $lanRoles[3]->name,
                        'display_name' => $lanRoles[3]->en_display_name,
                        'description' => $lanRoles[3]->en_description,
                    ]
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetAdminRolesCurrentUserNoEmail(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-admin-roles')
            ->take(8)
            ->get();

        $lanRoles = factory('App\Model\LanRole', 4)->create([
            'lan_id' => $lan->id
        ]);
        for ($i = 0; $i <= 3; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $lanRoles[$i]->id
            ]);
            factory('App\Model\LanRoleUser')->create([
                'user_id' => $this->user->id,
                'role_id' => $lanRoles[$i]->id
            ]);
        }

        $globalRoles = factory('App\Model\GlobalRole', 4)->create();
        for ($i = 4; $i <= 7; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
            factory('App\Model\GlobalRoleUser')->create([
                'user_id' => $this->user->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
        }

        $this->actingAs($this->user)
            ->json('GET', '/api/admin/roles', [
                'lan_id' => $lan->id,
                'lang' => 'en'
            ])
            ->seeJsonEquals([
                'global_roles' => [
                    [
                        'id' => $globalRoles[0]->id,
                        'name' => $globalRoles[0]->name,
                        'display_name' => $globalRoles[0]->en_display_name,
                        'description' => $globalRoles[0]->en_description,
                    ],
                    [
                        'id' => $globalRoles[1]->id,
                        'name' => $globalRoles[1]->name,
                        'display_name' => $globalRoles[1]->en_display_name,
                        'description' => $globalRoles[1]->en_description,
                    ],
                    [
                        'id' => $globalRoles[2]->id,
                        'name' => $globalRoles[2]->name,
                        'display_name' => $globalRoles[2]->en_display_name,
                        'description' => $globalRoles[2]->en_description,
                    ],
                    [
                        'id' => $globalRoles[3]->id,
                        'name' => $globalRoles[3]->name,
                        'display_name' => $globalRoles[3]->en_display_name,
                        'description' => $globalRoles[3]->en_description,
                    ]
                ],
                'lan_roles' => [
                    [
                        'id' => $lanRoles[0]->id,
                        'name' => $lanRoles[0]->name,
                        'display_name' => $lanRoles[0]->en_display_name,
                        'description' => $lanRoles[0]->en_description,
                    ],
                    [
                        'id' => $lanRoles[1]->id,
                        'name' => $lanRoles[1]->name,
                        'display_name' => $lanRoles[1]->en_display_name,
                        'description' => $lanRoles[1]->en_description,
                    ],
                    [
                        'id' => $lanRoles[2]->id,
                        'name' => $lanRoles[2]->name,
                        'display_name' => $lanRoles[2]->en_display_name,
                        'description' => $lanRoles[2]->en_description,
                    ],
                    [
                        'id' => $lanRoles[3]->id,
                        'name' => $lanRoles[3]->name,
                        'display_name' => $lanRoles[3]->en_display_name,
                        'description' => $lanRoles[3]->en_description,
                    ]
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetAdminRolesCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-admin-roles')
            ->take(8)
            ->get();

        $lanRoles = factory('App\Model\LanRole', 4)->create([
            'lan_id' => $lan->id
        ]);
        for ($i = 0; $i <= 3; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $lanRoles[$i]->id
            ]);
            factory('App\Model\LanRoleUser')->create([
                'user_id' => $this->user->id,
                'role_id' => $lanRoles[$i]->id
            ]);
        }

        $globalRoles = factory('App\Model\GlobalRole', 4)->create();
        for ($i = 4; $i <= 7; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
            factory('App\Model\GlobalRoleUser')->create([
                'user_id' => $this->user->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
        }

        $this->actingAs($this->user)
            ->json('GET', '/api/admin/roles', [
                'email' => $this->user->email,
                'lang' => 'en'
            ])
            ->seeJsonEquals([
                'global_roles' => [
                    [
                        'id' => $globalRoles[0]->id,
                        'name' => $globalRoles[0]->name,
                        'display_name' => $globalRoles[0]->en_display_name,
                        'description' => $globalRoles[0]->en_description,
                    ],
                    [
                        'id' => $globalRoles[1]->id,
                        'name' => $globalRoles[1]->name,
                        'display_name' => $globalRoles[1]->en_display_name,
                        'description' => $globalRoles[1]->en_description,
                    ],
                    [
                        'id' => $globalRoles[2]->id,
                        'name' => $globalRoles[2]->name,
                        'display_name' => $globalRoles[2]->en_display_name,
                        'description' => $globalRoles[2]->en_description,
                    ],
                    [
                        'id' => $globalRoles[3]->id,
                        'name' => $globalRoles[3]->name,
                        'display_name' => $globalRoles[3]->en_display_name,
                        'description' => $globalRoles[3]->en_description,
                    ]
                ],
                'lan_roles' => [
                    [
                        'id' => $lanRoles[0]->id,
                        'name' => $lanRoles[0]->name,
                        'display_name' => $lanRoles[0]->en_display_name,
                        'description' => $lanRoles[0]->en_description,
                    ],
                    [
                        'id' => $lanRoles[1]->id,
                        'name' => $lanRoles[1]->name,
                        'display_name' => $lanRoles[1]->en_display_name,
                        'description' => $lanRoles[1]->en_description,
                    ],
                    [
                        'id' => $lanRoles[2]->id,
                        'name' => $lanRoles[2]->name,
                        'display_name' => $lanRoles[2]->en_display_name,
                        'description' => $lanRoles[2]->en_description,
                    ],
                    [
                        'id' => $lanRoles[3]->id,
                        'name' => $lanRoles[3]->name,
                        'display_name' => $lanRoles[3]->en_display_name,
                        'description' => $lanRoles[3]->en_description,
                    ]
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetAdminRolesHasPermission(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $admin = factory('App\Model\User')->create();
        $this->actingAs($admin)
            ->json('GET', '/api/admin/roles', [
                'lan_id' => $lan->id,
                'email' => $this->user->email
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testGetAdminRolesLanIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('GET', '/api/admin/roles', [
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

    public function testGetAdminRolesLanIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('GET', '/api/admin/roles', [
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

    public function testGetAdminRolesEmailExist(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'get-admin-roles')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);
        $this->actingAs($this->user)
            ->json('GET', '/api/admin/roles', [
                'email' => '☭',
                'lan_id' => $lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'email' => [
                        0 => 'The selected email is invalid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testGetAdminRolesEmailString(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'get-admin-roles')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);
        $this->actingAs($this->user)
            ->json('GET', '/api/admin/roles', [
                'email' => 1,
                'lan_id' => $lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'email' => [
                        0 => 'The email must be a string.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

}
