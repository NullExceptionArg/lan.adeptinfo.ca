<?php

namespace Tests\Unit\Controller\Image;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class addImageTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;

    protected $requestContent = [
        'image' => null,
        'lan_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'add-image')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->requestContent['image'] = factory('App\Model\Image')->make([
            'lan_id' => $this->lan->id
        ])->image;
        $this->requestContent['lan_id'] = $this->lan->id;
    }

    public function testAddImage(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/image', $this->requestContent)
            ->seeJsonEquals([
                'id' => 1,
                'image' => $this->requestContent['image'],
                'lan_id' => $this->lan->id
            ])
            ->assertResponseStatus(201);
    }

    public function testAddImageCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'add-image')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->requestContent['lan_id'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/image', $this->requestContent)
            ->seeJsonEquals([
                'id' => 1,
                'image' => $this->requestContent['image'],
                'lan_id' => $lan->id
            ])
            ->assertResponseStatus(201);
    }

    public function testAddImageHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('POST', '/api/image', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testAddImageLanIdExists(): void
    {
        $this->requestContent['lan_id'] = -1;
        $this->actingAs($this->user)
            ->json('POST', '/api/image', $this->requestContent)
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

    public function testAddImageLanIdInteger(): void
    {
        $this->requestContent['lan_id'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/image', $this->requestContent)
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

    public function testAddImageRequired(): void
    {
        $this->requestContent['image'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/image', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'image' => [
                        0 => 'The image field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testAddImageString(): void
    {
        $this->requestContent['image'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/image', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'image' => [
                        0 => 'The image must be a string.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
