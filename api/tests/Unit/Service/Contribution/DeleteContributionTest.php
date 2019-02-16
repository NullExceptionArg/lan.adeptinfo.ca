<?php

namespace Tests\Unit\Service\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteContributionTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributorService;

    protected $user;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->contributorService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testDeleteContributionUserEmail(): void
    {
        $contributionCategory = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->user->id
        ]);
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id,
            'contribution_category_id' => $contributionCategory->id
        ]);

        $result = $this->contributorService->deleteContribution($contribution->id);
        $result = $result->jsonSerialize();

        $this->assertEquals($contribution->id, $result['id']);
        $this->assertEquals($this->user->getFullName(), $result['user_full_name']);
    }

    public function testDeleteContributionUserFullName(): void
    {
        $contributionCategory = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->user->id
        ]);
        $contribution = factory('App\Model\Contribution')->create([
            'user_full_name' => $this->user->getFullName(),
            'contribution_category_id' => $contributionCategory->id
        ]);

        $result = $this->contributorService->deleteContribution($contribution->id);
        $result = $result->jsonSerialize();

        $this->assertEquals($contribution->id, $result['id']);
        $this->assertEquals($this->user->getFullName(), $result['user_full_name']);
    }
}
