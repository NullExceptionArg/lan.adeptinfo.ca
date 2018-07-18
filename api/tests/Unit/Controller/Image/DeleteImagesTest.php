<?php

namespace Tests\Unit\Controller\Image;

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

    public function testDeleteImagesCurrentLan(): void
    {
        factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $image1 = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
        $image2 = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
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
                        0 => 'The ids ' . -1 . ' on the field images id don\'t exist.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
