<?php

namespace Tests\Unit\Controller\Tag;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    protected $requestContent = [
        'name' => 'PRO'
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
    }

    public function testCreate(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/tag', $this->requestContent)
            ->seeJsonEquals([
                'id' => 1,
                'name' => $this->requestContent['name']
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateRequired(): void
    {
        $this->requestContent['name'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/tag', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'name' => [
                        0 => 'The name field is required.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateString(): void
    {
        $this->requestContent['name'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/tag', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'name' => [
                        0 => 'The name must be a string.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateMaxLength(): void
    {
        $this->requestContent['name'] = str_repeat('☭', 6);
        $this->actingAs($this->user)
            ->json('POST', '/api/tag', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'name' => [
                        0 => 'The name may not be greater than 5 characters.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateUnique(): void
    {
        factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
            'name' => $this->requestContent['name']
        ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/tag', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'name' => [
                        0 => 'The name has already been taken.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

}
