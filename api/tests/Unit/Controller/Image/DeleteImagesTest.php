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

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->image = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testDeleteImages(): void
    {
        $image1 = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
        $image2 = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $this->lan->id . '/image/' . $image1->id . ',' . $image2->id)
            ->seeJsonEquals([
                $image1->id,
                $image2->id
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteImagesLanIdExists(): void
    {
        $badLanId = -1;
        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $badLanId . '/image/' . $this->image->id)
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
        $badLanId = '☭';
        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $badLanId . '/image/' . $this->image->id)
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
        $badImageId = -1;
        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $this->lan->id . '/image/' . $badImageId)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'images_id' => [
                        0 => 'Images with id ' . $badImageId . ' don\'t exist.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
