<?php

namespace Tests\Unit\Repository\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class FindContributionByIdTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionRepository;

    protected $contribution;

    public function setUp(): void
    {
        parent::setUp();
        $this->contributionRepository = $this->app->make('App\Repositories\Implementation\ContributionRepositoryImpl');

        $lan = factory('App\Model\Lan')->create();
        $contributionCategory = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id,
        ]);
        $this->contribution = factory('App\Model\Contribution')->create([
            'user_full_name'           => 'Karl Marx',
            'contribution_category_id' => $contributionCategory->id,
        ]);
    }

    public function testFindContributionById(): void
    {
        $result = $this->contributionRepository->findContributionById($this->contribution->id);
        $this->assertEquals($this->contribution->id, $result->id);
    }
}
