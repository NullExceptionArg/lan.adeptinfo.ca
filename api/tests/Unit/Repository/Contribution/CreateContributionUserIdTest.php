<?php

namespace Tests\Unit\Repository\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateContributionUserIdTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionRepository;

    protected $user;
    protected $contributionCategory;

    public function setUp(): void
    {
        parent::setUp();
        $this->contributionRepository = $this->app->make('App\Repositories\Implementation\ContributionRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();
        $this->contributionCategory = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id,
        ]);
    }

    public function testCreateContributionUserId(): void
    {
        $result = $this->contributionRepository->createContributionUserId(
            $this->user->id,
            $this->contributionCategory->id
        );

        $this->assertIsInt($result);
        $this->seeInDatabase('contribution', [
            'id'             => 1,
            'user_full_name' => null,
            'user_id'        => $this->user->id,
        ]);
    }
}
