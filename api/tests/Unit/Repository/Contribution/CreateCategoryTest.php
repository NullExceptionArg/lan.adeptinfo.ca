<?php

namespace Tests\Unit\Repository\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateCategoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionRepository;

    protected $lan;

    protected $paramsContent = [
        "name" => 'Programmer'
    ];

    public function setUp()
    {
        parent::setUp();
        $this->contributionRepository = $this->app->make('App\Repositories\Implementation\ContributionRepositoryImpl');
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testCreateCategory()
    {
        $this->contributionRepository->createCategory($this->lan, $this->paramsContent['name']);

        $this->seeInDatabase('contribution_category', ['name' => $this->paramsContent['name']]);
    }
}
