<?php

namespace Tests\Unit\Repository\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class FindCategoryByIdTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionRepository;

    public function setUp()
    {
        parent::setUp();
        $this->contributionRepository = $this->app->make('App\Repositories\Implementation\ContributionRepositoryImpl');
    }

    public function testFindCategoryById()
    {
        $lan = factory('App\Model\Lan')->create();
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);

        $result = $this->contributionRepository->findCategoryById($category->id);

        $this->assertEquals($category->id, $result->id);
        $this->assertEquals($category->name, $result->name);
    }
}
