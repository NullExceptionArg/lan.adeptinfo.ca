<?php

namespace Tests\Unit\Repository\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateContributionUserFullNameTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionRepository;

    protected $paramsContent = [
        "user_full_name" => 'Karl Marx'
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->contributionRepository = $this->app->make('App\Repositories\Implementation\ContributionRepositoryImpl');
    }

    public function testCreateContributionUserFullNameTest(): void
    {
        $this->contributionRepository->createContributionUserFullName($this->paramsContent['user_full_name']);

        $this->seeInDatabase('contribution', [
            'id' => 1,
            'user_full_name' => $this->paramsContent['user_full_name'],
            'user_id' => null
        ]);
    }
}
