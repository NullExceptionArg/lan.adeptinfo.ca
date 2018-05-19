<?php

namespace Tests\Unit\Controller\Contribution;


use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteContributionCategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testDeleteContributionCategoryTest()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);

        $this->actingAs($user)
            ->json('DELETE', '/api/lan/' . $lan->id . '/contribution-category/' . $category->id)
            ->seeJsonEquals([
                'contribution_category_id' => $category->id
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteContributionCategoryTestLanIdExist()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);

        $badLanId = -1;

        $this->actingAs($user)
            ->json('DELETE', '/api/lan/' . $badLanId . '/contribution-category/' . $category->id)
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

    public function testDeleteContributionCategoryTestLanIdInteger()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);

        $badLanId = '☭';

        $this->actingAs($user)
            ->json('DELETE', '/api/lan/' . $badLanId . '/contribution-category/' . $category->id)
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

    public function testDeleteContributionCategoryTestCategoryIdExist()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();

        $badCategoryId = -1;

        $this->actingAs($user)
            ->json('DELETE', '/api/lan/' . $lan->id . '/contribution-category/' . $badCategoryId)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'contribution_category_id' => [
                        0 => 'Contribution category with id ' . $badCategoryId . ' doesn\'t exist',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testDeleteContributionCategoryTestCategoryIdInteger()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();

        $badCategoryId = '☭';

        $this->actingAs($user)
            ->json('DELETE', '/api/lan/' . $lan->id . '/contribution-category/' . $badCategoryId)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'contribution_category_id' => [
                        0 => 'The contribution category id must be an integer.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}
