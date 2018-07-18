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
        'image' => null,
        'lan_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

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
