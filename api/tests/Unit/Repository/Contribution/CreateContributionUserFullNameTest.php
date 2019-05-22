<?php

namespace Tests\Unit\Repository\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateContributionUserFullNameTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionRepository;

    protected $contributionCategory;

    protected $paramsContent = [
        'user_full_name' => 'Karl Marx',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->contributionRepository = $this->app
            ->make('App\Repositories\Implementation\ContributionRepositoryImpl');

        $lan = factory('App\Model\Lan')->create();
        $this->contributionCategory = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id,
        ]);
    }

    public function testCreateContributionUserFullName(): void
    {
        $result = $this->contributionRepository->createContributionUserFullName(
            $this->paramsContent['user_full_name'],
            $this->contributionCategory->id
        );

        $this->assertIsInt($result);
        $this->seeInDatabase('contribution', [
            'id'             => 1,
            'user_full_name' => $this->paramsContent['user_full_name'],
            'user_id'        => null,
        ]);
    }
}
