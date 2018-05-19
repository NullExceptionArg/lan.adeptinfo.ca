<?php

namespace Tests\Unit\Controller\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateContributionCategoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $requestContent = [
        'name' => "Programmer",
    ];

    public function testCreateContributionCategory()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();

        $this->actingAs($user)
            ->json('POST', '/api/lan/' . $lan->id . '/contribution-category', $this->requestContent)
            ->seeJsonEquals([
                'id' => 1,
                'name' => $this->requestContent['name'],
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateContributionCategoryLanIdExist()
    {
        $user = factory('App\Model\User')->create();
        $badLanId = -1;

        $this->actingAs($user)
            ->json('POST', '/api/lan/' . $badLanId . '/contribution-category', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'Lan with id ' . $badLanId . ' doesn\'t exist',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateContributionCategoryLanIdInteger()
    {
        $user = factory('App\Model\User')->create();
        $badLanId = '☭';

        $this->actingAs($user)
            ->json('POST', '/api/lan/' . $badLanId . '/contribution-category', $this->requestContent)
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

    public function testCreateContributionCategoryNameRequired()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();
        $this->requestContent['name'] = null;

        $this->actingAs($user)
            ->json('POST', '/api/lan/' . $lan->id . '/contribution-category', $this->requestContent)
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

    public function testCreateContributionCategoryNameString()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();
        $this->requestContent['name'] = 1;

        $this->actingAs($user)
            ->json('POST', '/api/lan/' . $lan->id . '/contribution-category', $this->requestContent)
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
