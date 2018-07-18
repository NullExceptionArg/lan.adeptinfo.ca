<?php

namespace Tests\Unit\Controller\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateContributionCategoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;

    protected $requestContent = [
        'name' => "Programmer",
        'lan_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->requestContent['lan_id'] = $this->lan->id;
    }

    public function testCreateContributionCategory(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/contribution/category', $this->requestContent)
            ->seeJsonEquals([
                'id' => 1,
                'name' => $this->requestContent['name'],
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateContributionCategoryLanIdExist(): void
    {
        $this->requestContent['lan_id'] = -1;
        $this->actingAs($this->user)
            ->json('POST', '/api/contribution/category', $this->requestContent)
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

    public function testCreateContributionCategoryLanIdInteger(): void
    {
        $this->requestContent['lan_id'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/contribution/category', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The lan id must be an integer.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateContributionCategoryNameRequired(): void
    {
        $this->requestContent['name'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/contribution/category', $this->requestContent)
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

    public function testCreateContributionCategoryNameString(): void
    {
        $this->requestContent['name'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/contribution/category', $this->requestContent)
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
}
