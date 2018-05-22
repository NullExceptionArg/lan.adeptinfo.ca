<?php

namespace Tests\Unit\Repository\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateContributionUserIdTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionRepository;

    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->contributionRepository = $this->app->make('App\Repositories\Implementation\ContributionRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
    }

    public function testCreateContributionUserId()
    {
        $this->contributionRepository->createContributionUserId($this->user->id);

        $this->seeInDatabase('contribution', [
            'id' => 1,
            'user_full_name' => null,
            'user_id' => $this->user->id
        ]);
    }
}
