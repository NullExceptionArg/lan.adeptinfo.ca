<?php

namespace Tests\Unit\Controller\Lan;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteLanImagesTest extends TestCase
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
        $this->image = factory('App\Model\LanImage')->create([
            'lan_id' => $this->lan->id
        ]);
        $this->image1 = factory('App\Model\LanImage')->create([
            'lan_id' => $this->lan->id
        ]);
        $this->image2 = factory('App\Model\LanImage')->create([
            'lan_id' => $this->lan->id
        ]);

        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'delete-image'
        );
    }

    public function testDeleteLanImages(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/lan/image', [
                'lan_id' => $this->lan->id,
                'image_ids' => $this->image1->id . ',' . $this->image2->id
            ])
            ->seeJsonEquals([
                $this->image1->id,
                $this->image2->id
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteLanImagesHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/lan/image', [
                'lan_id' => $this->lan->id,
                'image_ids' => $this->image1->id . ',' . $this->image2->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testDeleteLanImagesHasPermissionDifferentLanForImages(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $image = factory('App\Model\LanImage')->create([
            'lan_id' => $lan->id
        ]);

        $this->actingAs($this->user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/lan/image', [
                'lan_id' => $this->lan->id,
                'image_ids' => $this->image1->id . ',' . $image->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testDeleteLanImagesCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $image1 = factory('App\Model\LanImage')->create([
            'lan_id' => $lan->id
        ]);
        $image2 = factory('App\Model\LanImage')->create([
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
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/lan/image', [
                'image_ids' => $image1->id . ',' . $image2->id
            ])
            ->seeJsonEquals([
                $image1->id,
                $image2->id
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteLanImagesImagesIdString(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/lan/image', [
                'lan_id' => $this->lan->id,
                'image_ids' => -1
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'image_ids' => [
                        0 => 'The image ids must be a string.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
