<?php

namespace Tests\Unit\Controller\Contribution;


use App\Model\Contribution;
use App\Model\ContributionCategory;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteContributionCategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testDeleteContributionCategorySimple()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);

        $this->actingAs($user)
            ->json('DELETE', '/api/lan/' . $lan->id . '/contribution-category/' . $category->id)
            ->seeJsonEquals([
                'id' => $category->id,
                'name' => $category->name
            ])
            ->assertResponseStatus(200);
    }

    // Should be updated every time Contribution Category has a new relation
    public function testDeleteContributionCategoryComplexOnCategoryForContribution()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $user->id
        ]);

        ///Building relations
        // Contribution - Contribution category relation
        $contribution->ContributionCategory()->attach($category);

        /// Make sure every relations exist
        // Lan - Contribution
        $this->assertEquals(1, $user->Contribution()->count());

        // Contribution - Contribution category
        $this->assertEquals(1, $contribution->ContributionCategory()->count());

        //Contribution category - Lan
        $this->assertEquals(1, $category->Lan()->count());

        $this->actingAs($user)
            ->json('DELETE', '/api/lan/' . $lan->id . '/contribution-category/' . $category->id)
            ->seeJsonEquals([
                'id' => $category->id,
                'name' => $category->name
            ])
            ->assertResponseStatus(200);

        /// Verify relations have been removed
        // Contribution category
        $this->assertEquals(0, ContributionCategory::all()->count());

        // Contribution
        $this->assertEquals(0, Contribution::all()->count());
    }

    // Should be updated every time Contribution Category has a new relation
    public function testDeleteContributionCategoryComplexManyCategoryForContribution()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);
        $category2 = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $user->id
        ]);

        ///Building relations
        // Contribution - Contribution category relation
        $contribution->ContributionCategory()->attach($category);
        $contribution->ContributionCategory()->attach($category2);

        /// Make sure every relations exist
        // Lan - Contribution
        $this->assertEquals(1, $user->Contribution()->count());

        // Contribution - Contribution category
        $this->assertEquals(2, $contribution->ContributionCategory()->count());

        //Contribution category - Lan
        $this->assertEquals(1, $category->Lan()->count());

        $this->actingAs($user)
            ->json('DELETE', '/api/lan/' . $lan->id . '/contribution-category/' . $category->id)
            ->seeJsonEquals([
                'id' => $category->id,
                'name' => $category->name
            ])
            ->assertResponseStatus(200);

        /// Verify relations have been removed
        // Contribution category
        $this->assertEquals(1, ContributionCategory::all()->count());

        // Contribution
        $this->assertEquals(1, Contribution::all()->count());
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
                        0 => 'The selected lan id is invalid.',
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
                        0 => 'The selected contribution category id is invalid.',
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
