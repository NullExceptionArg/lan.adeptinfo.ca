<?php

namespace Tests\Unit\Repository\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AttachContributionCategoryContributionTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionRepository;

    protected $user;
    protected $lan;
    protected $category;
    protected $contribution;

    public function setUp()
    {
        parent::setUp();
        $this->contributionRepository = $this->app->make('App\Repositories\Implementation\ContributionRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
        ]);
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testAttachContributionCategoryContribution()
    {
        $this->contributionRepository->attachContributionCategoryContribution($this->contribution, $this->category);

        $this->seeInDatabase('contribution_cat_contribution', [
            'contribution_category_id' => $this->category->id,
            'contribution_id' => $this->contribution->id
        ]);
    }
}
