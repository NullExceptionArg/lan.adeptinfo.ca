<?php

namespace Tests\Unit\Controller\Contribution;


use App\Model\Contribution;
use App\Model\ContributionCategory;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteContributionCategoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;
    protected $category;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testDeleteContributionCategorySimple()
    {
        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $this->lan->id . '/contribution-category/' . $this->category->id)
            ->seeJsonEquals([
                'id' => $this->category->id,
                'name' => $this->category->name
            ])
            ->assertResponseStatus(200);
    }

    // Should be updated every time Contribution Category has a new relation
    public function testDeleteContributionCategoryComplexOnCategoryForContribution()
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user
        ]);

        ///Building relations
        // Contribution - Contribution category relation
        $contribution->ContributionCategory()->attach($this->category);

        /// Make sure every relations exist
        // Lan - Contribution
        $this->assertEquals(1, $this->user->Contribution()->count());

        // Contribution - Contribution category
        $this->assertEquals(1, $contribution->ContributionCategory()->count());

        //Contribution category - Lan
        $this->assertEquals(1, $this->category->Lan()->count());

        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $this->lan->id . '/contribution-category/' . $this->category->id)
            ->seeJsonEquals([
                'id' => $this->category->id,
                'name' => $this->category->name
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
        $category2 = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
        ]);

        ///Building relations
        // Contribution - Contribution category relation
        $contribution->ContributionCategory()->attach($this->category);
        $contribution->ContributionCategory()->attach($category2);

        /// Make sure every relations exist
        // Lan - Contribution
        $this->assertEquals(1, $this->user->Contribution()->count());

        // Contribution - Contribution category
        $this->assertEquals(2, $contribution->ContributionCategory()->count());

        //Contribution category - Lan
        $this->assertEquals(1, $this->category->Lan()->count());

        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $this->lan->id . '/contribution-category/' . $this->category->id)
            ->seeJsonEquals([
                'id' => $this->category->id,
                'name' => $this->category->name
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
        $badLanId = -1;
        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $badLanId . '/contribution-category/' . $this->category->id)
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
        $badLanId = '☭';
        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $badLanId . '/contribution-category/' . $this->category->id)
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
        $badCategoryId = -1;
        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $this->lan->id . '/contribution-category/' . $badCategoryId)
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
        $badCategoryId = '☭';
        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $this->lan->id . '/contribution-category/' . $badCategoryId)
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
