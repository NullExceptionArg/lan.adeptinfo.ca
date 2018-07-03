<?php

namespace Tests\Unit\Controller\Image;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class addImageTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;

    protected $requestContent = [
        'image' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->requestContent['image'] = factory('App\Model\Image')->make([
            'lan_id' => $this->lan->id
        ])->image;
    }

    public function testAddImage(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/image', $this->requestContent)
            ->seeJsonEquals([
                'id' => 1,
                'image' => $this->requestContent['image'],
                'lan_id' => $this->lan->id
            ])
            ->assertResponseStatus(201);
    }

    public function testAddImageLanIdExists(): void
    {
        $badLanId = -1;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $badLanId . '/image', $this->requestContent)
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
        $badLanId = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $badLanId . '/image', $this->requestContent)
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
            ->json('POST', '/api/lan/' . $this->lan->id . '/image', $this->requestContent)
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
            ->json('POST', '/api/lan/' . $this->lan->id . '/image', $this->requestContent)
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
