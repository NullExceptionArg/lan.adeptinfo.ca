<?php

namespace Tests\Unit\Service\Contribution;

use App\Model\Contribution;
use App\Model\ContributionCategory;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteCategoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionService;

    protected $user;
    protected $lan;
    protected $category;

    public function setUp(): void
    {
        parent::setUp();
        $this->contributionService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);

        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'delete-contribution-category'
        );

        $this->be($this->user);
    }

    public function testDeleteCategorySimple(): void
    {
        $result = $this->contributionService->deleteCategory($this->category->id);

        $this->assertEquals($this->category->id, $result['id']);
    }

    // Should be updated every time Contribution Category has a new relation
    public function testDeleteContributionCategoryComplexOnCategoryForContribution(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
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

        $this->contributionService->deleteCategory($this->category->id);

        /// Verify relations have been removed
        // Contribution category
        $this->assertEquals(0, ContributionCategory::all()->count());

        // Contribution
        $this->assertEquals(0, Contribution::all()->count());
    }

    // Should be updated every time Contribution Category has a new relation
    public function testDeleteContributionCategoryComplexManyCategoryForContribution(): void
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

        $this->contributionService->deleteCategory($this->category->id);

        /// Verify relations have been removed
        // Contribution category
        $this->assertEquals(1, ContributionCategory::all()->count());

        // Contribution
        $this->assertEquals(1, Contribution::all()->count());
    }
}
