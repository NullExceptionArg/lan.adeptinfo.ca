<?php

namespace Tests\Unit\Repository\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class FindCategoryByIdTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionRepository;

    protected $lan;
    protected $category;

    public function setUp(): void
    {
        parent::setUp();
        $this->contributionRepository = $this->app->make('App\Repositories\Implementation\ContributionRepositoryImpl');
        $this->lan = factory('App\Model\Lan')->create();
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id,
        ]);
    }

    public function testFindCategoryById(): void
    {
        $result = $this->contributionRepository->findCategoryById($this->category->id);

        $this->assertEquals($this->category->id, $result->id);
        $this->assertEquals($this->category->name, $result->name);
    }
}
