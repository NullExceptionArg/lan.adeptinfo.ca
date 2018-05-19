<?php

namespace Tests\Unit\Repository\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteCategoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionRepository;

    public function setUp()
    {
        parent::setUp();
        $this->contributionRepository = $this->app->make('App\Repositories\Implementation\ContributionRepositoryImpl');
    }

    public function testDeleteCategory()
    {
        $lan = factory('App\Model\Lan')->create();
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);

        $this->seeInDatabase('contribution_category', [
            'id' => $category->id,
            'name' => $category->name
        ]);

        $this->contributionRepository->deleteCategory($category);

        $this->notSeeInDatabase('contribution_category', [
            'id' => $category->id,
            'name' => $category->name
        ]);
    }
}
