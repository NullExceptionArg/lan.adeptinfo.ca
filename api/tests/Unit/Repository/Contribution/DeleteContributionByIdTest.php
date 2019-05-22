<?php

namespace Tests\Unit\Repository\Contribution;

use App\Model\Contribution;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteContributionByIdTest extends TestCase
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

    public function testDeleteContributionById(): void
    {
        $this->seeInDatabase('contribution', [
            'id'             => $this->contribution->id,
            'user_full_name' => $this->contribution->user_full_name,
            'user_id'        => null,
        ]);

        $this->contributionRepository->deleteContributionById($this->contribution->id);

        $contribution = Contribution::withTrashed()->first();
        $this->assertEquals($this->contribution->id, $contribution->id);
    }
}
