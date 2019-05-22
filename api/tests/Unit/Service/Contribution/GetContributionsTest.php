<?php

namespace Tests\Unit\Service\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetContributionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionService;

    protected $user;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->contributionService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testGetContributions(): void
    {
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id,
        ]);
        $contribution = factory('App\Model\Contribution')->create([
            'user_full_name'           => $this->user->getFullName(),
            'contribution_category_id' => $category->id,
        ]);

        $result = $this->contributionService->getContributions($this->lan->id);
        $this->assertEquals($category->id, $result[0]->id);
        $this->assertEquals($category->name, $result[0]->name);
        $this->assertEquals([
            'id'             => $contribution->id,
            'user_full_name' => $this->user->getFullName(),
        ], collect($result[0]->jsonSerialize()['contributions'][0])->toArray());
    }
}
