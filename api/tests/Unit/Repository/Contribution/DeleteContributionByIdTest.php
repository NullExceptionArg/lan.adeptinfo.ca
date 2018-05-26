<?php

namespace Tests\Unit\Repository\Contribution;

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

        $this->contribution = factory('App\Model\Contribution')->create([
            'user_full_name' => 'Karl Marx'
        ]);
    }

    public function testDeleteContribution(): void
    {
        $this->seeInDatabase('contribution', [
            'id' => $this->contribution->id,
            'user_full_name' => $this->contribution->user_full_name,
            'user_id' => null
        ]);

        $this->contributionRepository->deleteContributionById($this->contribution->id);

        $this->notSeeInDatabase('contribution', [
            'id' => $this->contribution->id,
            'user_full_name' => $this->contribution->user_full_name,
            'user_id' => null
        ]);
    }
}
