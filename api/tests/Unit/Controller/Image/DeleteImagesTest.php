<?php

namespace Tests\Unit\Controller\Image;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteImagesTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;
    protected $image;
    protected $image1;
    protected $image2;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->image = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
        $this->image1 = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
        $this->image2 = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'delete-image')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);
    }

    public function testDeleteImages(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', '/api/image', [
                'lan_id' => $this->lan->id,
                'images_id' => $this->image1->id . ',' . $this->image2->id
            ])
            ->seeJsonEquals([
                $this->image1->id,
                $this->image2->id
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteImagesHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('DELETE', '/api/image', [
                'lan_id' => $this->lan->id,
                'images_id' => $this->image1->id . ',' . $this->image2->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testDeleteImagesCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $image1 = factory('App\Model\Image')->create([
            'lan_id' => $lan->id
        ]);
        $image2 = factory('App\Model\Image')->create([
            'lan_id' => $lan->id
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'delete-image')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->actingAs($this->user)
            ->json('DELETE', '/api/image', [
                'images_id' => $image1->id . ',' . $image2->id
            ])
            ->seeJsonEquals([
                $image1->id,
                $image2->id
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteImagesLanIdExists(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', '/api/image', [
                'lan_id' => -1,
                'images_id' => $this->image1->id . ',' . $this->image2->id
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

    public function testDeleteImagesLanIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', '/api/image', [
                'lan_id' => '☭',
                'images_id' => $this->image1->id . ',' . $this->image2->id
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

    public function testDeleteImagesImagesIdString(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', '/api/image', [
                'lan_id' => $this->lan->id,
                'images_id' => -1
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'images_id' => [
                        0 => 'The images id must be a string.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
