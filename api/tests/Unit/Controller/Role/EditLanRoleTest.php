<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class EditLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;
    protected $lanRole;

    protected $requestContent = [
        'role_id' => null,
        'name' => 'comrade',
        'en_display_name' => 'Comrade',
        'en_description' => 'Our equal',
        'fr_display_name' => 'Camarade',
        'fr_description' => 'Notre égal.',
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
        $permission = Permission::where('name', 'edit-lan-role')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->requestContent['role_id'] = $this->lanRole->id;
    }

    public function testEditLanRole(): void
    {
        $this->actingAs($this->user)
            ->json('PUT', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'lan_id' => $this->lan->id,
                'name' => $this->requestContent['name'],
                'en_display_name' => $this->requestContent['en_display_name'],
                'en_description' => $this->requestContent['en_description'],
                'fr_display_name' => $this->requestContent['fr_display_name'],
                'fr_description' => $this->requestContent['fr_description']
            ])
            ->assertResponseStatus(200);
    }

    public function testEditLanRoleLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('PUT', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testEditLanRoleRoleIdRequired(): void
    {
        $this->requestContent['role_id'] = null;
        $this->actingAs($this->user)
            ->json('PUT', '/api/role/lan', $this->requestContent)
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

    public function testEditLanRoleRoleIdExist(): void
    {
        $this->requestContent['role_id'] = '☭';
        $this->actingAs($this->user)
            ->json('PUT', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'role_id' => [
                        0 => 'The selected role id is invalid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testEditLanRoleNameString(): void
    {
        $this->requestContent['name'] = 1;
        $this->actingAs($this->user)
            ->json('PUT', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'name' => [
                        0 => 'The name must be a string.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testEditLanRoleNameMaxLength(): void
    {
        $this->requestContent['name'] = str_repeat('☭', 51);
        $this->actingAs($this->user)
            ->json('PUT', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'name' => [
                        0 => 'The name may not be greater than 50 characters.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testEditLanRoleNameUnique(): void
    {
        factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id,
            'name' => $this->requestContent['name']
        ]);
        $this->actingAs($this->user)
            ->json('PUT', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'name' => [
                        0 => 'The name has already been taken.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testEditLanRoleEnDisplayNameString(): void
    {
        $this->requestContent['en_display_name'] = 1;
        $this->actingAs($this->user)
            ->json('PUT', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'en_display_name' => [
                        0 => 'The en display name must be a string.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testEditLanRoleEnDisplayNameMaxLength(): void
    {
        $this->requestContent['en_display_name'] = str_repeat('☭', 71);
        $this->actingAs($this->user)
            ->json('PUT', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'en_display_name' => [
                        0 => 'The en display name may not be greater than 70 characters.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testEditLanRoleEnDescriptionString(): void
    {
        $this->requestContent['en_description'] = 1;
        $this->actingAs($this->user)
            ->json('PUT', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'en_description' => [
                        0 => 'The en description must be a string.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testEditLanRoleEnDescriptionMaxLength(): void
    {
        $this->requestContent['en_description'] = str_repeat('☭', 1001);
        $this->actingAs($this->user)
            ->json('PUT', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'en_description' => [
                        0 => 'The en description may not be greater than 1000 characters.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testEditLanRoleFrDisplayNameString(): void
    {
        $this->requestContent['fr_display_name'] = 1;
        $this->actingAs($this->user)
            ->json('PUT', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'fr_display_name' => [
                        0 => 'The fr display name must be a string.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testEditLanRoleFrDisplayNameMaxLength(): void
    {
        $this->requestContent['fr_display_name'] = str_repeat('☭', 71);
        $this->actingAs($this->user)
            ->json('PUT', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'fr_display_name' => [
                        0 => 'The fr display name may not be greater than 70 characters.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testEditLanRoleFrDescriptionString(): void
    {
        $this->requestContent['fr_description'] = 1;
        $this->actingAs($this->user)
            ->json('PUT', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'fr_description' => [
                        0 => 'The fr description must be a string.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testEditLanRoleFrDescriptionMaxLength(): void
    {
        $this->requestContent['fr_description'] = str_repeat('☭', 1001);
        $this->actingAs($this->user)
            ->json('PUT', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'fr_description' => [
                        0 => 'The fr description may not be greater than 1000 characters.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

}
