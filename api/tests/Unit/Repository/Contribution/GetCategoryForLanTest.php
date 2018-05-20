<?php

namespace Tests\Unit\Repository\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetCategoryForLanTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionRepository;

    protected $paramsContent = [
        "name" => 'Programmer'
    ];

    public function setUp()
    {
        parent::setUp();
        $this->contributionRepository = $this->app->make('App\Repositories\Implementation\ContributionRepositoryImpl');
    }

    public function testGetCategoryForLan()
    {
        $lan = factory('App\Model\Lan')->create();
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);

        $result = $this->contributionRepository->getCategories($lan);

        $this->assertEquals($category->id, $result[0]['id']);
        $this->assertEquals($category->name, $result[0]['name']);
    }
}
