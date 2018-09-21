<?php

namespace Tests\Unit\Controller\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;

    protected $requestContent = [
        'lan_id' => null,
        'name' => 'comrade',
        'en_display_name' => 'Comrade',
        'en_description' => 'Our equal',
        'fr_display_name' => 'Camarade',
        'fr_description' => 'Notre égal.',
        'permissions' => null
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->requestContent['lan_id'] = $this->lan->id;
        $this->requestContent['permissions'] = Permission::inRandomOrder()
            ->take(5)
            ->pluck('id')
            ->toArray();
    }

    public function testCreateLanRoleRoleTest(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'lan_id' => $this->requestContent['lan_id'],
                'name' => $this->requestContent['name'],
                'en_display_name' => $this->requestContent['en_display_name'],
                'en_description' => $this->requestContent['en_description'],
                'fr_display_name' => $this->requestContent['fr_display_name'],
                'fr_description' => $this->requestContent['fr_description']
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateLanRoleLanIdExists(): void
    {
        $this->requestContent['lan_id'] = -1;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRoleLanIdInteger(): void
    {
        $this->requestContent['lan_id'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRoleNameRequired(): void
    {
        $this->requestContent['name'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'name' => [
                        0 => 'The name field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateLanRoleRoleNameString(): void
    {
        $this->requestContent['name'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRoleNameMaxLength(): void
    {
        $this->requestContent['name'] = str_repeat('☭', 51);
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRoleNameUnique(): void
    {
        factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id,
            'name' => $this->requestContent['name']
        ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRoleEnDisplayNameRequired(): void
    {
        $this->requestContent['en_display_name'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'en_display_name' => [
                        0 => 'The en display name field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateLanRoleRoleEnDisplayNameString(): void
    {
        $this->requestContent['en_display_name'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRoleEnDisplayNameMaxLength(): void
    {
        $this->requestContent['en_display_name'] = str_repeat('☭', 71);
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRoleEnDescriptionRequired(): void
    {
        $this->requestContent['en_description'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'en_description' => [
                        0 => 'The en description field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateLanRoleRoleEnDescriptionString(): void
    {
        $this->requestContent['en_description'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRoleEnDescriptionMaxLength(): void
    {
        $this->requestContent['en_description'] = str_repeat('☭', 1001);
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRoleFrDisplayNameRequired(): void
    {
        $this->requestContent['fr_display_name'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'fr_display_name' => [
                        0 => 'The fr display name field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateLanRoleRoleFrDisplayNameString(): void
    {
        $this->requestContent['fr_display_name'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRoleFrDisplayNameMaxLength(): void
    {
        $this->requestContent['fr_display_name'] = str_repeat('☭', 71);
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRoleFrDescriptionRequired(): void
    {
        $this->requestContent['fr_description'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'fr_description' => [
                        0 => 'The fr description field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateLanRoleRoleFrDescriptionString(): void
    {
        $this->requestContent['fr_description'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRoleFrDescriptionMaxLength(): void
    {
        $this->requestContent['fr_description'] = str_repeat('☭', 1001);
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRolePermissionsRequired(): void
    {
        $this->requestContent['permissions'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRolePermissionsArray(): void
    {
        $this->requestContent['permissions'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRolePermissionsArrayOfInteger(): void
    {
        $this->requestContent['permissions'] = ['1', 2];
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
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

    public function testCreateLanRoleRolePermissionsElementsInArrayExistInPermission(): void
    {
        $this->requestContent['permissions'] = [2, -1];
        $this->actingAs($this->user)
            ->json('POST', '/api/role/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'permissions' => [
                        0 => 'An element of the array is not contained an existing permission id.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
