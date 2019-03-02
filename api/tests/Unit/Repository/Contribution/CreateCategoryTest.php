<?php

namespace Tests\Unit\Repository\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateCategoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionRepository;

    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->contributionRepository = $this->app->make('App\Repositories\Implementation\ContributionRepositoryImpl');
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testCreateCategory(): void
    {
        $name = 'Programmer';
        $this->contributionRepository->createCategory($this->lan->id, $name);
        $this->seeInDatabase('contribution_category', ['name' => $name]);
    }
}
