<?php

namespace Tests\Unit\Service\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetCategoriesTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionService;

    protected $lan;
    protected $category;

    public function setUp(): void
    {
        parent::setUp();
        $this->contributionService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');
        $this->lan = factory('App\Model\Lan')->create();
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id,
        ]);
    }

    public function testGetCategories(): void
    {
        $result = $this->contributionService->getCategories($this->lan->id);
        $result = $result->jsonSerialize();

        $this->assertEquals($this->category->id, $result[0]['id']);
        $this->assertEquals($this->category->name, $result[0]['name']);
    }
}
