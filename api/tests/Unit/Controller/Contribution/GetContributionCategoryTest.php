<?php

namespace Tests\Unit\Controller\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetContributionCategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testGetContributionCategory()
    {
        $lan = factory('App\Model\Lan')->create();
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);

        $this->json('GET', '/api/lan/' . $lan->id . '/contribution-category')
            ->seeJsonEquals([[
                'id' => $category->id,
                'name' => $category->name
            ]])
            ->assertResponseStatus(200);
    }

    public function testGetLanRulesLanIdExist()
    {
        $badLanId = -1;
        $this->json('GET', '/api/lan/' . $badLanId . '/contribution-category')
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

    public function testGetLanRulesLanIdInteger()
    {
        $badLanId = '☭';
        $this->json('GET', '/api/lan/' . $badLanId . '/contribution-category')
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
