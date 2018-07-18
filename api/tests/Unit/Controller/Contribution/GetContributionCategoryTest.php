<?php

namespace Tests\Unit\Controller\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetContributionCategoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $lan;
    protected $category;

    public function setUp(): void
    {
        parent::setUp();
        $this->lan = factory('App\Model\Lan')->create();
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testGetContributionCategory(): void
    {
        $this->json('GET', '/api/contribution/category', [
            'lan_id' => $this->lan->id
        ])
            ->seeJsonEquals([[
                'id' => $this->category->id,
                'name' => $this->category->name
            ]])
            ->assertResponseStatus(200);
    }

    public function testGetContributionCategoryCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);
        $this->json('GET', '/api/contribution/category')
            ->seeJsonEquals([[
                'id' => $category->id,
                'name' => $category->name
            ]])
            ->assertResponseStatus(200);
    }

    public function testGetLanIdExist(): void
    {
        $this->json('GET', '/api/contribution/category', [
            'lan_id' => -1
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

    public function testGetLanIdInteger(): void
    {
        $this->json('GET', '/api/contribution/category', [
            'lan_id' => '☭'
        ])
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
}
