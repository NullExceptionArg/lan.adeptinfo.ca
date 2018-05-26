<?php

namespace Tests\Unit\Repository\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetCategoriesTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionRepository;

    protected $lan;
    protected $category;

    protected $paramsContent = [
        "name" => 'Programmer'
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->contributionRepository = $this->app->make('App\Repositories\Implementation\ContributionRepositoryImpl');
        $this->lan = factory('App\Model\Lan')->create();
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testGetCategoryForLan(): void
    {
        $result = $this->contributionRepository->getCategories($this->lan);

        $this->assertEquals($this->category->id, $result[0]['id']);
        $this->assertEquals($this->category->name, $result[0]['name']);
    }
}
